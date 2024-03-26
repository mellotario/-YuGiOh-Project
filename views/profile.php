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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if delete checkbox is checked
    if (isset($_POST['delete_image']) && $_POST['delete_image'] === 'on') {
        // Remove image from database
        $stmt = $pdo->prepare("UPDATE users SET profile_image = NULL WHERE id = ?");
        $stmt->execute([$user_id]);

        // Remove image file
        $imageFilename = $_POST['current_image'];
        $imagePath = 'C:/xampp/htdocs/wd2/project/-YuGiOh-Project/views/uploads/' . $imageFilename;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Redirect to profile page
        header('Location: /wd2/project/-YuGiOh-Project/profile');
        exit();
    } elseif (isset($_FILES['image'])) {
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
                header('Location: /wd2/project/-YuGiOh-Project/profile');
                exit();
            } else {
                echo "Failed to upload file.";
            }
        } else {
            echo "File is not an image.";
        }
    }
}

// Fetch user's current profile image
$stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$currentImage = ($row && $row['profile_image']) ? $row['profile_image'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <script>
        function validateForm() {
            const fileInput = document.querySelector('input[type="file"]');
            if (fileInput.files.length === 0) {
                alert('Please select a file.');
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <h1>User Profile</h1>
    <?php if ($currentImage) : ?>
        <img src='views/uploads/<?php echo $currentImage; ?>' alt='Profile Image'><br>
        <form action="" method="post">
            <label for="delete_image">Delete Profile Image:</label>
            <input type="checkbox" name="delete_image" id="delete_image">
            <input type="hidden" name="current_image" value="<?php echo $currentImage; ?>">
            <button type="submit">Delete</button>
        </form>
    <?php endif; ?>
    <p>Upload a new profile image:</p>
    <form action="" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
        <input type="file" name="image" accept="image/*">
        <button type="submit">Upload</button>
    </form>
</body>

</html>