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
                // Resize the uploaded image
                $resizedImage = resizeImage($uploadPath, 200, 200); // Resize to 200x200

                // Save the resized image
                imagejpeg($resizedImage, $uploadPath);

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

// Function to resize the image using GD library
function resizeImage($imagePath, $width, $height)
{
    $info = getimagesize($imagePath);
    $mime = $info['mime'];

    // Check the MIME type to determine the image format
    switch ($mime) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($imagePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($imagePath);
            break;
        default:
            echo "Unsupported image format. Please upload a JPEG or PNG file.";
            return null;
    }

    $resizedImage = imagecreatetruecolor($width, $height);
    imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $width, $height, imagesx($sourceImage), imagesy($sourceImage));
    imagedestroy($sourceImage);
    return $resizedImage;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f8f8;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-image {
            display: block;
            margin: 0 auto;
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form {
            margin-top: 20px;
            text-align: center;
        }
        label, .upload-txt {
            font-weight: bold;
            text-align: center;
        }
        input[type="file"] {
            display: none;
        }
        .custom-file-upload {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
        }
        .custom-file-upload:hover {
            background-color: #0056b3;
        }
        button[type="submit"] {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: #218838;
        }
        .error-message {
            color: red;
            font-style: italic;
        }
        hr{
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>User Profile</h1>
    <?php if ($currentImage) : ?>
        <img src='views/uploads/<?php echo $currentImage; ?>' alt='Profile Image' class="profile-image"><br>
        <form action="" method="post">
            <label for="delete_image">Delete Profile Image:</label>
            <input type="checkbox" name="delete_image" id="delete_image">
            <input type="hidden" name="current_image" value="<?php echo $currentImage; ?>">
            <button type="submit">Delete</button>
        </form>
        <hr/>
    <?php endif; ?>
  
    <p class="upload-txt">Upload a new profile image:</p>
    <form action="" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
        <label for="image" class="custom-file-upload">Choose File</label>
        <input type="file" name="image" id="image" accept="image/*" onchange="updateFileName()">
        <span id="file-name"></span>
        <button type="submit">Upload</button>
        <p id="error-message" class="error-message"></p>
    </form>

    <script>
        function validateForm() {
            var fileInput = document.getElementById('image');
            var errorMessage = document.getElementById('error-message');
            if (fileInput.files.length === 0) {
                errorMessage.innerText = "Please select a file.";
                return false;
            }
            return true;
        }

        function updateFileName() {
            var fileInput = document.getElementById('image');
            var fileNameSpan = document.getElementById('file-name');
            if (fileInput.files.length > 0) {
                fileNameSpan.innerText = "Selected file: " + fileInput.files[0].name;
            } else {
                fileNameSpan.innerText = "";
            }
        }
    </script>
</body>
</html>
