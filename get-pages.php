<?php
// Include the database connection file
include 'includes/connect.php';

// Default sorting column and order
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'title';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

// Validate sort column to prevent SQL injection
$valid_columns = ['title', 'created_at', 'updated_at'];
if (!in_array($sort_column, $valid_columns)) {
    $sort_column = 'title'; // Default to title if an invalid column is provided
}

// Query to fetch pages sorted by the selected column and order
$stmt = $db->prepare("SELECT * FROM pages ORDER BY $sort_column $sort_order");
$stmt->execute();
$pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output the list of pages as JSON
header('Content-Type: application/json');
echo json_encode($pages);
?>
