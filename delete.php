<?php
include_once 'includes/connect.php';

// Get the posted data
$data = json_decode(file_get_contents("php://input"));

// Check if data is valid
if (!empty($data->table) && !empty($data->id)) {
    $table = $data->table;
    $id = $data->id;

    // Check if the table exists in the database
    $stmt = $db->prepare("SHOW TABLES LIKE :table");
    $stmt->bindParam(':table', $table, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        // Construct the SQL query to delete from the specified table
        $query = "DELETE FROM $table WHERE id = :id";

        try {
            $stmt = $db->prepare($query);
            // Bind the id parameter
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            // Execute the query
            $stmt->execute();
            // Return success message
            echo json_encode(["success" => true, "message" => "Row deleted successfully"]);
        } catch (PDOException $e) {
            // Return error message
            echo json_encode(["success" => false, "message" => $e->getMessage()]);
        }
    } else {
        // Return error message if table does not exist
        echo json_encode(["success" => false, "message" => "Table not found"]);
    }
} else {
    // Return error message if data is invalid
    echo json_encode(["success" => false, "message" => "Invalid data"]);
}
?>
