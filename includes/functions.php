<?php
// Session Configuration (must be set before session_start())
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

// Autoload function for models
function autoload($className) {
    $paths = [
        __DIR__ . '/../models/' . $className . '.php',
        __DIR__ . '/../includes/' . $className . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
}

spl_autoload_register('autoload');

// Helper functions
function native_redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $db = new Database();
    $user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
    return $user;
}

function hasRole($roles) {
    $user = getCurrentUser();
    if (!$user) return false;
    
    if (is_array($roles)) {
        return in_array($user['role'], $roles);
    }
    
    return $user['role'] === $roles;
}

function canBookRoom($roomId) {
    $user = getCurrentUser();
    if (!$user) return false;
    
    $db = new Database();
    $access = $db->fetchOne("SELECT can_book FROM role_access WHERE role = ? AND ruangan_id = ?", 
                           [$user['role'], $roomId]);
    
    return $access ? $access['can_book'] : false;
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function formatTime($time) {
    return date('H:i', strtotime($time));
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function showAlert($message, $type = 'info') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

function displayAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        echo "<div class='alert alert-{$alert['type']} alert-dismissible fade show' role='alert'>
                {$alert['message']}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
        unset($_SESSION['alert']);
    }
}



function checkPermission($requiredRoles = []) {
    if (!isLoggedIn()) {
        native_redirect('/');
    }

    $user = getCurrentUser();

    if (!empty($requiredRoles)) {
        if (!is_array($requiredRoles)) {
            $requiredRoles = [$requiredRoles];
        }

        if (!in_array($user['role'], $requiredRoles)) {
            http_response_code(403);
            die('<!DOCTYPE html>
<html>
<head>
    <title>403 Forbidden</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
        .container { background: white; padding: 40px; border-radius: 8px; max-width: 500px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #dc3545; font-size: 48px; margin: 0; }
        p { color: #666; margin: 10px 0; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>403</h1>
        <p><strong>Forbidden</strong></p>
        <p>You don\'t have permission to access this resource.</p>
        <p>Role Anda: <strong>' . htmlspecialchars($user['role']) . '</strong></p>
        <p>Dibutuhkan: <strong>' . htmlspecialchars(implode(', ', $requiredRoles)) . '</strong></p>
        <p><a href="/dashboard">← Kembali ke Dashboard</a></p>
    </div>
</body>
</html>');
        }
    }

    return true;
}
?>