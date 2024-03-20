<?php
class CardController {
    public function list() {
        // Logic to fetch and display card list
        include 'views/header.php';
        include 'views/card-list.php';
        include 'views/footer.php';
    }

    public function show($id) {
        // Logic to fetch and display single card
        include 'views/header.php';
        include 'views/card-single.php';
        include 'views/footer.php';
    }
}
?>
