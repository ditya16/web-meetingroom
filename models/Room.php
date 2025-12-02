<?php
class Room {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getAllRooms() {
        return $this->db->fetchAll("SELECT * FROM rooms WHERE status = 'Aktif' ORDER BY nama_ruangan");
    }
    
    public function getRoomById($id) {
        return $this->db->fetchOne("SELECT * FROM rooms WHERE id = ?", [$id]);
    }
    
    public function createRoom($data) {
        return $this->db->query(
            "INSERT INTO rooms (nama_ruangan, penanggung_jawab, kapasitas, fasilitas, status) VALUES (?, ?, ?, ?, ?)",
            [$data['nama_ruangan'], $data['penanggung_jawab'], $data['kapasitas'], $data['fasilitas'], $data['status']]
        );
    }
    
    public function updateRoom($id, $data) {
        return $this->db->query(
            "UPDATE rooms SET nama_ruangan = ?, penanggung_jawab = ?, kapasitas = ?, fasilitas = ?, status = ? WHERE id = ?",
            [$data['nama_ruangan'], $data['penanggung_jawab'], $data['kapasitas'], $data['fasilitas'], $data['status'], $id]
        );
    }
    
    public function deleteRoom($id) {
        return $this->db->query("DELETE FROM rooms WHERE id = ?", [$id]);
    }
    
    public function getAvailableRooms($date, $startTime, $endTime, $excludeBookingId = null) {
        $sql = "SELECT r.* FROM rooms r
                WHERE r.status = 'Aktif'
                AND r.id NOT IN (
                    SELECT DISTINCT b.ruangan_id
                    FROM bookings b
                    WHERE b.tanggal = ?
                    AND b.status IN ('Diterima')
                    AND (
                        (b.waktu_mulai < ? AND b.waktu_selesai > ?) OR
                        (b.waktu_mulai < ? AND b.waktu_selesai > ?) OR
                        (b.waktu_mulai >= ? AND b.waktu_selesai <= ?)
                    )";

        $params = [$date, $endTime, $startTime, $endTime, $startTime, $startTime, $endTime];

        if ($excludeBookingId) {
            $sql .= " AND b.id != ?";
            $params[] = $excludeBookingId;
        }

        $sql .= ") ORDER BY r.nama_ruangan";

        return $this->db->fetchAll($sql, $params);
    }
    
    public function isRoomAvailable($roomId, $date, $startTime, $endTime, $excludeBookingId = null) {
        $sql = "SELECT COUNT(*) as count FROM bookings
                WHERE ruangan_id = ?
                AND tanggal = ?
                AND status IN ('Diterima')
                AND (
                    (waktu_mulai < ? AND waktu_selesai > ?) OR
                    (waktu_mulai < ? AND waktu_selesai > ?) OR
                    (waktu_mulai >= ? AND waktu_selesai <= ?)
                )";

        $params = [$roomId, $date, $endTime, $startTime, $endTime, $startTime, $startTime, $endTime];

        if ($excludeBookingId) {
            $sql .= " AND id != ?";
            $params[] = $excludeBookingId;
        }

        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] == 0;
    }
    
    public function getRoomBookings($roomId, $date = null) {
        $sql = "SELECT b.*, u.nama as pemesan_nama 
                FROM bookings b 
                JOIN users u ON b.pemesan_id = u.id 
                WHERE b.ruangan_id = ?";
        $params = [$roomId];
        
        if ($date) {
            $sql .= " AND b.tanggal = ?";
            $params[] = $date;
        }
        
        $sql .= " ORDER BY b.tanggal DESC, b.waktu_mulai ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getUserAccessibleRooms($userRole) {
        return $this->db->fetchAll(
            "SELECT r.* FROM rooms r 
             JOIN role_access ra ON r.id = ra.ruangan_id 
             WHERE ra.role = ? AND ra.can_book = 1 AND r.status = 'Aktif'
             ORDER BY r.nama_ruangan",
            [$userRole]
        );
    }
}
?>