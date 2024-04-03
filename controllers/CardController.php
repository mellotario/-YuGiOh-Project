<?php
class CardController {
    public function list() {
        include 'views/header/header.php';
        include 'views/card-list.php';
        include 'views/footer.php';
    }

    public function single() {
        include 'views/header/header.php';
        include 'views/card-single.php';
        include 'views/footer.php';
    }
}
?>
