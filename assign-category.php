<?php
include_once 'includes/connect.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $categoryId = $data['categoryId'];
    $cardId = $data['cardId'];
    // Validate input
    if (!empty($categoryId) && !empty($cardId)) {
        $stmt = $db->prepare("UPDATE cards SET type = ? WHERE id = ?");
        $stmt->execute([$categoryId, $cardId]);
        echo json_encode(['message' => 'Category assigned successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid data']);
    }
}
