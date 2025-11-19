<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$room = new Room();
$rooms = $room->getAllRooms();

$title = 'Data Ruangan - Room Booking System';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Data Ruangan</h1>
    <?php if (hasRole('Admin')): ?>
        <a href="manage-rooms.php" class="btn btn-primary">
            <i class="bi bi-gear"></i> Kelola Ruangan
        </a>
    <?php endif; ?>
</div>

<div class="row"> 
    <?php foreach ($rooms as $r): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?= $r['nama_ruangan'] ?></h5>
                    <span class="badge bg-<?= $r['status'] == 'Aktif' ? 'success' : 'secondary' ?>">
                        <?= $r['status'] ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <i class="bi bi-person-badge text-primary"></i>
                        <strong>PIC:</strong> <?= $r['penanggung_jawab'] ?>
                    </div>
                    <div class="mb-2">
                        <i class="bi bi-people text-primary"></i>
                        <strong>Kapasitas:</strong> <?= $r['kapasitas'] ?> orang
                    </div>
                    <?php if (!empty($r['fasilitas'])): ?>
                        <div class="mb-3">
                            <i class="bi bi-gear text-primary"></i>
                            <strong>Fasilitas:</strong><br>
                            <small><?= $r['fasilitas'] ?></small>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <?php if (canBookRoom($r['id'])): ?>
                        <button class="btn btn-primary btn-sm w-100" onclick="bookRoom(<?= $r['id'] ?>, '<?= $r['nama_ruangan'] ?>')">
                            <i class="bi bi-calendar-plus"></i> Book Ruangan
                        </button>
                    <?php else: ?>
                        <div class="text-center text-muted">
                            <small>Tidak dapat dibooking</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
function bookRoom(roomId, roomName) {
    if (confirm(`Book ruangan ${roomName}?`)) {
        window.location.href = `bookings.php?room=${roomId}`;
    }
    
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>