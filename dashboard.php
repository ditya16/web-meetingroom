<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$user = getCurrentUser();
$booking = new Booking();
$room = new Room();

// Get statistics
$stats = $booking->getBookingStats();
$todayBookings = $booking->getTodayBookings();
$upcomingBookings = $booking->getUpcomingBookings(5);
$pendingBookings = hasRole(['Admin', 'Resepsionis']) ? $booking->getPendingBookings() : [];

$title = 'Dashboard - Room Booking System';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Dashboard</h1>
    <div class="text-muted">
        <?= date('l, d F Y') ?>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="display-6 text-primary mb-2">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <h5 class="card-title"><?= $stats['today'] ?></h5>
                <p class="card-text text-muted">Booking Hari Ini</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="display-6 text-warning mb-2">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h5 class="card-title"><?= $stats['pending'] ?></h5>
                <p class="card-text text-muted">Menunggu Approval</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="display-6 text-success mb-2">
                    <i class="bi bi-calendar-month"></i>
                </div>
                <h5 class="card-title"><?= $stats['month'] ?></h5>
                <p class="card-text text-muted">Booking Bulan Ini</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="display-6 text-info mb-2">
                    <i class="bi bi-list-check"></i>
                </div>
                <h5 class="card-title"><?= $stats['total'] ?></h5>
                <p class="card-text text-muted">Total Booking</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Today's Bookings -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-day"></i> Booking Hari Ini
                </h5>
                <a href="bookings.php" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Booking Baru
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($todayBookings)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-calendar-x display-4"></i>
                        <p class="mt-2">Tidak ada booking hari ini</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($todayBookings as $booking): ?>
                        <div class="border-start border-primary border-3 ps-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><?= $booking['nama_ruangan'] ?></h6>
                                    <p class="mb-1 text-muted">
                                        <i class="bi bi-clock"></i> 
                                        <?= formatTime($booking['waktu_mulai']) ?> - <?= formatTime($booking['waktu_selesai']) ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-person"></i> <?= $booking['pemesan_nama'] ?>
                                    </p>
                                    <small class="text-muted"><?= $booking['keperluan_rapat'] ?></small>
                                </div>
                                <span class="badge bg-success">Diterima</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Bookings -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-plus"></i> Booking Mendatang
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($upcomingBookings)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-calendar-plus display-4"></i>
                        <p class="mt-2">Tidak ada booking mendatang</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($upcomingBookings as $booking): ?>
                        <div class="border-start border-info border-3 ps-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><?= $booking['nama_ruangan'] ?></h6>
                                    <p class="mb-1 text-muted">
                                        <i class="bi bi-calendar"></i> <?= formatDate($booking['tanggal']) ?>
                                    </p>
                                    <p class="mb-1 text-muted">
                                        <i class="bi bi-clock"></i> 
                                        <?= formatTime($booking['waktu_mulai']) ?> - <?= formatTime($booking['waktu_selesai']) ?>
                                    </p>
                                    <p class="mb-1">
                                        <i class="bi bi-person"></i> <?= $booking['pemesan_nama'] ?>
                                    </p>
                                    <small class="text-muted"><?= $booking['keperluan_rapat'] ?></small>
                                </div>
                                <span class="badge bg-success">Diterima</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Pending Bookings (Admin/Resepsionis only) -->
<?php if (!empty($pendingBookings)): ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history"></i> Booking Menunggu Approval
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ruangan</th>
                                <th>Pemesan</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Keperluan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingBookings as $booking): ?>
                            <tr>
                                <td><?= $booking['nama_ruangan'] ?></td>
                                <td>
                                    <?= $booking['pemesan_nama'] ?><br>
                                    <small class="text-muted"><?= $booking['pemesan_email'] ?></small>
                                </td>
                                <td><?= formatDate($booking['tanggal']) ?></td>
                                <td>
                                    <?= formatTime($booking['waktu_mulai']) ?> - 
                                    <?= formatTime($booking['waktu_selesai']) ?>
                                </td>
                                <td><?= $booking['keperluan_rapat'] ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="manage-bookings.php?action=approve&id=<?= $booking['id'] ?>" 
                                           class="btn btn-success" 
                                           onclick="return confirm('Approve booking ini?')">
                                            <i class="bi bi-check"></i>
                                        </a>
                                        <a href="manage-bookings.php?action=reject&id=<?= $booking['id'] ?>" 
                                           class="btn btn-danger"
                                           onclick="return confirm('Tolak booking ini?')">
                                            <i class="bi bi-x"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>