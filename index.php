<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load the necessary files
require_once 'includes/connect.php';
require_once 'includes/authenticate.php';
require_once 'includes/functions.php';

// Include any controllers or additional setup files here
require_once 'controllers/HomeController.php';
require_once 'controllers/CardController.php';
require_once 'controllers/DeckController.php';
require_once 'controllers/CategoryController.php';
require_once 'controllers/ProfileController.php';
require_once 'controllers/Auth/LoginController.php';
require_once 'controllers/Auth/RegisterController.php';
require_once 'controllers/Auth/ForgotPasswordController.php';

// You can also include any other setup files or libraries here
// For example, if you're using a templating engine, you might include it here
// require_once 'path/to/templating/engine.php';

// You might also include any middleware or authentication logic here
// require_once 'path/to/middleware.php';


// Handle the request
$base_path = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$request_uri = str_replace($base_path, '', $_SERVER['REQUEST_URI']);
$request_uri = '/' . trim($request_uri, '/');

switch ($request_uri) {
    case '/':
        require 'views/home.php';
        break;
    case '/card-list':
        require 'views/card-list.php';
        break;
    case '/card-single':
        require 'views/card-single.php';
        break;
    case '/deck-list':
        require 'views/deck-list.php';
        break;
    case '/deck-single':
        require 'views/deck-single.php';
        break;
    case '/category':
        require 'views/category.php';
        break;
    case '/profile':
        require 'views/profile.php';
        break;
    case '/auth/login':
        require 'views/auth/login.php';
        break;
    case '/auth/register':
        require 'views/auth/register.php';
        break;
    case '/auth/forgot-password':
        require 'views/auth/forgot-password.php';
        break;
    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
?>
