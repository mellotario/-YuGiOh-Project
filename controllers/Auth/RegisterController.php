<?php
class RegisterController {
    public function showRegisterForm() {
        // Logic to display registration form
        include 'views/header/header.php';
        include 'views/auth/register.php';
        include 'views/footer.php';
    }

    public function register() {
        // Logic to handle registration form submission
    }
}
?>
