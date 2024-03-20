<?php
require 'includes/connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Yu-Gi-Oh! Card Manager</title>
<link rel="stylesheet" type="text/css" href="./assets/styles.css">

<body>
    
    <main>
        <section class="hero">
            <div class="container">
                <h1>Welcome to My Yu-Gi-Oh! Card Collection</h1>
                <p>Explore the world of Yu-Gi-Oh! with our extensive collection of cards and decks.</p>
                <a href="/card-list" class="btn btn-primary">View All Cards</a>
                <button id="update-cards-btn" class="btn btn-secondary">Update Cards</button>
                <p id="update-message"></p>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <h2>Features</h2>
                <div class="feature">
                    <i class="fas fa-search"></i>
                    <h3>Search Cards</h3>
                    <p>Search for specific cards by name, category, or keyword.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-comments"></i>
                    <h3>Comment on Cards</h3>
                    <p>Share your thoughts and strategies by commenting on cards.</p>
                </div>
                <div class="feature">
                    <i class="fas fa-layer-group"></i>
                    <h3>Build Decks</h3>
                    <p>Create and manage your decks with our deck builder feature.</p>
                </div>
            </div>
        </section>

        <section class="latest-cards">
            <div class="container">
                <h2>Latest Cards</h2>
                <div class="card-list">
                    <?php
                    $api_url = 'https://db.ygoprodeck.com/api/v7/cardinfo.php';
                    $response = file_get_contents($api_url);
                    $data = json_decode($response, true);

                    $counter = 0;
                    foreach ($data['data'] as $card) {
                        if ($counter < 9) {
                            echo '<div class="card">';
                            echo '<img src="' . $card['card_images'][0]['image_url'] . '" alt="' . $card['name'] . '">';
                            echo '<h3>' . $card['name'] . '</h3>';
                            echo '<p>' . $card['desc'] . '</p>';
                            echo '</div>';
                            $counter++;
                        } else {
                            break;
                        }
                    }
                    ?>
                </div>
            </div>
        </section>

        <section class="card-count">
            <div class="container">
                <h2>Card Count</h2>
                <?php
                // Get the card count from the database
                $stmt = $db->prepare("SELECT COUNT(*) as card_count FROM cards");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $card_count = $result['card_count'];

                echo '<p>There are currently ' . $card_count . ' cards in the database.</p>';
                ?>
            </div>
        </section>
    </main>

    <script>
        document.getElementById('update-cards-btn').addEventListener('click', function() {
            fetch('./controllers/update-cards.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('update-message').textContent = 'Updated ' + data.updatedCount + ' cards.';
                });
        });
    </script>
</body>
</html>
