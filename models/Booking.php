<?php
class Booking {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function createBooking($data) {
        return $this->db->query(
            "INSERT INTO bookings (ruangan_id, pemesan_id, tanggal, waktu_mulai, waktu_selesai, keperluan_rapat, status, catatan) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['ruangan_id'], 
                $data['pemesan_id'], 
                $data['tanggal'], 
                $data['waktu_mulai'], 
                $data['waktu_selesai'], 
                $data['keperluan_rapat'],
                $data['status'] ?? 'Menunggu',
                $data['catatan'] ?? ''
            ]
        );
    }
    
    public function getAllBookings($limit = null) {
        $sql = "SELECT b.*, r.nama_ruangan, u.nama as pemesan_nama, u.divisi
                FROM bookings b 
                JOIN rooms r ON b.ruangan_id = r.id 
                JOIN users u ON b.pemesan_id = u.id 
                ORDER BY b.tanggal DESC, b.waktu_mulai DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->fetchAll($sql);
    }
    
    public function getBookingById($id) {
        return $this->db->fetchOne(
            "SELECT b.*, r.nama_ruangan, u.nama as pemesan_nama, u.email as pemesan_email, u.divisi
             FROM bookings b 
             JOIN rooms r ON b.ruangan_id = r.id 
             JOIN users u ON b.pemesan_id = u.id 
             WHERE b.id = ?",
            [$id]
        );
    }
    
    public function updateBooking($id, $data) {
        return $this->db->query(
            "UPDATE bookings SET ruangan_id = ?, tanggal = ?, waktu_mulai = ?, waktu_selesai = ?, keperluan_rapat = ?, status = ?, catatan = ? WHERE id = ?",
            [
                $data['ruangan_id'], 
                $data['tanggal'], 
                $data['waktu_mulai'], 
                $data['waktu_selesai'], 
                $data['keperluan_rapat'],
                $data['status'],
                $data['catatan'] ?? '',
                $id
            ]
        );
    }
    
    public function updateBookingStatus($id, $status, $catatan = '') {
        return $this->db->query(
            "UPDATE bookings SET status = ?, catatan = ? WHERE id = ?",
            [$status, $catatan, $id]
        );
    }
    
    public function deleteBooking($id) {
        return $this->db->query("DELETE FROM bookings WHERE id = ?", [$id]);
    }
    
    public function getUserBookings($userId) {
        return $this->db->fetchAll(
            "SELECT b.*, r.nama_ruangan 
             FROM bookings b 
             JOIN rooms r ON b.ruangan_id = r.id 
             WHERE b.pemesan_id = ? 
             ORDER BY b.tanggal DESC, b.waktu_mulai DESC",
            [$userId]
        );
    }
    
    public function getTodayBookings() {
        $today = date('Y-m-d');
        return $this->db->fetchAll(
            "SELECT b.*, r.nama_ruangan, u.nama as pemesan_nama 
             FROM bookings b 
             JOIN rooms r ON b.ruangan_id = r.id 
             JOIN users u ON b.pemesan_id = u.id 
             WHERE b.tanggal = ? AND b.status = 'Diterima'
             ORDER BY b.waktu_mulai ASC",
            [$today]
        );
    }
    
    public function getUpcomingBookings($limit = 5) {
        $today = date('Y-m-d');
        return $this->db->fetchAll(
            "SELECT b.*, r.nama_ruangan, u.nama as pemesan_nama 
             FROM bookings b 
             JOIN rooms r ON b.ruangan_id = r.id 
             JOIN users u ON b.pemesan_id = u.id 
             WHERE b.tanggal >= ? AND b.status = 'Diterima'
             ORDER BY b.tanggal ASC, b.waktu_mulai ASC 
             LIMIT ?",
            [$today, $limit]
        );
    }
    
    public function getPendingBookings() {
        return $this->db->fetchAll(
            "SELECT b.*, r.nama_ruangan, u.nama as pemesan_nama, u.email as pemesan_email 
             FROM bookings b 
             JOIN rooms r ON b.ruangan_id = r.id 
             JOIN users u ON b.pemesan_id = u.id 
             WHERE b.status = 'Menunggu'
             ORDER BY b.created_at ASC"
        );
    }
    
    public function getBookingsByDate($date) {
        return $this->db->fetchAll(
            "SELECT b.*, r.nama_ruangan, u.nama as pemesan_nama 
             FROM bookings b 
             JOIN rooms r ON b.ruangan_id = r.id 
             JOIN users u ON b.pemesan_id = u.id 
             WHERE b.tanggal = ? AND b.status IN ('Diterima', 'Menunggu')
             ORDER BY b.waktu_mulai ASC",
            [$date]
        );
    }
    
    public function getBookingStats() {
        $stats = [];
        
        // Total bookings
        $result = $this->db->fetchOne("SELECT COUNT(*) as total FROM bookings");
        $stats['total'] = $result['total'];
        
        // Pending bookings
        $result = $this->db->fetchOne("SELECT COUNT(*) as pending FROM bookings WHERE status = 'Menunggu'");
        $stats['pending'] = $result['pending'];
        
        // Today's bookings
        $today = date('Y-m-d');
        $result = $this->db->fetchOne("SELECT COUNT(*) as today FROM bookings WHERE tanggal = ? AND status = 'Diterima'", [$today]);
        $stats['today'] = $result['today'];
        
        // This month's bookings
        $thisMonth = date('Y-m');
        $result = $this->db->fetchOne("SELECT COUNT(*) as month FROM bookings WHERE DATE_FORMAT(tanggal, '%Y-%m') = ? AND status = 'Diterima'", [$thisMonth]);
        $stats['month'] = $result['month'];
        
        return $stats;
    }
    
    public function hasConflict($roomId, $date, $startTime, $endTime, $excludeBookingId = null) {
        $sql = "SELECT COUNT(*) as count FROM bookings 
                WHERE ruangan_id = ? 
                AND tanggal = ? 
                AND status IN ('Diterima', 'Menunggu')
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
        return $result['count'] > 0;
    }
}
?>