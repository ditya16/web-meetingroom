# Room Booking System

Sistem booking ruangan meeting dengan PHP dan MySQL yang mendukung role-based access control.

## Features

- **User Management**: Login dengan role Direktur, Pegawai, Admin, dan Resepsionis
- **Room Management**: Kelola data ruangan meeting
- **Booking System**: Booking ruangan dengan deteksi konflik waktu
- **Role-based Access**: Akses ruangan berdasarkan role user
- **Approval Workflow**: Sistem approval untuk booking
- **Dashboard**: Overview booking dan statistik

## Installation

### Prerequisites
- XAMPP/WAMP (PHP 7.4+, MySQL, Apache)
- Web browser

### Setup Steps

1. **Clone/Copy project ke htdocs**
   ```
   c:\xampp\htdocs\room\
   ```

2. **Start XAMPP**
   - Start Apache
   - Start MySQL

3. **Create Database**
   - Buka phpMyAdmin (http://localhost/phpmyadmin)
   - Import file `database/room_booking.sql`
   - Atau jalankan script SQL yang ada di file tersebut

4. **Configure Database**
   - Edit `config/config.php` jika perlu mengubah setting database
   
5. **Access Application**
   - Buka browser: http://localhost/room

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@ntp.co.id | admin123 |
| Direktur | direktur@ntp.co.id | direktur123 |
| Resepsionis | resepsionis@ntp.co.id | resepsionis123 |
| Pegawai | andi@ntp.co.id | andi123 |

## File Structure

```
room/
├── config/
│   └── config.php              # Database & app configuration
├── database/
│   └── room_booking.sql        # Database schema & sample data
├── includes/
│   ├── Database.php            # Database connection class
│   ├── functions.php           # Helper functions
│   └── layout.php              # Main layout template
├── models/
│   ├── User.php                # User model
│   ├── Room.php                # Room model
│   └── Booking.php             # Booking model
├── index.php                   # Login page
├── dashboard.php               # Main dashboard
├── bookings.php                # Create new booking
├── my-bookings.php             # User's bookings
├── rooms.php                   # Room list
├── manage-bookings.php         # Manage all bookings (Admin/Resepsionis)
├── manage-users.php            # User management (Admin)
├── manage-rooms.php            # Room management (Admin)
└── logout.php                  # Logout handler
```

## Database Schema

### Tables
- `users`: User data dengan role-based access
- `rooms`: Data ruangan meeting
- `bookings`: Data booking/pemesanan ruangan
- `role_access`: Aturan akses ruangan per role

### Role Access Rules
- **Direktur**: Bisa booking semua ruangan, auto-approve
- **Pegawai**: Bisa booking semua ruangan kecuali Ruang BOD, butuh approval
- **Resepsionis**: Bisa booking semua ruangan atas nama orang lain, bisa approve
- **Admin**: Full access ke semua fitur

## Features Detail

### 1. User Authentication
- Session-based login
- Role-based menu display
- Password encryption dengan MD5

### 2. Booking System
- Real-time availability checking
- Conflict detection untuk waktu booking
- Status tracking (Menunggu, Diterima, Ditolak, Selesai)
- Auto-approval untuk role tertentu

### 3. Dashboard
- Statistics overview
- Today's bookings
- Upcoming bookings
- Pending approvals (untuk Admin/Resepsionis)

### 4. Room Management
- CRUD operations untuk ruangan
- Capacity & facilities management
- Room availability status

### 5. User Management (Admin only)
- Add/edit/delete users
- Role assignment
- Department management

## Customization

### Adding New Roles
1. Update ENUM di table `users.role`
2. Add role access rules di table `role_access`
3. Update helper functions di `includes/functions.php`

### Adding New Room Types
1. Tambah data di table `rooms`
2. Set access rules di table `role_access`

### Email Notifications
Bisa ditambahkan email notification untuk:
- Booking confirmation
- Approval/rejection notifications
- Booking reminders

## Browser Support
- Chrome/Chromium
- Firefox
- Safari
- Edge

## Security Notes
- Change default passwords setelah installation
- Update database credentials di production
- Enable HTTPS di production environment
- Consider using stronger password hashing (bcrypt)

## Support
Untuk pertanyaan atau issue, silakan buat ticket di repository ini.