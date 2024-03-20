<?php
// Include the database connection file
include_once 'includes/connect.php';

// Get the POST data
$data = json_decode(file_get_contents("php://input"), true);

// Extract the table name, ID, and values from the data
$table = $data['table'];
$id = (int) $data['id']; // Ensure that id is treated as an integer
$values = $data['values'];

// Prepare the update query
$query = "UPDATE $table SET ";
$setValues = [];
foreach ($values as $key => $value) {
    $setValues[] = "$key = :$key";
}
$query .= implode(", ", $setValues);
$query .= " WHERE id = :id";

// Begin a transaction
$db->beginTransaction();

try {
    // Prepare and execute the statement
    $stmt = $db->prepare($query);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT); // Bind id as an integer
    foreach ($values as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $result = $stmt->execute();

    // Commit the transaction if update is successful
    if ($result) {
        $db->commit();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update value']);
    }
} catch (PDOException $e) {
    // Roll back the transaction on error
    $db->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
