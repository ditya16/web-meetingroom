<?php
require_once 'includes/functions.php';

checkPermission();

$user = getCurrentUser();
$booking = new Booking();
$room = new Room();

$error = '';
$success = '';

// Get user accessible rooms
$rooms = $room->getUserAccessibleRooms($user['role']);

// Handle form submission
if ($_POST) {
    $ruanganId = (int)$_POST['ruangan_id'];
    $tanggal = $_POST['tanggal'];
    $waktuMulai = $_POST['waktu_mulai'];
    $waktuSelesai = $_POST['waktu_selesai'];
    $keperluanRapat = sanitizeInput($_POST['keperluan_rapat']);
    
    // Validation
    if (empty($ruanganId) || empty($tanggal) || empty($waktuMulai) || empty($waktuSelesai) || empty($keperluanRapat)) {
        $error = 'Semua field harus diisi.';
    } elseif ($tanggal < date('Y-m-d')) {
        $error = 'Tanggal booking tidak boleh kurang dari hari ini.';
    } elseif ($waktuMulai >= $waktuSelesai) {
        $error = 'Waktu selesai harus lebih besar dari waktu mulai.';
    } else {
        // Check if room is available
        if (!$room->isRoomAvailable($ruanganId, $tanggal, $waktuMulai, $waktuSelesai)) {
            $error = 'Ruangan tidak tersedia pada waktu yang dipilih.';
        } else {
            // Check user access to room
            if (!canBookRoom($ruanganId)) {
                $error = 'Anda tidak memiliki akses untuk booking ruangan ini.';
            } else {
                // Create booking
                $bookingData = [
                    'ruangan_id' => $ruanganId,
                    'pemesan_id' => $user['id'],
                    'tanggal' => $tanggal,
                    'waktu_mulai' => $waktuMulai,
                    'waktu_selesai' => $waktuSelesai,
                    'keperluan_rapat' => $keperluanRapat,
                    'status' => hasRole(['Direktur']) ? 'Diterima' : 'Menunggu'
                ];
                
                if ($booking->createBooking($bookingData)) {
                    $status = $bookingData['status'];
                    $message = $status == 'Diterima' ? 
                        'Booking berhasil dibuat dan langsung diterima!' : 
                        'Booking berhasil dibuat dan menunggu approval.';
                    showAlert($message, 'success');
                    redirect('my-bookings.php');
                } else {
                    $error = 'Gagal membuat booking.';
                }
            }
        }
    }
}

$title = 'Booking Ruangan - Room Booking System';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Booking Ruangan</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Booking Ruangan</li>
        </ol>
    </nav>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-plus"></i> Form Booking Ruangan
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" id="bookingForm">
                    <input type="hidden" name="_token" value="<?php echo session()->token(); ?>" />
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ruangan_id" class="form-label">Ruangan <span class="text-danger">*</span></label>
                            <select class="form-select" id="ruangan_id" name="ruangan_id" required onchange="checkAvailability()">
                                <option value="">Pilih Ruangan</option>
                                <?php foreach ($rooms as $r): ?>
                                    <option value="<?= $r['id'] ?>" 
                                            data-capacity="<?= $r['kapasitas'] ?>"
                                            data-pic="<?= $r['penanggung_jawab'] ?>"
                                            <?= (isset($_POST['ruangan_id']) && $_POST['ruangan_id'] == $r['id']) ? 'selected' : '' ?>>
                                        <?= $r['nama_ruangan'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                   min="<?= date('Y-m-d') ?>" 
                                   value="<?= $_POST['tanggal'] ?? '' ?>" 
                                   required onchange="checkAvailability()">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="waktu_mulai" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai" 
                                   value="<?= $_POST['waktu_mulai'] ?? '' ?>" 
                                   required onchange="checkAvailability()">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="waktu_selesai" class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="waktu_selesai" name="waktu_selesai" 
                                   value="<?= $_POST['waktu_selesai'] ?? '' ?>" 
                                   required onchange="checkAvailability()">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keperluan_rapat" class="form-label">Keperluan Rapat <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="keperluan_rapat" name="keperluan_rapat" 
                                  rows="3" placeholder="Jelaskan keperluan rapat..." required><?= $_POST['keperluan_rapat'] ?? '' ?></textarea>
                    </div>
                    
                    <div id="availability-check" class="mb-3" style="display: none;">
                        <div class="alert" id="availability-alert"></div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check"></i> Buat Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Room Info -->
        <div class="card mb-3" id="room-info" style="display: none;">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i> Informasi Ruangan
                </h6>
            </div>
            <div class="card-body">
                <div id="room-details"></div>
            </div>
        </div>
        
        <!-- Available Rooms -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-door-open"></i> Ruangan Tersedia
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($rooms as $r): ?>
                        <div class="col-12 mb-2">
                            <div class="border rounded p-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= $r['nama_ruangan'] ?></strong><br>
                                        <small class="text-muted">
                                            <i class="bi bi-person"></i> <?= $r['kapasitas'] ?> orang<br>
                                            <i class="bi bi-person-badge"></i> <?= $r['penanggung_jawab'] ?>
                                        </small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="selectRoom(<?= $r['id'] ?>)">
                                        Pilih
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectRoom(roomId) {
    document.getElementById('ruangan_id').value = roomId;
    updateRoomInfo();
    checkAvailability();
}

function updateRoomInfo() {
    const select = document.getElementById('ruangan_id');
    const option = select.options[select.selectedIndex];
    const roomInfo = document.getElementById('room-info');
    const roomDetails = document.getElementById('room-details');
    
    if (option.value) {
        const capacity = option.dataset.capacity;
        const pic = option.dataset.pic;
        
        roomDetails.innerHTML = `
            <p class="mb-2"><strong>Ruangan:</strong> ${option.text}</p>
            <p class="mb-2"><strong>Kapasitas:</strong> ${capacity} orang</p>
            <p class="mb-0"><strong>PIC:</strong> ${pic}</p>
        `;
        roomInfo.style.display = 'block';
    } else {
        roomInfo.style.display = 'none';
    }
}

function checkAvailability() {
    const ruanganId = document.getElementById('ruangan_id').value;
    const tanggal = document.getElementById('tanggal').value;
    const waktuMulai = document.getElementById('waktu_mulai').value;
    const waktuSelesai = document.getElementById('waktu_selesai').value;
    
    if (ruanganId && tanggal && waktuMulai && waktuSelesai) {
        // In a real implementation, you would make an AJAX call here
        // For now, we'll just show a simple validation
        if (waktuMulai >= waktuSelesai) {
            showAvailabilityAlert('Waktu selesai harus lebih besar dari waktu mulai!', 'danger');
            document.getElementById('submitBtn').disabled = true;
        } else {
            showAvailabilityAlert('Memeriksa ketersediaan ruangan...', 'info');
            document.getElementById('submitBtn').disabled = false;
            
            // Simulate checking availability
            setTimeout(function() {
                showAvailabilityAlert('Ruangan tersedia untuk waktu yang dipilih!', 'success');
            }, 1000);
        }
    }
}

function showAvailabilityAlert(message, type) {
    const alertDiv = document.getElementById('availability-alert');
    const checkDiv = document.getElementById('availability-check');
    
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    checkDiv.style.display = 'block';
}

// Initialize room info on page load
document.getElementById('ruangan_id').addEventListener('change', updateRoomInfo);

// Initialize if there's a selected room
if (document.getElementById('ruangan_id').value) {
    updateRoomInfo();
}
</script>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>