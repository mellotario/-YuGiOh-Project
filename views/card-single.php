<?php
include 'C:\xampp\htdocs\wd2\project\-YuGiOh-Project\includes\connect.php';

$cardName = isset($_GET['card_name']) ? urldecode($_GET['card_name']) : '';

// Function to output card details
function outputCardDetails($cardName, $cardDescription, $cardAtk, $cardDef, $cardLevel, $cardRace, $cardImage)
{
    echo '<h2>' . $cardName . '</h2>';
    echo '<img style="height:250px;width:172px" src="' . $cardImage . '" alt="' . $cardName . '">';
    echo '<p><strong>Description:</strong> ' . $cardDescription . '</p>';
    echo '<p><strong>ATK:</strong> ' . $cardAtk . '</p>';
    echo '<p><strong>DEF:</strong> ' . $cardDef . '</p>';
    echo '<p><strong>Level:</strong> ' . $cardLevel . '</p>';
    echo '<p><strong>Race:</strong> ' . $cardRace . '</p>';
}

if ($cardName !== '') {
    // Fetch card from the database
    $stmt = $db->prepare("SELECT * FROM cards WHERE name = ?");
    $stmt->execute([$cardName]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$card){
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

        // If card details were fetched via search, return JSON
        if (isset($_GET['card_name'])) {
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
                <label for="race">Race:</label>
                <input type="text" id="race" name="race" required><br><br>
                <label for="image">Image:</label>
                <input type="file" id="image" name="image" accept="image/*" required><br><br>
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

            fetch(`http://localhost/wd2/project/-YuGiOh-Project/views/card-single.php?card_name=${inputValue}`)
                .then(response => response.json())
                .then(data => {
                    if (Object.keys(data).length > 0) {
                        const cardDetailsContainer = document.getElementById('cardDetailsContainer');
                        cardData = data;
                        cardDetailsContainer.innerHTML = `
                    <h2>${data.name}</h2>  
                    <img style="height:250px;width:172px" src="${data.image_url}" alt="${data.name}">
                    <p><strong>Description:</strong> ${data.description}</p>
                    <p><strong>ATK:</strong> ${data.atk}</p>
                    <p><strong>DEF:</strong> ${data.def}</p>
                    <p><strong>Level:</strong> ${data.level}</p>
                    <p><strong>Race:</strong> ${data.race}</p>
                    <button id="editCardButton">Edit Card</button>
                `;
                    } else {
                        console.error('No card found with that name');
                    }
                })
                .catch(error => {
                    console.error('Error fetching card details:', error);
                });
        });

        document.getElementById('addCardButton').addEventListener('click', function() {
            isAddCardFormVisible = !isAddCardFormVisible;
            document.getElementById('addCardFormContainer').style.display = isAddCardFormVisible ? 'block' : 'none';
        });

        document.getElementById('addCardForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form submission

            const form = document.getElementById('addCardForm');
            const formData = new FormData(form);

            // Use AJAX to submit the form
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'addCard.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        alert('Card added successfully');
                        location.reload();
                    } else {
                        console.error('Error adding card:', xhr.statusText);
                    }
                }
            };
            xhr.send(formData);
        });

        document.addEventListener('click', function(event) {
            const editButton = event.target.closest('#editCardButton');
            if (editButton) {
                const cardDetailsContainer = document.getElementById('cardDetailsContainer');
                cardDetailsContainer.innerHTML = ''; // Clear existing card details
                createEditableInputs(cardData);
            }
        });

        function createEditableInputs(data) {
            if (data && Object.keys(data).length > 0) {
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

                // Add an Edit button
                const editButton = document.createElement('button');
                editButton.innerText = 'Edit Card';
                editButton.addEventListener('click', function() {
                    // Create editable inputs for card details
                    cardDetailsContainer.innerHTML = `
                <input type="text" id="editName" value="${data.name}">
                <textarea name="editDescription" rows="4" id="editDescription">${data.description}</textarea>
                <input type="number" id="editAtk" value="${data.atk}">
                <input type="number" id="editDef" value="${data.def}">
                <input type="number" id="editLevel" value="${data.level}">
                <input type="text" id="editRace" value="${data.race}">
                <button id="saveButton">Save Changes</button>
                <button id="cancelButton">Cancel</button>
            `;

                    // Handle save and cancel button clicks
                    document.getElementById('saveButton').addEventListener('click', function() {
                        saveChanges(data.name);
                    });

                    document.getElementById('cancelButton').addEventListener('click', function() {
                        createEditableInputs(data);
                    });
                });

                cardDetailsContainer.appendChild(editButton);
            } else {
                console.error('No card found with that name');
            }
        }

        function saveChanges(cardName) {
            const editedName = document.getElementById('editName').value;
            const editedDescription = document.getElementById('editDescription').value;
            const editedAtk = document.getElementById('editAtk').value;
            const editedDef = document.getElementById('editDef').value;
            const editedLevel = document.getElementById('editLevel').value;
            const editedRace = document.getElementById('editRace').value;

            // Update the card details in the database
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'updateCard.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        alert('Card updated successfully');
                        location.reload(); // Reload the page
                    } else {
                        console.error('Error updating card:', xhr.statusText);
                    }
                }
            };
            xhr.send(`name=${cardName}&description=${editedDescription}&atk=${editedAtk}&def=${editedDef}&level=${editedLevel}&race=${editedRace}`);
        }
    </script>
</body>

</html>