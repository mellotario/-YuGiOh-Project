<?php
include_once 'includes/connect.php';
include_once 'includes/functions.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Decode the JSON data from the request body
    $data = json_decode(file_get_contents("php://input"));

    // Check if the 'card_id' is present in the JSON data
    if (isset($data->card_id)) {
        $cardId = validate_numeric($data->card_id);

        // Fetch the card details from the user_cards table
        $stmt = $db->prepare("SELECT * FROM user_cards WHERE id = ?");
        $stmt->execute([$cardId]);
        $card = $stmt->fetch(PDO::FETCH_ASSOC);

        // If card not found in user_cards, check the cards table
        if (!$card) {
            $stmt = $db->prepare("SELECT * FROM cards WHERE id = ?");
            $stmt->execute([$cardId]);
            $card = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Check if the card exists
        if ($card) {
            // Delete the image file from the file system
            $imagePath = $card['image_url'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Update the card record in the database to remove the image URL
            if (isset($card['user_id'])) {
                $stmt = $db->prepare("UPDATE user_cards SET image_url = NULL WHERE id = ?");
            } else {
                $stmt = $db->prepare("UPDATE cards SET image_url = NULL WHERE id = ?");
            }
            $stmt->execute([$cardId]);

            // Return success response
            echo json_encode(["success" => true, "message" => "Image deleted successfully"]);
            exit;
        } else {
            echo json_encode(["success" => false, "message" => "Card not found"]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request"]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit;
}
?>
