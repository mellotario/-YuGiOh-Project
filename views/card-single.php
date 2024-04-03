<?php
include 'C:\xampp\htdocs\wd2\project\-YuGiOh-Project\includes\connect.php';

$cardName = '';
$cardDescription = '';
$cardAtk = '';
$cardDef = '';
$cardLevel = '';
$cardRace = '';
if (isset($_GET['card_name']) && $_GET['card_name'] !== '') {
    $cardName = $_GET['card_name'];

    // Fetch card from the database
    $stmt = $db->prepare("SELECT * FROM cards WHERE name = ?");
    $stmt->execute([$cardName]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($card) {
        $cardDescription = $card['description'];
        $cardAtk = $card['atk'];
        $cardDef = $card['def'];
        $cardLevel = $card['level'];
        $cardRace = $card['race'];
        $cardImage = $card['image_url'];

        // Return card details as JSON
        echo json_encode([
            'name' => $cardName,
            'description' => $cardDescription,
            'atk' => $cardAtk,
            'def' => $cardDef,
            'level' => $cardLevel,
            'race' => $cardRace,
            'image_url' => $cardImage
        ]);
        exit; // Stop further execution
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
        <div class="card-details" id="cardDetailsContainer"></div>
    </div>

    <script>
        document.getElementById('searchForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form submission
            const inputValue = document.getElementById('card_name').value.trim();

            if (inputValue.length === 0) {
                return;
            }

            fetch(`http://localhost/wd2/project/-YuGiOh-Project/views/card-single.php?card_name=${inputValue}`)
                .then(response => response.json())
                .then(data => {
                    if (Object.keys(data).length > 0) {
                        const cardDetailsContainer = document.getElementById('cardDetailsContainer');
                        cardDetailsContainer.innerHTML = `
                            <h2>${data.name}</h2>  
                            <img style="height:250px" src="${data.image_url}" alt="${data.name}">
                            <p><strong>Description:</strong> ${data.description}</p>
                            <p><strong>ATK:</strong> ${data.atk}</p>
                            <p><strong>DEF:</strong> ${data.def}</p>
                            <p><strong>Level:</strong> ${data.level}</p>
                            <p><strong>Race:</strong> ${data.race}</p>
                        `;
                    } else {
                        console.error('No card found with that name');
                    }
                })
                .catch(error => {
                    console.error('Error fetching card details:', error);
                });
        });
    </script>
</body>

</html>
