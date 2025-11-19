<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Room Booking System' ?></title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    /* === BASE STYLE (umum) === */
    body {
        font-family: 'Poppins', 'Segoe UI', sans-serif;
        background: linear-gradient(135deg, #007bff, #00c6ff);
        color: #1e293b;
        margin: 0;
    }

    /* === LOGIN PAGE (ketika sidebar belum muncul) === */
    <?php if (!isLoggedIn()): ?>
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    .card {
        border: none;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
        color: #fff;
        transition: transform 0.3s ease;
    }
g
    .card:hover {
        transform: translateY(-4px);
    }

    .btn-primary {
        background: #007bff;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-primary:hover {
        background: #005fcc;
        transform: scale(1.02);
    }

    .text-primary {
        color: #ffffff !important;
    }
    <?php endif; ?>


    /* === DASHBOARD / HALAMAN SETELAH LOGIN === */
    .sidebar {
        min-height: 100vh;
        background: linear-gradient(180deg, #007bff, #005fcc);
        color: #fff;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }

    .sidebar h5 {
        font-weight: 700;
        text-align: center;
        margin-bottom: 1.5rem;
        color: #fff;
    }

    .sidebar .nav-link {
        color: #e2e8f0;
        border-radius: 8px;
        margin: 4px 0;
        padding: 10px 12px;
        transition: all 0.2s ease;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        background-color: rgba(255,255,255,0.2);
        color: #fff;
    }

    .navbar {
        background: #ffffff !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .navbar-brand {
        font-weight: 700;
        color: #007bff !important;
    }

    .main-content {
        background: #f8fbff;
        min-height: 100vh;
        padding-bottom: 50px;
    }

    .card {
        border: none;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.2s ease;
    }

    .card:hover {
        transform: translateY(-3px);
    }

    .dropdown-menu {
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .status-badge {
        font-size: 0.8rem;
        font-weight: 500;
    }
    </style>
</head>

<body>
    <?php if (isLoggedIn()): ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar p-3">
                <div class="position-sticky">
                    <h5><i class="bi bi-building"></i> Room Booking</h5>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : '' ?>" href="bookings.php">
                                <i class="bi bi-calendar-check"></i> Booking Ruangan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'my-bookings.php' ? 'active' : '' ?>" href="my-bookings.php">
                                <i class="bi bi-calendar-event"></i> Booking Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'rooms.php' ? 'active' : '' ?>" href="rooms.php">
                                <i class="bi bi-door-open"></i> Data Ruangan
                            </a>
                        </li>
                        
                        <?php if (hasRole(['Admin', 'Resepsionis'])): ?>
                        <hr class="text-white">
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage-bookings.php' ? 'active' : '' ?>" href="manage-bookings.php">
                                <i class="bi bi-list-check"></i> Kelola Booking
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto main-content">
                <!-- Top navigation -->
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="dashboard.php">Room Booking System</a>
                        
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle"></i> <?= $_SESSION['user_name'] ?? 'User' ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><h6 class="dropdown-header"><?= $_SESSION['user_role'] ?? '' ?> - <?= $_SESSION['user_divisi'] ?? '' ?></h6></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                                    <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page content -->
                <div class="container-fluid p-4">
                    <?php displayAlert(); ?>
                    <?= $content ?? '' ?>
                </div>
            </main>
        </div>
    </div>
    <?php else: ?>
        <?= $content ?? '' ?>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);
    </script>
</body>
</html>
