# 🚀 QUICK START - Solusi Error Forbidden

## Problem
```
403 Forbidden
You don't have permission to access this resource.
```

## ✅ Solusi Cepat

### Step 1: Verifikasi Login
Pastikan Anda sudah login dengan role yang tepat. 

**Test Credentials:**
- Email: `admin@ntp.co.id`
- Password: `admin123`

### Step 2: Debug
Buka halaman debug (untuk developer saja):
```
http://localhost/room/debug-permission.php
```

Halaman ini menampilkan:
- ✓ Status login Anda
- ✓ Role Anda  
- ✓ Semua user di database
- ✓ Test akses ke setiap halaman

### Step 3: Identifikasi Masalah

| Problem | Solution |
|---------|----------|
| Error 403 di `manage-bookings.php` | Login sebagai Admin atau Resepsionis |
| Error 403 di halaman lain | Cek apakah Anda sudah login |
| Tidak bisa login | Gunakan credentials di atas atau hubungi Admin |

---

## 📝 Halaman yang Dilindungi

| Halaman | Butuh Login | Role Khusus |
|---------|-----------|------------|
| `bookings.php` | ✅ Ya | ❌ Tidak |
| `my-bookings.php` | ✅ Ya | ❌ Tidak |
| `dashboard.php` | ✅ Ya | ❌ Tidak |
| `rooms.php` | ✅ Ya | ❌ Tidak |
| `manage-bookings.php` | ✅ Ya | ✅ Admin/Resepsionis |
| `index.php` | ❌ Tidak | ❌ Tidak |
| `logout.php` | ❌ Tidak | ❌ Tidak |

---

## 🔐 Role dan Akses

### Admin / Resepsionis
- ✅ Bisa akses `manage-bookings.php`
- ✅ Bisa approve/reject booking
- ✅ Bisa lihat semua booking

### Direktur
- ✅ Bisa booking semua ruangan
- ✅ Bisa approve booking sendiri
- ✅ Bisa lihat semua booking

### Pegawai
- ✅ Bisa booking (kecuali Ruang BOD)
- ❌ Tidak bisa approve booking orang lain
- ✅ Bisa lihat booking sendiri

---

## 💡 Pro Tips

1. **Gunakan Debug Page untuk test:**
   ```
   http://localhost/room/debug-permission.php
   ```

2. **Lihat user role di debug page**
   Semua user ditampilkan dengan role mereka

3. **Test setiap halaman dari debug page**
   Klik tombol test untuk coba akses setiap halaman

4. **Lihat dokumentasi lengkap:**
   - `PERMISSION_SYSTEM.md` - Dokumentasi teknis
   - `SOLUSI_FORBIDDEN.md` - Penjelasan lengkap

---

## ❓ FAQ

**Q: Kenapa saya kena error Forbidden?**
A: Anda tidak punya role yang dibutuhkan untuk akses halaman tersebut. Lihat tabel Role dan Akses di atas.

**Q: Bagaimana cara login?**
A: Klik "Login" di halaman utama dan gunakan email + password dari database.

**Q: Siapa admin default?**
A: Email: `admin@ntp.co.id`, Password: `admin123`

**Q: Bagaimana cara tambah user baru?**
A: Hubungi Admin atau edit database users table langsung.

**Q: Ingin test akses user berbeda?**
A: Logout, login dengan user lain, lalu coba akses halaman yang berbeda.

---

## 🆘 Jika Masih Error

1. Buka: `http://localhost/room/debug-permission.php`
2. Cek apakah Anda sudah login ✓
3. Cek role Anda sesuai dengan halaman yang diakses ✓
4. Klik tombol "Test" untuk setiap halaman

Jika masih error, hubungi developer dengan screenshot dari debug page.

---

**Happy Booking! 🎉**
