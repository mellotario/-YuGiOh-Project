<?php
class ForgotPasswordController {
    public function showForgotPasswordForm() {
        // Logic to display forgot password form
        include 'views/header.php';
        include 'views/auth/forgot-password.php';
        include 'views/footer.php';
    }

    public function sendPasswordResetLink() {
        // Logic to handle sending password reset link
    }
}
?>
