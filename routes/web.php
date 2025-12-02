<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], '/', function () {
    ob_start();
    include base_path('index.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/dashboard', function () {
    ob_start();
    include base_path('dashboard.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/rooms', function () {
    ob_start();
    include base_path('rooms.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/bookings', function () {
    ob_start();
    include base_path('bookings.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/manage-bookings', function () {
    ob_start();
    include base_path('manage-bookings.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/my-bookings', function () {
    ob_start();
    include base_path('my-bookings.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/logout', function () {
    ob_start();
    include base_path('logout.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/logout.php', function () {
    ob_start();
    include base_path('logout.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/dashboard.php', function () {
    ob_start();
    include base_path('dashboard.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/debug-permission', function () {
    ob_start();
    include base_path('debug-permission.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/bookings.php', function () {
    ob_start();
    include base_path('bookings.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/my-bookings.php', function () {
    ob_start();
    include base_path('my-bookings.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/rooms.php', function () {
    ob_start();
    include base_path('rooms.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/manage-bookings.php', function () {
    ob_start();
    include base_path('manage-bookings.php');
    return ob_get_clean();
});

Route::match(['get', 'post'], '/profile.php', function () {
    ob_start();
    include base_path('profile.php');
    return ob_get_clean();
});
