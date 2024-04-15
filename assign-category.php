<?php
include_once 'includes/connect.php'; // Include database connection

// Check if the request is a POST request and if the assignCategory parameter is set
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $categoryId = $data['categoryId'];
    $cardId = $data['cardId'];

    // Validate input
    if (!empty($categoryId) && !empty($cardId)) {
        $stmt = $db->prepare("UPDATE cards SET type = ? WHERE id = ?");
        $stmt->execute([$categoryId, $cardId]);
        $response = ['status' => 'success', 'message' => 'Category assigned successfully'];
    } else {
        $response = ['status' => 'error', 'message' => 'Invalid category or card'];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request'];
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
