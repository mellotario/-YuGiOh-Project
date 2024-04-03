<?php
include './includes/connect.php';

// Check if user is logged in (for demonstration purposes)
$isLoggedIn = true; // Assume user is logged in

$cardName = '';
$cardDescription = '';
$cardAtk = '';
$cardDef = '';
$cardLevel = '';
$cardRace = '';

if (isset($_GET['card_name']) && $_GET['card_name'] !== '') {
    $cardName = $_GET['card_name'];

    // Fetch card names from the database for autocomplete
    $stmt = $db->prepare("SELECT name FROM cards WHERE name LIKE CONCAT('%', ?, '%')");
    $stmt->execute([$cardName]);
    $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $cardNames = [];
    foreach ($results as $result) {
        $cardNames[] = $result;
    }

    if (!empty($cardNames)) {
        echo json_encode($cardNames);
        exit;
    }

    $apiUrl = 'https://db.ygoprodeck.com/api/v7/cardinfo.php?name=' . urlencode($cardName);
    $response = file_get_contents($apiUrl);
    $data = json_decode($response, true);

    if (!empty($data['data'])) {
        $card = $data['data'][0];
        $cardName = $card['name'];
        $cardDescription = $card['desc'];
        $cardAtk = $card['atk'];
        $cardDef = $card['def'];
        $cardLevel = $card['level'];
        $cardRace = $card['race'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./assets/styles.css">
</head>

<body>
    <div class="container">
        <h1>Card Details</h1>
        <div class="search">
            <form id="searchForm" action="" method="GET">
                <input type="text" name="card_name" id="card_name" placeholder="Enter card name">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div id="autocomplete"></div>
    </div>

    <script>
        const cardNameInput = document.getElementById('card_name');
        const autocompleteContainer = document.getElementById('autocomplete');

        cardNameInput.addEventListener('input', function(event) {
            const inputValue = event.target.value.trim();

            if (inputValue.length === 0) {
                autocompleteContainer.innerHTML = '';
                return;
            }

            fetch(`./card-single.php?card_name=${inputValue}`)
                .then(response => response.json())
                .then(data => {
                    showAutocompleteOptions(data);
                })
                .catch(error => {
                    console.error('Error fetching autocomplete options:', error);
                });
        });

        function showAutocompleteOptions(options) {
            autocompleteContainer.innerHTML = '';

            if (options.length === 0) {
                return;
            }

            const autocompleteList = document.createElement('ul');
            autocompleteList.classList.add('autocomplete-list');

            options.forEach(option => {
                const listItem = document.createElement('li');
                listItem.textContent = option.name;
                listItem.addEventListener('click', function() {
                    cardNameInput.value = option.name;
                    autocompleteContainer.innerHTML = '';
                });
                autocompleteList.appendChild(listItem);
            });

            autocompleteContainer.appendChild(autocompleteList);
        }

        document.getElementById('searchForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form submission
            const inputValue = cardNameInput.value.trim();

            if (inputValue.length === 0) {
                return;
            }

            fetch(`https://db.ygoprodeck.com/api/v7/cardinfo.php?name=${inputValue}`)
                .then(response => response.json())
                .then(data => {
                    // Handle the response
                })
                .catch(error => {
                    console.error('Error fetching card details:', error);
                });
        });
    </script>
</body>

</html>
