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
        $name = $_POST['categoryName'];
        // Validate input, e.g., check for empty name
        if (!empty($name)) {
            $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
        }
    }
}

// Fetch existing categories
$stmt = $db->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Assigning categories to pages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignCategory'])) {
    $categoryId = $_POST['categoryId'];
    $pageId = $_POST['pageId'];
    // Validate input
    if (!empty($categoryId) && !empty($pageId)) {
        $stmt = $db->prepare("INSERT INTO category_page (category_id, page_id) VALUES (?, ?)");
        $stmt->execute([$categoryId, $pageId]);
    }
}

// Fetch pages and their associated categories
$stmt = $db->prepare("SELECT p.*, GROUP_CONCAT(c.name SEPARATOR ', ') AS categories 
                      FROM pages p
                      LEFT JOIN category_page cp ON p.id = cp.page_id
                      LEFT JOIN categories c ON cp.category_id = c.id
                      GROUP BY p.id");
$stmt->execute();
$pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main>
    <h2>Category Management</h2>
    <form method="post">
        <input type="hidden" name="action" value="create">
        <label for="categoryName">Category Name:</label>
        <input type="text" id="categoryName" name="categoryName" required>
        <button type="submit">Create Category</button>
    </form>

    <!-- Display existing categories -->
    <h3>Existing Categories:</h3>
    <ul>
        <?php foreach ($categories as $category) : ?>
            <li><?php echo $category['name']; ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- Assign categories to pages -->
    <h3>Assign Categories to Pages:</h3>
    <form method="post">
        <label for="categoryId">Category:</label>
        <select name="categoryId" id="categoryId">
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <label for="pageId">Page:</label>
        <select name="pageId" id="pageId">
            <?php foreach ($pages as $page) : ?>
                <option value="<?php echo $page['id']; ?>"><?php echo $page['title']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="assignCategory">Assign Category</button>
    </form>

    <!-- Display pages by category -->
    <h3>Pages by Category:</h3>
    <?php foreach ($categories as $category) : ?>
        <h4><?php echo $category['name']; ?></h4>
        <ul>
            <?php foreach ($pages as $page) : ?>
                <?php if ($page['categories'] && strpos($page['categories'], $category['name']) !== false) : ?>
                    <li><a href="/wd2/project/-YuGiOh-Project<?php echo $page['url']; ?>"><?php echo $page['title']; ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
</main>