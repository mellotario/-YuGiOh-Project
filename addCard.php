<?php
include_once 'includes/connect.php';
include_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $name = validate_input($_POST['name']);
    $description = validate_input($_POST['description']);
    $atk = validate_numeric($_POST['atk']);
    $def = validate_numeric($_POST['def']);
    $level = validate_numeric($_POST['level']);
    $race = validate_input($_POST['race']);

    // Validate image upload
    $image = isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK ? $_FILES['image']['name'] : null;
    $image_temp = isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK ? $_FILES['image']['tmp_name'] : null;
    if ($image && $image_temp) {
        $image = validate_image($image_temp, 200, 200);
    }

    // Insert the new card into the database
    $stmt = $db->prepare("INSERT INTO user_cards (name, description, atk, def, level, race, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $description, $atk, $def, $level, $race, $image])) {
        // Return a success message
        http_response_code(200);
        echo json_encode(['message' => 'Card added successfully']);
    } else {
        // Return an error message
        http_response_code(500);
        echo json_encode(['message' => 'Error adding card']);
    }
} else {
    // Return a method not allowed error
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
}
?>

