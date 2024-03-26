<?php
require_once 'includes/connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /wd2/project/-YuGiOh-Project/login');
    exit;
}

// Set user_id from session
$user_id = $_SESSION['user_id'];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];

    // Check if file is an image
    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
        $filename = basename($file['name']);
        $uploadDir = 'C:/xampp/htdocs/wd2/project/-YuGiOh-Project/views/uploads/';
        $uploadPath = $uploadDir . $filename;

        // Create the uploads directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the file to the uploads directory
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Insert filename into images table
            $stmt = $pdo->prepare("INSERT INTO images (filename, user_id) VALUES (?, ?)");
            $stmt->execute([$filename, $user_id]);

            // Update user profile image
            $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $stmt->execute([$filename, $user_id]);

            // Redirect to profile page
            header('Location: /wd2/project/-YuGiOh-Project/user-profile.php');
            exit();
        } else {
            echo "Failed to upload file.";
        }
    } else {
        echo "File is not an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
</head>

<body>
    <h1>User Profile</h1>
    <p>Upload a new profile image:</p>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*">
        <button type="submit">Upload</button>
    </form>
    <?php
    // Display user's current profile image if exists
    $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && $row['profile_image']) {
        echo "<img src='views/uploads/{$row['profile_image']}' alt='Profile Image'>";
    }
    ?>
</body>

</html>