# 🔒 Solusi - Error "Forbidden" pada Room Booking System

## Masalah yang Dihadapi
Error: **"Forbidden - You don't have permission to access this resource"**

Ini terjadi ketika user mencoba mengakses halaman yang tidak sesuai dengan role mereka.

---

## ✅ Solusi yang Diterapkan

### 1. Fungsi Permission Check Baru
Ditambahkan fungsi `checkPermission()` di file `includes/functions.php`:

```php
checkPermission($requiredRoles = []);
```

**Kegunaan:**
- Memeriksa apakah user sudah login
- Memverifikasi role user
- Menampilkan error 403 jika tidak punya akses
- Mengarahkan ke login jika belum login

### 2. Halaman yang Diperbarui
✅ `manage-bookings.php` - Hanya Admin & Resepsionis
✅ `bookings.php` - Hanya user yang sudah login  
✅ `my-bookings.php` - Hanya user yang sudah login
✅ `dashboard.php` - Sudah ada proteksi
✅ `rooms.php` - Sudah ada proteksi

### 3. Cara Penggunaan di Halaman Baru

**Untuk halaman yang butuh login saja:**
```php
<?php
require_once 'includes/functions.php';
checkPermission();
```

**Untuk halaman yang butuh role spesifik:**
```php
<?php
require_once 'includes/functions.php';
checkPermission(['Admin', 'Resepsionis']);
```

---

## 📋 Role dan Akses

| Role | Booking | Approve | Lihat Ruang | Kelola Booking |
|------|---------|---------|------------|----------------|
| **Direktur** | ✅ Semua | ✅ Ya | ✅ Ya | ✅ Ya |
| **Pegawai** | ✅ Kecuali BOD | ❌ Tidak | ✅ Ya | ❌ Tidak |
| **Admin** | ✅ Semua | ✅ Ya | ✅ Ya | ✅ Ya |
| **Resepsionis** | ✅ Semua | ✅ Ya | ✅ Ya | ✅ Ya |

---

## 🧪 Testing

### Test 1: User Tanpa Akses
1. Login sebagai `andi@ntp.co.id` (Pegawai)
2. Akses `manage-bookings.php`
3. **Hasil:** Muncul error 403 dengan pesan bahwa role ini tidak punya akses

### Test 2: User Dengan Akses
1. Login sebagai `admin@ntp.co.id` (Admin)
2. Akses `manage-bookings.php`
3. **Hasil:** Halaman terbuka dengan normal

### Test 3: User Belum Login
1. Akses halaman apapun tanpa login
2. **Hasil:** Otomatis redirect ke halaman login

---

## 📁 File yang Diubah

1. **`includes/functions.php`**
   - Menambah fungsi `checkPermission()`

2. **`manage-bookings.php`**
   - Update: `checkPermission(['Admin', 'Resepsionis']);`

3. **`bookings.php`**
   - Update: `checkPermission();`

4. **`my-bookings.php`**
   - Update: `checkPermission();`

---

## 📖 Dokumentasi Lengkap

Untuk dokumentasi lebih detail, lihat file:
**`PERMISSION_SYSTEM.md`**

---

## 🔧 Helper Functions yang Tersedia

```php
// Cek apakah user sudah login
isLoggedIn();

// Ambil data user yang login
getCurrentUser();

// Cek apakah user punya role tertentu
hasRole($roles);

// Cek permission (fungsi utama untuk 403)
checkPermission($requiredRoles = []);

// Arahkan ke halaman lain
redirect($url);

// Ambil user yang bisa book room
canBookRoom($roomId);
```

---

## 🛠️ Troubleshooting

**Q: Masih muncul Forbidden padahal seharusnya punya akses?**
A: Cek role user di database:
```sql
SELECT * FROM users WHERE email = 'your@email.com';
SELECT * FROM role_access WHERE role = 'YourRole';
```

**Q: Ingin tambah role baru?**
A: Update database:
```sql
-- 1. Tambah role
ALTER TABLE users MODIFY role ENUM(..., 'NewRole');

-- 2. Tambah aturan akses
INSERT INTO role_access (role, ruangan_id, can_book, can_approve, can_cancel) 
SELECT 'NewRole', id, TRUE, FALSE, FALSE FROM rooms;

-- 3. Gunakan di halaman
checkPermission(['NewRole']);
```

---

**Status:** ✅ Selesai  
**Tanggal:** 26 November 2025  
**Sistem:** Room Booking System
