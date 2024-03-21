<?php
// Include the database connection file
include_once 'includes/connect.php';

// Check if the POST data exists
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    var_dump($_POST); // Debugging statement
    
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert the user into the database
    $stmt = $db->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $passwordHash])) {
        // Return a success message
        http_response_code(200);
        echo json_encode(['message' => 'User added successfully']);
    } else {
        // Return an error message
        http_response_code(500);
        echo json_encode(['message' => 'Error adding user']);
    }
} else {
    // Return a method not allowed error
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
}
?>
