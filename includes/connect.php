<?php
// Database configuration
$host = 'localhost';
$dbname = 'yugioh';
$username = 'deckuser';
$password = 'magonegro';

// Create a PDO database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
$db = $pdo; // Set $db to $pdo for consistency
?>
