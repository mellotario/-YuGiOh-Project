<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load the necessary files
require_once 'includes/connect.php';
require_once 'includes/authenticate.php';
require_once 'includes/functions.php';

// Include any controllers or additional setup files here
$controllers = [
    'Home' => 'HomeController.php',
    'Card' => 'CardController.php',
    'Category' => 'CategoryController.php',
    'Profile' => 'ProfileController.php',
    'Auth/Login' => 'Auth/LoginController.php',
    'Auth/Register' => 'Auth/RegisterController.php',
    'Auth/ForgotPassword' => 'Auth/ForgotPasswordController.php',
    'Auth/AdminController' => 'Auth/AdminController.php',
    'PageList' => 'PageListController.php',
    'Comments' => 'CommentsController.php',
];


foreach ($controllers as $controller => $file) {
    require_once 'controllers/' . $file;
}

$base_path = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$request_uri = str_replace($base_path, '', $_SERVER['REQUEST_URI']);
$request_uri = '/' . trim($request_uri, '/');

switch ($request_uri) {
    case '/home':
        $homeController = new HomeController();
        $homeController->index();
        break;
    case '/':
        $homeController = new HomeController();
        $homeController->index();
        break;
    case '/card-list':
        $cardController = new CardController();
        $cardController->list();
        break;
    case '/single-card':
        $cardController = new CardController();
        $cardController->single();
        break;
    case '/category':
        $categoryController = new CategoryController();
        $categoryController->index();
        break;
    case '/profile':
        $profileController = new ProfileController();
        $profileController->index();
        break;
    case '/login':
        $loginController = new LoginController();
        $loginController->showLoginForm();
        break;
    case '/logout':
        $loginController = new LoginController();
        $loginController->logout();
        break;
    case '/register':
        $registerController = new RegisterController();
        $registerController->showRegisterForm();
        break;
    case '/forgot-password':
        $forgotPasswordController = new ForgotPasswordController();
        $forgotPasswordController->showForgotPasswordForm();
        break;

    case '/page-list':
        $pageListController = new PageListController();
        $pageListController->index();
        break;

    case '/comments':
        $commentsController = new CommentsController();
        $commentsController->index();
        break;

    case '/admin_page':
        // Check if user is logged in and is admin
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $stmt = $db->prepare("SELECT is_admin FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user['is_admin']) {
                // Show admin page
                $adminController = new AdminController();
                $adminController->index($db);
                break;
            }
        }

        // If not logged in or not admin, redirect to home page
        header('Location: /home');
        exit;
    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
