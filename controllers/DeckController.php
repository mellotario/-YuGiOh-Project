<?php
class DeckController {
    public function list() {
        // Logic to fetch and display deck list
        include 'views/header/header.php';
        include 'views/deck-list.php';
        include 'views/footer.php';
    }

    public function single($id) {
        // Logic to fetch and display single deck
        include 'views/header/header.php';
        include 'views/deck-single.php';
        include 'views/footer.php';
    }
}
?>
