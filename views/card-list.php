<?php
require 'includes/connect.php';

try {
    // Fetch cards from the user_cards table
    $stmt = $db->query("SELECT id, name, description, atk, def, level, race, image_url FROM user_cards 
                        UNION 
                        SELECT id, name, description, atk, def, level, race, image_url FROM cards");
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display the card information
    if (!empty($cards)) {
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">';

        $counter = 0;
        foreach ($cards as $card) {
            if ($counter < 30) {
                echo '<div style="border: 1px solid #ccc; padding: 10px;">';
                // Wrap the image in a link to card-single.php with the card name as a parameter
                echo '<a href="views/card-single.php?card_name=' . urlencode($card['name']) . '">';
                echo '<img style="height:250px;width:172px" src="' . $card['image_url'] . '" alt="' . $card['name'] . '">';
                echo '</a>';
                echo '<h2>' . $card['name'] . '</h2>';
                echo '<p><strong>Description:</strong> ' . $card['description'] . '</p>';
                echo '<p><strong>ATK:</strong> ' . $card['atk'] . '</p>';
                echo '<p><strong>DEF:</strong> ' . $card['def'] . '</p>';
                echo '<p><strong>Level:</strong> ' . $card['level'] . '</p>';
                echo '</div>';

                $counter++;
            } else {
                break; // Exit the loop once 30 cards have been displayed
            }
        }

        echo '</div>';
    } else {
        echo 'No cards found.';
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>