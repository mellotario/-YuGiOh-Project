<?php
include 'C:\xampp\htdocs\wd2\project\-YuGiOh-Project\includes\connect.php';

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name'; // Default sorting by name
$order = isset($_GET['order']) ? $_GET['order'] : 'asc'; // Default order ascending

try {
    // Validate sort column to prevent SQL injection
    $validColumns = ['name', 'atk', 'def'];
    if (!in_array($sort, $validColumns)) {
        $sort = 'name'; // Default to name if an invalid column is provided
    }

    // Fetch cards from the database
    $stmt = $db->prepare("SELECT id, name, description, atk, def, level, race, image_url FROM user_cards 
     UNION 
     SELECT id, name, description, atk, def, level, race, image_url FROM cards
     ORDER BY $sort $order"); // Sort the cards
    $stmt->execute();
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display the card information
    if (!empty($cards)) {
        // Display sorting options and indication of current sorting type using JavaScript
        echo '<div>';
        echo '<h3>Sort by:</h3>';
        echo '<ul id="sorting-options">';
        echo '</ul>';
        echo '<p id="current-sorting">Currently sorting by: ' . ucfirst($sort) . ' (' . ucfirst($order) . ')</p>';
        echo '</div>';

        echo '<div id="card-container" style="display: grid; grid-template-columns: auto auto auto auto; gap: 20px;">';
        // Output card data
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

<script>
    // Define the sortAndRenderCards function in the global scope
    function sortAndRenderCards(sortBy, order) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "views/card-list.php?sort=" + sortBy + "&order=" + order, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById("card-container").innerHTML = xhr.responseText;
                updateSortingOptions(sortBy, order);
            }
        };
        xhr.send();
    }



    function updateSortingOptions(sortBy, order) {
        const sortingOptions = document.getElementById('sorting-options');
        sortingOptions.innerHTML = `
            <li><a href="#" onclick="sortAndRenderCards('name', 'asc')">Name (A-Z)</a></li>
            <li><a href="#" onclick="sortAndRenderCards('name', 'desc')">Name (Z-A)</a></li>
            <li><a href="#" onclick="sortAndRenderCards('atk', 'asc')">ATK (Low to High)</a></li>
            <li><a href="#" onclick="sortAndRenderCards('atk', 'desc')">ATK (High to Low)</a></li>
            <li><a href="#" onclick="sortAndRenderCards('def', 'asc')">DEF (Low to High)</a></li>
            <li><a href="#" onclick="sortAndRenderCards('def', 'desc')">DEF (High to Low)</a></li>
        `;
        document.getElementById('current-sorting').textContent = `Currently sorting by: ${sortBy} (${order})`;
    }

    document.addEventListener('DOMContentLoaded', function() {

        // Initial load of the page with default sorting
        sortAndRenderCards('name', 'asc');

        // Update sorting options and current sorting indication
        updateSortingOptions('name', 'asc');
    });
</script>