<?php
require 'includes/connect.php';
$stmt = $pdo->query("SELECT * FROM pages");
$menuLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            margin: 0;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            width: 50px;
            height: 50px;
        }

        .logo a {
            color: #fff;
            text-decoration: none;
        }

        h1 {
            font-size: 2rem;
            margin: 0;
        }

        h1 span {
            font-family: 'Roboto', sans-serif;
        }

        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        nav ul li {
            margin-right: 20px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            header {
                padding: 10px;
            }

            .header-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .logo {
                margin-bottom: 10px;
            }

            h1 {
                font-size: 1.5rem;
            }

            nav ul {
                flex-direction: column;
            }

            nav ul li {
                margin-right: 0;
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="header-container">
            <img src="assets/yugioh-eye.png" alt="Yu-Gi-Oh! Eye" class="logo">
            <h1>Welcome to <span>My Project</span></h1>
            <nav>
                <ul>
                    <?php foreach ($menuLinks as $menuItem) : ?>
                        <?php if ($menuItem['title'] != "Admin Page") : ?>
                            <li><a href="/wd2/project/-YuGiOh-Project<?php echo $menuItem['url']; ?>"><?php echo $menuItem['title']; ?></a></li>

                        <?php endif; ?>

                    <?php endforeach; ?>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) : ?>
                        <li><a href="admin_page">Admin Page</a></li>
                    <?php endif; ?>
                    <li><?php echo isset($_SESSION['user_id']) ? '<a href="logout">Logout</a>' : '<a href="login">Login</a>'; ?></li>
                </ul>
            </nav>
        </div>
    </header>
</body>

</html>