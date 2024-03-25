<?php
class PageListController
{
    public function index()
    {
        // Include the view file to display the page list
        include 'views/header/header.php';
        include 'views/page-list.php';
        include 'views/footer.php';
    }
}

?>
