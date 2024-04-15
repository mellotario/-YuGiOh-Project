<?php
include_once 'includes/connect.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    header('Location: /wd2/project/-YuGiOh-Project/login');
    exit;
}

// Logic for managing categories
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'create') {
        $name = $_POST['name'];
        // Validate input, e.g., check for empty name
        if (!empty($name)) {
            $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
        }
    }
}

// Fetch existing categories (card types)
$stmt = $db->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Assigning categories (types) to cards
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignCategory'])) {
    $categoryId = $_POST['categoryId'];
    $cardId = $_POST['cardId'];
    // Validate input
    if (!empty($categoryId) && !empty($cardId)) {
        $stmt = $db->prepare("UPDATE cards SET type = ? WHERE id = ?");
        $stmt->execute([$categoryId, $cardId]);
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
            <li class="category" data-category="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- Assign categories (types) to cards -->
    <h3>Assign Categories to Cards:</h3>
    <form id="assignForm" method="post">
        <label for="categoryId">Category:</label>
        <select name="categoryId" id="categoryId">
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
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
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categoryList = document.querySelectorAll('.category');
        const cardSelect = document.getElementById('cardId');

        categoryList.forEach(category => {
            category.addEventListener('click', function () {
                const categoryValue = this.getAttribute('data-category');
                fetchCardsByCategory(categoryValue);
            });
        });

        function fetchCardsByCategory(category) {
            fetch(`/get-cards.php?category=${category}`)
                .then(response => response.json())
                .then(data => {
                    cardSelect.innerHTML = '';
                    data.forEach(card => {
                        const option = document.createElement('option');
                        option.value = card.id;
                        option.textContent = card.name;
                        cardSelect.appendChild(option);
                    });
                });
        }

        document.getElementById('assignForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const categoryId = document.getElementById('categoryId').value;
            const cardId = document.getElementById('cardId').value;
            // Assign category to card
            fetch('/assign-category.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    categoryId: categoryId,
                    cardId: cardId,
                }),
            })
                .then(response => response.json())
                .then(data => {
                    // Handle response if needed
                    console.log(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });
</script>
