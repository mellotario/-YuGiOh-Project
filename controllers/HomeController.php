<?php
class HomeController {
    public function index() {
        // Logic for the home page
        include 'views/header/header.php';
        include 'views/home.php';
        include 'views/footer.php';
    }
}
?>
