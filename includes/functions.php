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
function redirect($url) {
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
?>