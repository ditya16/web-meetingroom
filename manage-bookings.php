<?php
require_once 'includes/functions.php';

if (!isLoggedIn() || !hasRole(['Admin', 'Resepsionis'])) {
    redirect('dashboard.php');
}

$booking = new Booking();

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $bookingId = (int)$_GET['id'];
    $action = $_GET['action'];
    
    switch ($action) {
        case 'approve':
            if ($booking->updateBookingStatus($bookingId, 'Diterima')) {
                showAlert('Booking berhasil disetujui.', 'success');
            } else {
                showAlert('Gagal menyetujui booking.', 'danger');
            }
            break;
            
        case 'reject':
            if ($booking->updateBookingStatus($bookingId, 'Ditolak')) {
                showAlert('Booking berhasil ditolak.', 'success');
            } else {
                showAlert('Gagal menolak booking.', 'danger');
            }
            break;
            
        case 'delete':
            if ($booking->deleteBooking($bookingId)) {
                showAlert('Booking berhasil dihapus.', 'success');
            } else {
                showAlert('Gagal menghapus booking.', 'danger');
            }
            break;
    }
    redirect('manage-bookings.php');
}

// Get all bookings
$allBookings = $booking->getAllBookings();
$pendingBookings = $booking->getPendingBookings();

$title = 'Kelola Booking - Room Booking System';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Kelola Booking</h1>
    <div>
        <span class="badge bg-warning fs-6">
            <?= count($pendingBookings) ?> Menunggu Approval
        </span>
    </div>
</div>

<!-- Pending Bookings -->
<?php if (!empty($pendingBookings)): ?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-clock-history text-warning"></i> Booking Menunggu Approval
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ruangan</th>
                        <th>Pemesan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Keperluan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingBookings as $b): ?>
                    <tr>
                        <td>#<?= $b['id'] ?></td>
                        <td><?= $b['nama_ruangan'] ?></td>
                        <td>
                            <strong><?= $b['pemesan_nama'] ?></strong><br>
                            <small class="text-muted"><?= $b['pemesan_email'] ?></small>
                        </td>
                        <td><?= formatDate($b['tanggal']) ?></td>
                        <td>
                            <?= formatTime($b['waktu_mulai']) ?> - 
                            <?= formatTime($b['waktu_selesai']) ?>
                        </td>
                        <td>
                            <span title="<?= $b['keperluan_rapat'] ?>">
                                <?= strlen($b['keperluan_rapat']) > 30 ? substr($b['keperluan_rapat'], 0, 30) . '...' : $b['keperluan_rapat'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="?action=approve&id=<?= $b['id'] ?>" 
                                   class="btn btn-success" 
                                   onclick="return confirm('Approve booking ini?')"
                                   title="Approve">
                                    <i class="bi bi-check"></i>
                                </a>
                                <a href="?action=reject&id=<?= $b['id'] ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Tolak booking ini?')"
                                   title="Tolak">
                                    <i class="bi bi-x"></i>
                                </a>
                                <button class="btn btn-info" 
                                        onclick="showBookingDetails(<?= $b['id'] ?>)"
                                        title="Detail">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- All Bookings -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-list-check"></i> Semua Booking
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ruangan</th>
                        <th>Pemesan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allBookings as $b): ?>
                    <?php
                    $statusClass = [
                        'Menunggu' => 'warning',
                        'Diterima' => 'success',
                        'Ditolak' => 'danger',
                        'Selesai' => 'secondary'
                    ];
                    ?>
                    <tr>
                        <td>#<?= $b['id'] ?></td>
                        <td><?= $b['nama_ruangan'] ?></td>
                        <td>
                            <strong><?= $b['pemesan_nama'] ?></strong><br>
                            <small class="text-muted"><?= $b['divisi'] ?></small>
                        </td>
                        <td><?= formatDate($b['tanggal']) ?></td>
                        <td>
                            <?= formatTime($b['waktu_mulai']) ?> - 
                            <?= formatTime($b['waktu_selesai']) ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $statusClass[$b['status']] ?>">
                                <?= $b['status'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-info" 
                                        onclick="showBookingDetails(<?= $b['id'] ?>)"
                                        title="Detail">
                                    <i class="bi bi-eye"></i>
                                </button>
                                
                                <?php if ($b['status'] == 'Menunggu'): ?>
                                    <a href="?action=approve&id=<?= $b['id'] ?>" 
                                       class="btn btn-success" 
                                       onclick="return confirm('Approve booking ini?')"
                                       title="Approve">
                                        <i class="bi bi-check"></i>
                                    </a>
                                    <a href="?action=reject&id=<?= $b['id'] ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirm('Tolak booking ini?')"
                                       title="Tolak">
                                        <i class="bi bi-x"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="?action=delete&id=<?= $b['id'] ?>" 
                                   class="btn btn-outline-danger" 
                                   onclick="return confirm('Hapus booking ini?')"
                                   title="Hapus">
                                    <i class="bi bi-trash"></i>
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

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bookingDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function showBookingDetails(bookingId) {
    const modal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
    document.getElementById('bookingDetailsContent').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat detail booking...</p>
        </div>
    `;
    modal.show();
    
    // In a real implementation, you would make an AJAX call here
    setTimeout(function() {
        document.getElementById('bookingDetailsContent').innerHTML = `
            <p>Booking ID: ${bookingId}</p>
        `;
    }, 1000);
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>