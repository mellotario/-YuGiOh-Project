<?php
// Start or resume the session

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Generate a random CAPTCHA code
function generateCaptcha() {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $length = 6;
    $captcha = '';
    for ($i = 0; $i < $length; $i++) {
        $captcha .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $captcha;
}

// Generate a new CAPTCHA code if it doesn't exist in the session
if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = generateCaptcha();
}

// Set the content type header
header('Content-type: image/png');

// Create a blank image with a white background
$image = imagecreatetruecolor(100, 30);
$bgColor = imagecolorallocate($image, 255, 255, 255);
imagefill($image, 0, 0, $bgColor);

// Set the text color to black
$textColor = imagecolorallocate($image, 0, 0, 0);

// Draw the CAPTCHA code on the image
imagestring($image, 5, 5, 5, $_SESSION['captcha'], $textColor);

// Output the image as PNG
imagepng($image);

// Free up memory
imagedestroy($image);
?>
