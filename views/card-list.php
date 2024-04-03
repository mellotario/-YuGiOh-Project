<?php
require 'includes/connect.php';

try {
    $stmt = $db->query("SELECT * FROM cards LIMIT 100");
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display the card information
    if (!empty($cards)) {
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">';
        foreach ($cards as $card) {
            echo '<div style="border: 1px solid #ccc; padding: 10px;">';
            echo '<h2>' . $card['name'] . '</h2>';
            echo '<p><strong>Type:</strong> ' . $card['type'] . '</p>';
            echo '<p><strong>Description:</strong> ' . $card['description'] . '</p>';
            echo '<p><strong>ATK:</strong> ' . $card['atk'] . '</p>';
            echo '<p><strong>DEF:</strong> ' . $card['def'] . '</p>';
            echo '<p><strong>Level:</strong> ' . $card['level'] . '</p>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo 'No cards found.';
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>