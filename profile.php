<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    native_redirect('index.php');
}

$user = getCurrentUser();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitizeInput($_POST['nama']);
    $email = sanitizeInput($_POST['email']);
    $divisi = sanitizeInput($_POST['divisi']);
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($nama) || empty($email) || empty($divisi)) {
        $error = 'Nama, email, dan divisi harus diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        // Check if email is already used by another user
        $db = new Database();
        $existingUser = $db->query("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $user['id']])->fetch_assoc();
        if ($existingUser) {
            $error = 'Email sudah digunakan oleh pengguna lain.';
        } else {
            // Handle password change
            $updateData = [
                'nama' => $nama,
                'email' => $email,
                'divisi' => $divisi
            ];

            if (!empty($newPassword)) {
                if (empty($currentPassword)) {
                    $error = 'Password saat ini harus diisi untuk mengubah password.';
                } elseif (md5($currentPassword) !== $user['password']) {
                    $error = 'Password saat ini tidak cocok.';
                } elseif (strlen($newPassword) < 6) {
                    $error = 'Password baru minimal 6 karakter.';
                } elseif ($newPassword !== $confirmPassword) {
                    $error = 'Konfirmasi password tidak cocok.';
                } else {
                    $updateData['password'] = md5($newPassword);
                }
            }

            if (empty($error)) {
                // Update user data
                $setClause = [];
                $params = [];
                foreach ($updateData as $field => $value) {
                    $setClause[] = "$field = ?";
                    $params[] = $value;
                }
                $params[] = $user['id'];

                $result = $db->query("UPDATE users SET " . implode(', ', $setClause) . " WHERE id = ?", $params);

                if ($result) {
                    $success = 'Profil berhasil diperbarui.';
                    // Update session data
                    $_SESSION['user']['nama'] = $nama;
                    $_SESSION['user']['email'] = $email;
                    $_SESSION['user']['divisi'] = $divisi;
                    $user = getCurrentUser(); // Refresh user data
                } else {
                    $error = 'Gagal memperbarui profil.';
                }
            }
        }
    }
}

$title = 'Profil Saya - Room Booking System';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Profil Saya</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Profil</li>
        </ol>
    </nav>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $success ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-person"></i> Informasi Profil
                </h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="_token" value="<?php echo session()->token(); ?>" />
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                   value="<?= htmlspecialchars($user['nama']) ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="divisi" class="form-label">Divisi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="divisi" name="divisi"
                                   value="<?= htmlspecialchars($user['divisi']) ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" class="form-control" id="role" name="role"
                                   value="<?= htmlspecialchars($user['role']) ?>" readonly>
                            <div class="form-text">Role tidak dapat diubah</div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3">Ubah Password (Opsional)</h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                            <div class="form-text">Minimal 6 karakter</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Profile Info -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i> Informasi Akun
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Role:</strong> <?= htmlspecialchars($user['role']) ?>
                </div>
                <div class="mb-2">
                    <strong>Divisi:</strong> <?= htmlspecialchars($user['divisi']) ?>
                </div>
                <div class="mb-2">
                    <strong>Bergabung:</strong> <?= date('d F Y', strtotime($user['created_at'])) ?>
                </div>
                <div class="mb-0">
                    <strong>Terakhir Update:</strong> <?= date('d F Y H:i', strtotime($user['updated_at'])) ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-lightning"></i> Aksi Cepat
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="bookings.php" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-plus"></i> Booking Baru
                    </a>
                    <a href="my-bookings.php" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-list"></i> Lihat Booking Saya
                    </a>
                    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
