<?php
include_once 'includes/connect.php';

// Get the card name and updated details from the POST request
$cardName = isset($_POST['name']) ? $_POST['name'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$atk = isset($_POST['atk']) ? $_POST['atk'] : '';
$def = isset($_POST['def']) ? $_POST['def'] : '';
$level = isset($_POST['level']) ? $_POST['level'] : '';
$race = isset($_POST['race']) ? $_POST['race'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';

// Validate and sanitize input
$cardName = validate_input($cardName);
$description = validate_input($description);
$atk = validate_numeric($atk);
$def = validate_numeric($def);
$level = validate_numeric($level);
$race = validate_input($race);
$type = validate_input($type);

if ($cardName !== '') {
    // Check if an image was uploaded
    if (isset($_FILES['editImage']) && $_FILES['editImage']['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['editImage']['tmp_name'];
        $imagePath = validate_image($imageTmpName, 200, 200);

        // Update the card details in the cards table
        $stmt = $db->prepare("UPDATE cards SET description = ?, atk = ?, def = ?, level = ?, race = ?, type = ?, image_url = ? WHERE name = ?");
        $stmt->execute([$description, $atk, $def, $level, $race, $type, $imagePath, $cardName]);

        // Update the card details in the user_cards table if the card exists
        $stmt = $db->prepare("UPDATE user_cards SET description = ?, atk = ?, def = ?, level = ?, race = ?, type = ?, image_url = ? WHERE name = ?");
        $stmt->execute([$description, $atk, $def, $level, $race, $type, $imagePath, $cardName]);
    } else {
        // Update the card details in the cards table
        $stmt = $db->prepare("UPDATE cards SET description = ?, atk = ?, def = ?, level = ?, race = ?, type = ? WHERE name = ?");
        $stmt->execute([$description, $atk, $def, $level, $race, $type, $cardName]);

        // Update the card details in the user_cards table if the card exists
        $stmt = $db->prepare("UPDATE user_cards SET description = ?, atk = ?, def = ?, level = ?, race = ?, type = ? WHERE name = ?");
        $stmt->execute([$description, $atk, $def, $level, $race, $type, $cardName]);
    }

    // Check if any update was successful
    if ($stmt->rowCount() > 0) {
        // Return a success message
        echo json_encode(['status' => 'success']);
    } else {
        // Return an error message
        echo json_encode(['status' => 'error', 'message' => 'Failed to update card']);
    }
} else {
    // Return an error message
    echo json_encode(['status' => 'error', 'message' => 'Invalid card name']);
}
