<?php
include_once 'includes/connect.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    header('Location: /wd2/project/-YuGiOh-Project/login');
    exit;
}

// Logic for managing categories


// Fetch existing categories (card types)
$stmt = $db->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Assigning categories (types) to cards
// Assigning categories (types) to cards
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignCategory'])) {
    $name = $_POST['categoryId']; // Updated to 'categoryName'
    $cardId = $_POST['cardId'];
    // Validate input
    if (!empty($name) && !empty($cardId)) {
        $stmt = $db->prepare("UPDATE cards SET type = ? WHERE id = ?");
        $stmt->execute([$name, $cardId]);
    }
}


// Fetch cards and their associated categories (types)
$stmt = $db->prepare("SELECT * FROM cards");
$stmt->execute();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<main>
    <h2>Category Management</h2>
    <form method="post">
        <input type="hidden" name="action" value="create">
        <label for="name">Category Name:</label>
        <input type="text" id="name" name="name" required>
        <button type="submit">Create Category</button>
    </form>

    <!-- Display existing categories (card types) -->
    <h3>Existing Categories:</h3>
    <ul>
        <?php foreach ($categories as $category) : ?>
            <li class="category" data-category="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- Assign categories (types) to cards -->
    <!-- Assign categories (types) to cards -->
    <h3>Assign Categories to Cards:</h3>
    <form id="assignForm" method="post">
        <label for="categoryId">Category:</label>
        <select name="categoryId" id="categoryId">
            <option value="">Select a category</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <label for="cardId">Card:</label>
        <select name="cardId" id="cardId">
            <?php foreach ($cards as $card) : ?>
                <option value="<?php echo $card['id']; ?>"><?php echo $card['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="assignCategory">Assign Category</button>
    </form>



    <!-- Display cards for selected category -->
    <!-- Select a category to view cards -->
    <h3>Select a Category to View Cards:</h3>
    <form id="selectCategoryForm" method="get">
        <label for="category">Category:</label>
        <select name="category" id="category">
            <option value="">Select a category</option>
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">View Cards</button>
    </form>
    <h3>Cards for Selected Category:</h3>
    <ul id="cardList"></ul>
</main>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryList = document.querySelectorAll('.category');
        const cardList = document.getElementById('cardList');
        const selectCategoryForm = document.getElementById('selectCategoryForm');

        selectCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const selectedCategory = document.getElementById('category').value;
            fetch(`/wd2/project/-YuGiOh-Project/get-cards.php?category=${encodeURIComponent(selectedCategory)}`)
                .then(response => response.json())
                .then(data => {
                    cardList.innerHTML = '';
                    data.forEach(card => {
                        const listItem = document.createElement('li');
                        listItem.textContent = card.name;
                        cardList.appendChild(listItem);
                    });
                });
        });
    });

    const assignForm = document.getElementById('assignForm');

    assignForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const categoryId = document.getElementById('categoryId').value;
        const cardId = document.getElementById('cardId').value;
        fetch(`/wd2/project/-YuGiOh-Project/assign-category.php`, {
                method: 'POST',
                body: JSON.stringify({
                    categoryId,
                    cardId
                }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Log the response data
                console.log(data);
                // Handle the response data as needed
                if (data.status === 'error') {
                    alert(data.message); // Show an alert with the error message
                } else {
                    // Category assigned successfully, update UI or take appropriate action
                }
            })
            .catch(error => {
                // Handle errors
                console.error('There was a problem with the fetch operation:', error);
            });
    });
</script>