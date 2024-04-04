<?php
include_once 'includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate a unique ID
    $stmt = $db->prepare("SELECT MAX(id) AS max_id FROM user_cards");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $row['max_id'] !== null ? $row['max_id'] + 5 : 1;

    // Assuming the form fields are named 'name', 'description', 'atk', 'def', 'level', 'race', and 'image'
    $name = $_POST['name'];
    $description = $_POST['description'];
    $atk = $_POST['atk'];
    $def = $_POST['def'];
    $level = $_POST['level'];
    $race = $_POST['race'];

    // Handle image upload and resizing
    $image = $_FILES['image']['name'];
    $image_temp = $_FILES['image']['tmp_name'];
    $upload_dir = 'uploads'; // Change this to your desired upload directory
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir);
    }
    $target_file = "{$upload_dir}/{$image}";

    // Resize the image
    $resizedImage = resizeImage($image_temp, 200, 200);
    imagejpeg($resizedImage, $target_file);
    imagedestroy($resizedImage);

    // Insert the new card into the database
    $stmt = $db->prepare("INSERT INTO user_cards (id, name, description, atk, def, level, race, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$id, $name, $description, $atk, $def, $level, $race, $target_file])) {
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

function resizeImage($imagePath, $width, $height)
{
    $sourceImage = imagecreatefromjpeg($imagePath);
    $resizedImage = imagecreatetruecolor($width, $height);
    imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $width, $height, imagesx($sourceImage), imagesy($sourceImage));
    imagedestroy($sourceImage);
    return $resizedImage;
}
?>
