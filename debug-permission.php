<?php
/**
 * Debug Permission System
 * File ini membantu debugging masalah permission
 * 
 * Akses: http://localhost/room/debug-permission.php
 * (Hapus file ini setelah debugging selesai)
 */

require_once 'includes/functions.php';

$debug = [];
$debug['timestamp'] = date('Y-m-d H:i:s');

// 1. Check login status
$debug['is_logged_in'] = isLoggedIn();

// 2. Get current user
if (isLoggedIn()) {
    $debug['current_user'] = getCurrentUser();
    $debug['user_role'] = $debug['current_user']['role'] ?? 'Unknown';
} else {
    $debug['current_user'] = 'Not logged in';
    $debug['user_role'] = 'Unknown';
}

// 3. Get all sessions
$debug['session_data'] = $_SESSION;

// 4. Database connection test
try {
    $db = new Database();
    $debug['db_status'] = 'Connected';
    
    // Get all users
    $users = $db->fetchAll("SELECT id, nama, email, role FROM users");
    $debug['users_count'] = count($users);
    $debug['users'] = $users;
    
    // Get role access rules
    $roleAccess = $db->fetchAll("SELECT * FROM role_access LIMIT 20");
    $debug['role_access_count'] = count($roleAccess);
    
} catch (Exception $e) {
    $debug['db_status'] = 'Error: ' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permission Debug - Room Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
            padding: 20px;
            font-family: 'Courier New', monospace;
        }
        .debug-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .debug-section {
            background: white;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .debug-section h2 {
            font-size: 18px;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .debug-item {
            margin-bottom: 10px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 3px;
        }
        .debug-key {
            color: #007bff;
            font-weight: bold;
        }
        .debug-value {
            color: #333;
            word-break: break-all;
        }
        .table-container {
            overflow-x: auto;
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .badge-success { background: #28a745; }
        .badge-danger { background: #dc3545; }
        .badge-warning { background: #ffc107; color: #333; }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="debug-container">
        <div class="warning-box">
            <strong>⚠️ Warning:</strong> Ini adalah halaman debug untuk developer. 
            Hapus file <code>debug-permission.php</code> setelah selesai.
        </div>

        <h1 style="margin-bottom: 30px;">🔍 Permission System Debug</h1>

        <!-- Login Status -->
        <div class="debug-section">
            <h2>Login Status</h2>
            <div class="debug-item">
                <span class="debug-key">Status:</span>
                <span class="badge <?= $debug['is_logged_in'] ? 'badge-success' : 'badge-danger' ?>">
                    <?= $debug['is_logged_in'] ? 'LOGGED IN' : 'NOT LOGGED IN' ?>
                </span>
            </div>
            <div class="debug-item">
                <span class="debug-key">Role:</span>
                <span class="debug-value"><?= htmlspecialchars($debug['user_role']) ?></span>
            </div>
        </div>

        <!-- Current User Info -->
        <?php if ($debug['is_logged_in']): ?>
        <div class="debug-section">
            <h2>Current User Information</h2>
            <div class="table-container">
                <table class="table table-sm table-striped mb-0">
                    <tbody>
                        <?php foreach ($debug['current_user'] as $key => $value): ?>
                        <tr>
                            <td class="debug-key" style="width: 20%;"><?= htmlspecialchars($key) ?></td>
                            <td class="debug-value"><?= htmlspecialchars($value) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Database Status -->
        <div class="debug-section">
            <h2>Database Connection</h2>
            <div class="debug-item">
                <span class="debug-key">Status:</span>
                <span class="badge <?= strpos($debug['db_status'], 'Connected') !== false ? 'badge-success' : 'badge-danger' ?>">
                    <?= htmlspecialchars($debug['db_status']) ?>
                </span>
            </div>
            <?php if (strpos($debug['db_status'], 'Connected') !== false): ?>
            <div class="debug-item">
                <span class="debug-key">Users in database:</span>
                <span class="debug-value"><?= $debug['users_count'] ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- All Users -->
        <?php if (!empty($debug['users'])): ?>
        <div class="debug-section">
            <h2>All Users in Database</h2>
            <div class="table-container">
                <table class="table table-sm table-striped">
                    <thead style="background: #f0f0f0;">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($debug['users'] as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['nama']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><span class="badge bg-info"><?= htmlspecialchars($user['role']) ?></span></td>
                            <td>
                                <small>
                                    <a href="?test_role=<?= urlencode($user['email']) ?>">Test</a>
                                </small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Session Data -->
        <div class="debug-section">
            <h2>Session Data</h2>
            <pre><?= json_encode($debug['session_data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?></pre>
        </div>

        <!-- Available Functions -->
        <div class="debug-section">
            <h2>Available Permission Functions</h2>
            <div class="debug-item">
                <code>isLoggedIn()</code> - Cek apakah user sudah login
            </div>
            <div class="debug-item">
                <code>getCurrentUser()</code> - Ambil data user yang login
            </div>
            <div class="debug-item">
                <code>hasRole($roles)</code> - Cek apakah user punya role tertentu
            </div>
            <div class="debug-item">
                <code>checkPermission($roles)</code> - Check permission dengan error 403 jika tidak punya akses
            </div>
            <div class="debug-item">
                <code>canBookRoom($roomId)</code> - Cek apakah user bisa booking room tertentu
            </div>
        </div>

        <!-- Quick Test -->
        <div class="debug-section">
            <h2>Quick Permission Test</h2>
            <?php if ($debug['is_logged_in']): ?>
            <div class="alert alert-info">
                <strong>Current Role:</strong> <?= htmlspecialchars($debug['user_role']) ?>
            </div>
            
            <h5>Test access to pages:</h5>
            <ul>
                <li>
                    <a href="dashboard.php" class="btn btn-sm btn-outline-primary">Dashboard</a>
                </li>
                <li>
                    <a href="bookings.php" class="btn btn-sm btn-outline-primary">Bookings</a>
                </li>
                <li>
                    <a href="my-bookings.php" class="btn btn-sm btn-outline-primary">My Bookings</a>
                </li>
                <li>
                    <a href="manage-bookings.php" class="btn btn-sm btn-outline-primary">Manage Bookings (Admin Only)</a>
                </li>
                <li>
                    <a href="rooms.php" class="btn btn-sm btn-outline-primary">Rooms</a>
                </li>
            </ul>
            <?php else: ?>
            <div class="alert alert-warning">
                <strong>Not logged in.</strong> <a href="index.php">Go to login page</a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Help -->
        <div class="debug-section">
            <h2>Troubleshooting</h2>
            <h5>Problem: Still seeing "Forbidden"</h5>
            <ul>
                <li>Check user role above - is it correct?</li>
                <li>Try accessing dashboard first</li>
                <li>Check browser console for errors</li>
            </ul>
            <h5>Problem: Can't login</h5>
            <ul>
                <li>Check if database is connected ✓</li>
                <li>Verify email and password in "All Users" table above</li>
                <li>Try default: <strong>admin@ntp.co.id / admin123</strong></li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 40px; color: #999;">
            <small>Debug Page - Safe to delete after troubleshooting</small><br>
            <small>Timestamp: <?= $debug['timestamp'] ?></small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
