<?php
class AdminController  {
    public function index($db) {
        // Logic to display admin page
        include 'views/header/header.php';
        include 'views/auth/admin_page.php';
        include 'views/footer.php';
    }
}
?>

