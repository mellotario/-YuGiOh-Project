<?php
// Include the database connection file
include_once 'includes/connect.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted data
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract the ID and new data from the request
    $id = $data['id'];
    $newData = $data['newData'];

    // Prepare and execute the update query
    $query = "UPDATE users SET ";
    $params = [];
    foreach ($newData as $key => $value) {
        $query .= "$key = ?, ";
        $params[] = $value;
    }
    // Remove the trailing comma and space
    $query = rtrim($query, ', ');
    $query .= " WHERE id = ?";
    $params[] = $id;

    $stmt = $db->prepare($query);
    $stmt->execute($params);

    // Check if the update was successful
    if ($stmt->rowCount() > 0) {
        // Return a success message
        http_response_code(200);
        echo json_encode(['message' => 'User updated successfully']);
    } else {
        // Return an error message
        http_response_code(500);
        echo json_encode(['message' => 'Error updating user']);
    }
} else {
    // Return an error if the request method is not POST
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
}
?>
