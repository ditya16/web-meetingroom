-- Room Booking System Database
-- Created: October 22, 2025

CREATE DATABASE IF NOT EXISTS room_booking;
USE room_booking;

-- Table for Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Direktur', 'Pegawai', 'Admin', 'Resepsionis') NOT NULL DEFAULT 'Pegawai',
    divisi VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for Rooms
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_ruangan VARCHAR(100) NOT NULL,
    penanggung_jawab VARCHAR(100) NOT NULL,
    kapasitas INT DEFAULT 10,
    fasilitas TEXT,
    status ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for Bookings
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ruangan_id INT NOT NULL,
    pemesan_id INT NOT NULL,
    tanggal DATE NOT NULL,
    waktu_mulai TIME NOT NULL,
    waktu_selesai TIME NOT NULL,
    keperluan_rapat TEXT NOT NULL,
    status ENUM('Menunggu', 'Diterima', 'Ditolak', 'Selesai') DEFAULT 'Menunggu',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ruangan_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (pemesan_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table for Role Access Rules
CREATE TABLE role_access (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('Direktur', 'Pegawai', 'Admin', 'Resepsionis') NOT NULL,
    ruangan_id INT,
    can_book BOOLEAN DEFAULT TRUE,
    can_approve BOOLEAN DEFAULT FALSE,
    can_cancel BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (ruangan_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- Insert sample rooms data
INSERT INTO rooms (nama_ruangan, penanggung_jawab, kapasitas, fasilitas) VALUES
('Ruang Customer 1', 'Resepsionis', 6, 'Proyektor, Whiteboard, AC'),
('Ruang Customer 2', 'Resepsionis', 6, 'Proyektor, Whiteboard, AC'),
('Ruang Customer 3', 'Resepsionis', 6, 'Proyektor, Whiteboard, AC'),
('Ruang Customer 4', 'Resepsionis', 6, 'Proyektor, Whiteboard, AC'),
('Ruang BOD', 'Sekder', 12, 'LED TV, Sound System, AC, Meja Besar'),
('Ruang Meet B', '-', 8, 'Proyektor, Whiteboard, AC'),
('War Room', 'QA', 10, 'Multiple Monitor, Whiteboard, AC'),
('Ruang Training 1', 'HC', 20, 'Proyektor, Sound System, AC, Kursi Training'),
('Ruang Training 2', 'HC', 20, 'Proyektor, Sound System, AC, Kursi Training');

-- Insert sample users
INSERT INTO users (nama, email, password, role, divisi) VALUES
('Admin System', 'admin@ntp.co.id', MD5('admin123'), 'Admin', 'IT'),
('Resepsionis 1', 'resepsionis@ntp.co.id', MD5('resepsionis123'), 'Resepsionis', 'Resepsionis'),
('Direktur Utama', 'direktur@ntp.co.id', MD5('direktur123'), 'Direktur', 'Direksi'),
('Andi Marketing', 'andi@ntp.co.id', MD5('andi123'), 'Pegawai', 'Marketing'),
('Budi QA', 'budi@ntp.co.id', MD5('budi123'), 'Pegawai', 'QA'),
('Sari HC', 'sari@ntp.co.id', MD5('sari123'), 'Pegawai', 'HC');

-- Insert role access rules
-- Direktur can book all rooms
INSERT INTO role_access (role, ruangan_id, can_book, can_approve, can_cancel) 
SELECT 'Direktur', id, TRUE, TRUE, TRUE FROM rooms;

-- Pegawai can book all rooms except Ruang BOD
INSERT INTO role_access (role, ruangan_id, can_book, can_approve, can_cancel) 
SELECT 'Pegawai', id, TRUE, FALSE, FALSE FROM rooms WHERE nama_ruangan != 'Ruang BOD';

-- Resepsionis can book all rooms for others
INSERT INTO role_access (role, ruangan_id, can_book, can_approve, can_cancel) 
SELECT 'Resepsionis', id, TRUE, TRUE, TRUE FROM rooms;

-- Admin can manage everything
INSERT INTO role_access (role, ruangan_id, can_book, can_approve, can_cancel) 
SELECT 'Admin', id, TRUE, TRUE, TRUE FROM rooms;

-- Insert sample bookings
INSERT INTO bookings (ruangan_id, pemesan_id, tanggal, waktu_mulai, waktu_selesai, keperluan_rapat, status) VALUES
(6, 4, '2025-08-01', '09:00:00', '11:00:00', 'Presentasi Produk', 'Diterima'),
(7, 5, '2025-08-01', '13:00:00', '15:00:00', 'Review QC', 'Menunggu');