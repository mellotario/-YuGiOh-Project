<?php
// Include the database connection
include 'C:\xampp\htdocs\wd2\project\-YuGiOh-Project\includes\connect.php';

// Get the card ID from the query string
$cardId = isset($_GET['card_id']) ? $_GET['card_id'] : null;

// Fetch the card information from the database
$stmt = $pdo->prepare("SELECT * FROM cards WHERE id = :card_id");
$stmt->execute(['card_id' => $cardId]);
$card = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$card) {
    echo 'Card not found.';
    exit;
}

if (isset($_SESSION['user_id'])) {
    // User is logged in
    $isLoggedIn = true;
    // Get the username from the session
    $name = $_SESSION['username'];
} else {
    $isLoggedIn = false;
    // User is not logged in
    // Use the name entered in the form
    $name = isset($_POST['name']) ? $_POST['name'] : '';
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission (insert comment into database)
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';

    // Insert the comment into the database
    $stmt = $pdo->prepare("INSERT INTO comments (card_id, name, content, created_at) VALUES (:card_id, :name, :comment, NOW())");
    $stmt->execute(['card_id' => $cardId, 'name' => $name, 'comment' => $comment]);

    // Redirect back to the comments page to prevent form resubmission
    header('Location: comments.php?card_id=' . $cardId);
    exit;
}

// Fetch comments for this card from the database
$stmt = $pdo->prepare("SELECT * FROM comments WHERE card_id = :card_id ORDER BY created_at DESC");
$stmt->execute(['card_id' => $cardId]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments for <?php echo $card['name']; ?></title>
</head>

<body>
    <div>
        <a href="/wd2/project/-YuGiOh-Project/card-list">Back</a>
        <h1>Comments for <?php echo $card['name']; ?></h1>

        <?php if ($card['image_url']) : ?>
            <img style="height:250px;width:172px" src="<?php echo $card['image_url']; ?>" alt="<?php echo $card['name']; ?>" style="max-width: 100%;">
        <?php endif; ?>

        <div>
            <h2>Submit a Comment</h2>
            <form action="" method="post">
                <?php if (!$isLoggedIn) : ?>
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required><br>
                <?php else : ?>
                    <input type="hidden" id="name" name="name" value="<?php echo $name; ?>">
                    <p><strong>Logged in as:</strong> <?php echo $name; ?></p>
                <?php endif; ?>
                <label for="comment">Comment:</label><br>
                <textarea id="comment" name="comment" rows="4" cols="50" required></textarea><br>
                <button type="submit">Submit Comment</button>
            </form>
        </div>

        <hr>

        <div>
            <h2>Comments</h2>
            <?php foreach ($comments as $comment) : ?>
                <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
                    <p><strong>Name:</strong> <?php echo $comment['name']; ?></p>
                    <p><strong>Comment:</strong> <?php echo $comment['content']; ?></p>
                    <p><strong>Posted at:</strong> <?php echo $comment['created_at']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>

</html>