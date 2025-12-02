<?php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    session_destroy();
}

showAlert('Anda telah logout.', 'info');
native_redirect('/');
?>