<?php
require_once 'includes/functions.php';

// Redirect jika sudah login
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_POST) {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi.';
    } else {
        $user = new User();
        if ($user->login($email, $password)) {
            showAlert('Login berhasil! Selamat datang.', 'success');
            redirect('dashboard.php');
        } else {
            $error = 'Email atau password salah.';
        }
    }
}

$title = 'Login - Room Booking System';
ob_start();
?>

<style>
    body {
        background: linear-gradient(135deg, #002a57ff, #0503a0ff);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Poppins', sans-serif;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 40px 35px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
        color: #fff;
        width: 100%;
        max-width: 380px;
        text-align: center;
    }

    .login-card h2 {
        font-weight: 700;
        margin-bottom: 10px;
        color: #fff;
    }

    .login-card p {
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 25px;
    }

    .form-label {
        color: #fff;
        font-weight: 500;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.25);
        border: none;
        border-radius: 10px;
        color: #fff;
        padding: 12px;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .input-group-text {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: #fff;
    }

    .btn-primary {
        background: #007bff;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        transition: 0.3s;
        padding: 10px;
    }

    .btn-primary:hover {
        background: #005fcc;
        transform: scale(1.02);
    }

    .alert {
        border-radius: 10px;
    }
</style>

<div class="login-card">
    <div class="mb-4">
        <i class="bi bi-building text-white" style="font-size: 2rem;"></i>
        <h2>Room Booking</h2>
        <p>Sistem Booking Ruangan Meeting</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3 text-start">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="contoh@ntp.co.id" value="<?= $_POST['email'] ?? '' ?>" required>
            </div>
        </div>
        
        <div class="mb-4 text-start">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Masukkan password" required>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary w-100 mb-3">
            <i class="bi bi-box-arrow-in-right"></i> Login
        </button>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
