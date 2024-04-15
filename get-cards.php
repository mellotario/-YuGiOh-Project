<?php
include_once 'includes/connect.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['category'])) {
    $category = $_GET['category'];
    $stmt = $db->prepare("SELECT * FROM cards WHERE type = ?");
    $stmt->execute([$category]);
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cards);
}
