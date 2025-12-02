# 🎬 START HERE - Panduan Singkat

## 📌 Ringkas: Apa Yang Terjadi?

**Error:** `403 Forbidden - You don't have permission to access this resource`

**Solusi:** Ditambahkan sistem permission check yang proper dengan error page yang jelas.

---

## ⚡ 5 Menit Quick Start

### 1. Buka Debug Page (Untuk Verifikasi)
```
http://localhost/room/debug-permission.php
```

Apa yang Anda lihat:
- ✓ Status login
- ✓ Role Anda saat ini
- ✓ Semua user dalam database
- ✓ Test buttons untuk setiap halaman

### 2. Gunakan Test Credentials
```
Admin:
  Email: admin@ntp.co.id
  Password: admin123

Employee:
  Email: andi@ntp.co.id
  Password: andi123
```

### 3. Test Setiap Role
- Login sebagai Admin → Buka manage-bookings.php (✓ Bisa)
- Login sebagai Pegawai → Buka manage-bookings.php (✗ Forbidden)

### 4. Baca Dokumentasi
- Pengguna: **QUICK_START.md**
- Developer: **TECHNICAL.md**
- Tester: **CHECKLIST.md**

---

## 📂 Apa Saja File Baru?

### Dokumentasi (7 Files)
```
✓ QUICK_START.md           - Start here (user guide)
✓ SOLUSI_FORBIDDEN.md      - Penjelasan lengkap masalah
✓ PERMISSION_SYSTEM.md     - API & technical docs
✓ TECHNICAL.md             - Implementation details
✓ SUMMARY.md               - Overview semua changes
✓ CHECKLIST.md             - Testing guide
✓ ARCHITECTURE.md          - System architecture
```

### Tools
```
✓ debug-permission.php     - Debug utility
```

---

## 🔧 Perubahan Kode (Simple)

### File: `includes/functions.php`
**Ditambahkan:**
```php
function checkPermission($requiredRoles = []) {
    // Check if logged in
    // Check if role allowed
    // Show 403 if denied
    // Continue if allowed
}
```

### Files: Updated (3 Files)
```php
// manage-bookings.php
checkPermission(['Admin', 'Resepsionis']);

// bookings.php
checkPermission();

// my-bookings.php
checkPermission();
```

---

## 🎯 Fitur Utama

✅ **Role-Based Access Control**
- Check login status
- Check user role
- Allow or deny access

✅ **Friendly Error Page**
- Shows your role
- Shows required role
- Link back to dashboard

✅ **Debug Tool**
- Test access
- View all users
- Check database

✅ **Complete Documentation**
- For users
- For developers
- For testers

---

## 📊 Role & Access

| Page | Role Dibutuhkan | Anda (Pegawai) | Admin |
|------|-----------------|--|--|
| dashboard.php | Logged in | ✅ | ✅ |
| bookings.php | Logged in | ✅ | ✅ |
| my-bookings.php | Logged in | ✅ | ✅ |
| rooms.php | Logged in | ✅ | ✅ |
| manage-bookings.php | Admin/Resepsionis | ❌ | ✅ |

---

## 🧪 Testing

### Test 1: Admin Access
1. Buka: http://localhost/room/debug-permission.php
2. Cari user: admin@ntp.co.id
3. Klik login atau gunakan credentials
4. Buka manage-bookings.php
5. **Expected:** Halaman terbuka ✓

### Test 2: Pegawai Denied
1. Login sebagai andi@ntp.co.id
2. Buka manage-bookings.php
3. **Expected:** Error 403 ✗

### Test 3: Not Logged In
1. Logout
2. Buka bookings.php
3. **Expected:** Redirect ke login ✓

---

## 📖 Baca Dokumentasi

**Untuk User:**
```
1. QUICK_START.md       (Panduan cepat)
2. SOLUSI_FORBIDDEN.md  (Lengkap)
3. debug-permission.php (Test)
```

**Untuk Developer:**
```
1. TECHNICAL.md              (Implementation)
2. PERMISSION_SYSTEM.md      (API reference)
3. debug-permission.php      (Debug tool)
```

**Untuk Tester:**
```
1. CHECKLIST.md        (Testing guide)
2. debug-permission.php (Test tool)
3. QUICK_START.md      (User perspective)
```

---

## 🆘 Masalah Umum

**Q: Masih dapat 403?**
A: 
1. Buka debug page
2. Cek role Anda
3. Pastikan role sesuai dengan page requirements
4. Lihat role_access table di database

**Q: Login tidak bisa?**
A:
1. Cek email & password di debug page
2. Default: admin@ntp.co.id / admin123
3. Cek database connection

**Q: Ingin test?**
A:
1. Buka debug page
2. Lihat semua user
3. Gunakan test credentials
4. Klik test button untuk setiap page

---

## ✅ Verifikasi Instalasi

Run di terminal:
```bash
# Check PHP syntax
php -l "includes/functions.php"
php -l "bookings.php"
php -l "manage-bookings.php"
php -l "my-bookings.php"

# Expected: No syntax errors detected
```

---

## 🚀 Deployment Checklist

- [x] Code updated
- [x] PHP syntax valid
- [x] Documentation created
- [x] Debug tool ready
- [ ] Manual testing done
- [ ] Staging deployment
- [ ] Production deployment

---

## 💡 Remember

✨ **Key Points:**
1. Semua sudah siap untuk test
2. Gunakan debug page untuk verifikasi
3. Read QUICK_START.md untuk user guide
4. Read TECHNICAL.md untuk developer
5. Semua dokumentasi tersedia

---

## 📞 Quick Links

| Resource | Link/File |
|----------|-----------|
| **Debug Tool** | http://localhost/room/debug-permission.php |
| **User Guide** | QUICK_START.md |
| **API Docs** | PERMISSION_SYSTEM.md |
| **Tech Docs** | TECHNICAL.md |
| **Test Guide** | CHECKLIST.md |
| **Full Solution** | SOLUSI_FORBIDDEN.md |
| **Architecture** | ARCHITECTURE.md |

---

## 🎯 Next Actions

1. **Now:**
   - [ ] Buka debug-permission.php
   - [ ] Verifikasi sistem berjalan

2. **Today:**
   - [ ] Test dengan credentials yang berbeda
   - [ ] Verifikasi setiap page access

3. **This Week:**
   - [ ] Deploy ke staging
   - [ ] Run full test suite
   - [ ] Deploy ke production

---

**Status: ✅ READY FOR TESTING**

Start dengan debug page → Test dengan credentials → Read documentation

Questions? Check the documentation files! 📚
