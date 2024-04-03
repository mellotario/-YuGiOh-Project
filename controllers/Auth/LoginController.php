<?php
class LoginController {
    public function showLoginForm($error = '') {
        include 'views/header/header.php';
        include 'views/auth/login.php';
        include 'views/footer.php';
    }

    public function handleLogin() {
        session_start();
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
            include 'includes/connect.php';
    
            $username = $_POST['username'];
            $password = $_POST['password'];
    
            $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch();
    
            if ($user && isset($user['password_hash']) && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['is_admin'] = $user['is_admin']; 
                $redirectUrl = $user['is_admin'] ? 'admin_page' : 'home';
                header('Location: ' . $redirectUrl);
                exit;
            } else {
                $error = 'Invalid username or password.';
                echo $error;
            }
        } 
    }
    
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: /wd2/project/-YuGiOh-Project/');
        exit;
    }
}

$loginController = new LoginController();
$loginController->handleLogin();
?>

