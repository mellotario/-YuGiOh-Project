<?php
class CardController {
    public function list() {
        // Logic to fetch and display card list
        include 'views/header/header.php';
        include 'views/card-list.php';
        include 'views/footer.php';
    }

    public function single($id) {
        // Logic to fetch and display single card
        include 'views/header/header.php';
        include 'views/card-single.php';
        include 'views/footer.php';
    }
}
?>
