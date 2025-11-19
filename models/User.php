<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function login($email, $password) {
        $hashedPassword = md5($password);
        $user = $this->db->fetchOne(
            "SELECT * FROM users WHERE email = ? AND password = ?", 
            [$email, $hashedPassword]
        );
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_divisi'] = $user['divisi'];
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        session_destroy();
    }
    
    public function register($data) {
        $hashedPassword = md5($data['password']);
        
        $result = $this->db->query(
            "INSERT INTO users (nama, email, password, role, divisi) VALUES (?, ?, ?, ?, ?)",
            [$data['nama'], $data['email'], $hashedPassword, $data['role'], $data['divisi']]
        );
        
        return $result;
    }
    
    public function getAllUsers() {
        return $this->db->fetchAll("SELECT id, nama, email, role, divisi, created_at FROM users ORDER BY nama");
    }
    
    public function getUserById($id) {
        return $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    }
    
    public function updateUser($id, $data) {
        $sql = "UPDATE users SET nama = ?, email = ?, role = ?, divisi = ?";
        $params = [$data['nama'], $data['email'], $data['role'], $data['divisi']];
        
        if (!empty($data['password'])) {
            $sql .= ", password = ?";
            $params[] = md5($data['password']);
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        return $this->db->query($sql, $params);
    }
    
    public function deleteUser($id) {
        return $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
    }
    
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] > 0;
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
}
?>