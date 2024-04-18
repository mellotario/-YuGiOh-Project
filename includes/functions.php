<?php

// Function to sanitize input datafunction validate_input($input)
function validate_input($input)
{
    return htmlspecialchars(trim($input));
}

function validate_numeric($input)
{
    return filter_var($input, FILTER_VALIDATE_INT);
}

function validate_image($image_temp, $width, $height)
{
    $sourceImage = imagecreatefromjpeg($image_temp);
    $resizedImage = imagecreatetruecolor($width, $height);
    imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $width, $height, imagesx($sourceImage), imagesy($sourceImage));
    imagedestroy($sourceImage);
    $target_file = "uploads/" . uniqid() . ".jpg";
    imagejpeg($resizedImage, $target_file);
    imagedestroy($resizedImage);
    return $target_file;
}

// Function to generate a random string
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, $characters_length - 1)];
    }
    return $random_string;
}


?>
