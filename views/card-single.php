<?php
include 'C:\xampp\htdocs\wd2\project\-YuGiOh-Project\includes\connect.php';

$cardName = isset($_GET['card_name']) ? urldecode($_GET['card_name']) : '';

// Function to output card details
function outputCardDetails($cardName, $cardDescription, $cardAtk, $cardDef, $cardLevel, $cardRace, $cardImage)
{
    echo '<h2>' . $cardName . '</h2>';
    if (!empty($cardImage)) {
        echo '<img style="height:250px;width:172px" src="' . $cardImage . '" alt="' . $cardName . '">';
    } else {
        echo '<p>Card with no image</p>';
    }
    echo '<p><strong>Description:</strong> ' . $cardDescription . '</p>';
    echo '<p><strong>ATK:</strong> ' . $cardAtk . '</p>';
    echo '<p><strong>DEF:</strong> ' . $cardDef . '</p>';
    echo '<p><strong>Level:</strong> ' . $cardLevel . '</p>';
    echo '<p><strong>Type:</strong> ' . $cardRace . '</p>';
    echo '<button id="editCardButton">Edit Card</button>';
    echo '<button id="deleteCardButton">Delete Card</button>';
}

if ($cardName !== '') {
    // Fetch card from the database
    $stmt = $db->prepare("SELECT * FROM cards WHERE name = ?");
    $stmt->execute([$cardName]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$card) {
        $stmt = $db->prepare("SELECT * FROM user_cards WHERE name = ?");
        $stmt->execute([$cardName]);
        $card = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($card) {
        $cardDescription = $card['description'];
        $cardAtk = $card['atk'];
        $cardDef = $card['def'];
        $cardLevel = $card['level'];
        $cardRace = $card['race'];
        $cardImage = $card['image_url'];
        $cardId =  $card['id'];

        // If card details were fetched via search, return JSON
        if (isset($_GET['card_name'])) {
            echo json_encode([
                'name' => $cardName,
                'description' => $cardDescription,
                'atk' => $cardAtk,
                'def' => $cardDef,
                'level' => $cardLevel,
                'race' => $cardRace,
                'image_url' => $cardImage,
                'id' => $cardId,
            ]);
            exit; // Stop further execution
        } else { // If card details were redirected from another page, output HTML
            outputCardDetails($cardName, $cardDescription, $cardAtk, $cardDef, $cardLevel, $cardRace, $cardImage);
        }
    } else {
        echo '<p>No card found with that name</p>';
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
        <div class="card-details" id="cardDetailsContainer"></div>
        <button id="addCardButton">Add Card</button>
        <div id="addCardFormContainer" style="display: none;">
            <h2>Add Card</h2>
            <form id="addCardForm" action="#" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" required><br><br>
                <label for="atk">ATK:</label>
                <input type="number" id="atk" name="atk" required><br><br>
                <label for="def">DEF:</label>
                <input type="number" id="def" name="def" required><br><br>
                <label for="level">Level:</label>
                <input type="number" id="level" name="level" required><br><br>
                <label for="race">Type:</label>
                <input type="text" id="race" name="race" required><br><br>
                <label for="image">Image:</label>
                <input type="file" id="image" name="image" accept="image/*"><br><br>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <script>
        let isAddCardFormVisible = false;
        let cardData;

        document.getElementById('searchForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form submission
            const inputValue = document.getElementById('card_name').value.trim();

            if (inputValue.length === 0) {
                return;
            }

            fetchCardDetails(inputValue);
        });

        function fetchCardDetails(cardName) {
            fetch(`http://localhost/wd2/project/-YuGiOh-Project/views/card-single.php?card_name=${cardName}`)
                .then(response => response.json())
                .then(data => {
                    if (Object.keys(data).length > 0) {
                        const cardDetailsContainer = document.getElementById('cardDetailsContainer');
                        cardData = data;
                        console.log(cardData);
                        let imageHtml = '';
                        if (data.image_url) {
                            imageHtml = `<img style="height:250px;width:172px" src="${data.image_url}" alt="${data.name}">`;
                        } else {
                            imageHtml = '<p>Card with no image</p>';
                        }
                        cardDetailsContainer.innerHTML = `
                    <h2>${data.name}</h2>  
                    ${imageHtml}
                    <p><strong>Description:</strong> ${data.description}</p>
                    <p><strong>ATK:</strong> ${data.atk}</p>
                    <p><strong>DEF:</strong> ${data.def}</p>
                    <p><strong>Level:</strong> ${data.level}</p>
                    <p><strong>Type:</strong> ${data.race}</p>
                    <button id="editCardButton">Edit Card</button>
                    <button id="deleteCardButton">Delete Card</button>
                `;
                    } else {
                        console.error('No card found with that name');
                    }
                })
                .catch(error => {
                    console.error('Error fetching card details:', error);
                });
        }

        document.getElementById('addCardButton').addEventListener('click', function() {
            isAddCardFormVisible = !isAddCardFormVisible;
            document.getElementById('addCardFormContainer').style.display = isAddCardFormVisible ? 'block' : 'none';
        });

        document.addEventListener('click', function(event) {
            const editButton = event.target.closest('#editCardButton');
            if (editButton) {
                const cardDetailsContainer = document.getElementById('cardDetailsContainer');
                cardDetailsContainer.innerHTML = ''; // Clear existing card details
                createEditableInputs(cardData);
            } else {
                const deleteButton = event.target.closest('#deleteCardButton');
                if (deleteButton) {
                    const cardName = cardData.name;
                    const cardId = cardData.id;
                    if (confirm(`Are you sure you want to delete ${cardName}?`)) {
                        // Send a request to delete.php to delete the card
                        fetch('delete.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    table: 'cards',
                                    id: cardId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert(data.message);
                                    location.reload(); // Reload the page
                                } else {
                                    alert(data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error deleting card:', error);
                                alert('An error occurred while deleting the card');
                            });
                    }
                }
            }

            const deleteImageButton = event.target.closest('#deleteImageButton');
            if (deleteImageButton) {
                if (confirm('Are you sure you want to delete the image?')) {
                    // Send a request to delete the image
                    fetch('deleteImage.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                card_id: cardData.id
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                location.reload(); // Reload the page
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting image:', error);
                            alert('An error occurred while deleting the image');
                        });
                }
            }
        });

        function createEditableInputs(data) {
            const cardDetailsContainer = document.getElementById('cardDetailsContainer');
            cardDetailsContainer.innerHTML = ''; // Clear existing card details

            let imageHtml = '';
            if (data.image_url) {
                imageHtml = `<img style="height:250px;width:172px" src="${data.image_url}" alt="${data.name}">`;
            } else {
                imageHtml = '<p>Card with no image</p>';
            }

            cardDetailsContainer.innerHTML = `
        <h2>${data.name}</h2>  
        ${imageHtml}
        <p><strong>Description:</strong> ${data.description}</p>
        <p><strong>ATK:</strong> ${data.atk}</p>
        <p><strong>DEF:</strong> ${data.def}</p>
        <p><strong>Level:</strong> ${data.level}</p>
        <p><strong>Type:</strong> ${data.race}</p>
    `;

            // Add Edit and Delete buttons if they don't already exist
            if (!document.getElementById('editCardButton')) {
                const editButton = document.createElement('button');
                editButton.innerText = 'Edit Card';
                editButton.id = 'editCardButton';
                cardDetailsContainer.appendChild(editButton);
            }

            if (!document.getElementById('deleteCardButton')) {
                const deleteButton = document.createElement('button');
                deleteButton.innerText = 'Delete Card';
                deleteButton.id = 'deleteCardButton';
                cardDetailsContainer.appendChild(deleteButton);
            }
        }
    </script>
</body>

</html>
