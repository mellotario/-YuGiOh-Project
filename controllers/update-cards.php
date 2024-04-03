<?php
include 'C:\xampp\htdocs\wd2\project\-YuGiOh-Project\includes\connect.php';


$url = 'https://db.ygoprodeck.com/api/v7/cardinfo.php?misc=yes'; // API endpoint for 200 cards

$response = @file_get_contents($url); // Suppress warnings and errors
if ($response === false) {
    echo json_encode(["error" => "Failed to fetch cards from API"]);
} else {
    $cards = json_decode($response, true); // Decode the JSON response

    if ($cards && isset($cards['data'])) {
        foreach ($cards['data'] as $card) {
            // Generate a unique identifier for the card based on its name
            $id = md5($card['name']);

            // Extract relevant information from each card
            $name = $card['name'];
            $description = $card['desc'];
            $image_url = isset($card['card_images'][0]['image_url']) ? $card['card_images'][0]['image_url'] : null;
            $atk = isset($card['atk']) ? $card['atk'] : null;
            $def = isset($card['def']) ? $card['def'] : null;
            $level = isset($card['level']) ? $card['level'] : null;
            $race = isset($card['race']) ? $card['race'] : null;
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

// Insert the card into the database if it doesn't already exist
$stmt = $db->prepare("INSERT INTO cards (id, name, description, image_url, atk, def, level, race, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), image_url = VALUES(image_url), atk = VALUES(atk), def = VALUES(def), level = VALUES(level), race = VALUES(race), updated_at = VALUES(updated_at)");
$stmt->execute([$id, $name, $description, $image_url, $atk, $def, $level, $race, $created_at, $updated_at]);

        }

        echo json_encode(["message" => "Cards updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to parse cards data"]);
    }
}
?>
