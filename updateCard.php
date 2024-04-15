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

if ($cardName !== '') {
    // Update the card details in the database
    $stmt = $db->prepare("UPDATE cards SET description = ?, atk = ?, def = ?, level = ?, race = ?, type = ? WHERE name = ?");
    $stmt->execute([$description, $atk, $def, $level, $race, $type, $cardName]);

    // Check if the update was successful
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
?>
