<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$user = getCurrentUser();
$booking = new Booking();

// Get user's bookings
$userBookings = $booking->getUserBookings($user['id']);

$title = 'Booking Saya - Room Booking System';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Booking Saya</h1>
    <div>
        <a href="bookings.php" class="btn btn-primary">
            <i class="bi bi-plus"></i> Booking Baru
        </a>
    </div>
</div>

<?php if (empty($userBookings)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-calendar-x display-4 text-muted"></i>
            <h4 class="mt-3">Belum Ada Booking</h4>
            <p class="text-muted">Anda belum memiliki booking ruangan.</p>
            <a href="bookings.php" class="btn btn-primary">
                <i class="bi bi-plus"></i> Buat Booking Pertama
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($userBookings as $b): ?>
            <?php
            $statusClass = [
                'Menunggu' => 'warning',
                'Diterima' => 'success',
                'Ditolak' => 'danger',
                'Selesai' => 'secondary'
            ];
            $statusIcon = [
                'Menunggu' => 'clock-history',
                'Diterima' => 'check-circle',
                'Ditolak' => 'x-circle',
                'Selesai' => 'check-circle-fill'
            ];
            ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card booking-card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><?= $b['nama_ruangan'] ?></h6>
                        <span class="badge bg-<?= $statusClass[$b['status']] ?>">
                            <i class="bi bi-<?= $statusIcon[$b['status']] ?>"></i> <?= $b['status'] ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="bi bi-calendar3 text-primary"></i>
                            <strong><?= formatDate($b['tanggal']) ?></strong>
                        </div>
                        <div class="mb-2">
                            <i class="bi bi-clock text-primary"></i>
                            <?= formatTime($b['waktu_mulai']) ?> - <?= formatTime($b['waktu_selesai']) ?>
                        </div>
                        <div class="mb-3">
                            <i class="bi bi-chat-text text-primary"></i>
                            <small><?= $b['keperluan_rapat'] ?></small>
                        </div>
                        
                        <?php if (!empty($b['catatan'])): ?>
                            <div class="alert alert-info alert-sm p-2 mb-2">
                                <small><strong>Catatan:</strong> <?= $b['catatan'] ?></small>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-muted small">
                            Dibuat: <?= date('d/m/Y H:i', strtotime($b['created_at'])) ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group w-100">
                            <?php if ($b['status'] == 'Menunggu'): ?>
                                <button class="btn btn-sm btn-outline-primary" 
                                        onclick="showBookingDetails(<?= $b['id'] ?>)">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                                <button class="btn btn-sm btn-outline-warning" 
                                        onclick="editBooking(<?= $b['id'] ?>)">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="cancelBooking(<?= $b['id'] ?>)">
                                    <i class="bi bi-trash"></i> Batal
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-primary w-100" 
                                        onclick="showBookingDetails(<?= $b['id'] ?>)">
                                    <i class="bi bi-eye"></i> Lihat Detail
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1">
    <div class="modal-dialog">
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
    // In a real implementation, you would make an AJAX call here
    // For now, we'll show a placeholder
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
    
    // Simulate loading
    setTimeout(function() {
        document.getElementById('bookingDetailsContent').innerHTML = `
            <p>Detail booking akan ditampilkan di sini.</p>
            <p>Booking ID: ${bookingId}</p>
        `;
    }, 1000);
}

function editBooking(bookingId) {
    if (confirm('Edit booking ini?')) {
        window.location.href = `edit-booking.php?id=${bookingId}`;
    }
}

function cancelBooking(bookingId) {
    if (confirm('Yakin ingin membatalkan booking ini?')) {
        window.location.href = `cancel-booking.php?id=${bookingId}`;
    }
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>