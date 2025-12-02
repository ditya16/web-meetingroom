# Room Booking System - Permission System Documentation

## Problem: "Forbidden - You don't have permission to access this resource"

This error occurs when a user tries to access a page they don't have permission for based on their role.

## Solution Implemented

I've added a comprehensive permission checking system with the `checkPermission()` function in `includes/functions.php`.

### How It Works

#### 1. **Permission Check Function** (`checkPermission()`)
```php
checkPermission($requiredRoles = [])
```

**Parameters:**
- `$requiredRoles` (array|string, optional): The role(s) allowed to access the page
  - If empty: Only requires user to be logged in
  - If specified: User must have one of the specified roles

**Behavior:**
- If user is not logged in → Redirects to login page
- If user's role is not allowed → Shows 403 Forbidden error with details
- If permission granted → Script continues normally

#### 2. **Updated Pages**

All pages that need permission checks have been updated:

| Page | Permission Required | Function |
|------|-------------------|----------|
| `bookings.php` | Logged in | Make new bookings |
| `my-bookings.php` | Logged in | View own bookings |
| `manage-bookings.php` | Admin, Resepsionis | Approve/reject bookings |
| `dashboard.php` | Logged in | View dashboard |
| `rooms.php` | Logged in | View room list |

### Role-Based Access

**Available Roles:**
- `Direktur` (Director) - Full access to all rooms and features
- `Pegawai` (Employee) - Limited access (can't access Ruang BOD)
- `Admin` - Full system management
- `Resepsionis` (Receptionist) - Can manage bookings for all rooms

### How to Use

#### Example 1: Check if user is simply logged in
```php
<?php
require_once 'includes/functions.php';

checkPermission();

// Page content here
```

#### Example 2: Restrict to specific roles
```php
<?php
require_once 'includes/functions.php';

// Only Admin and Resepsionis can access this page
checkPermission(['Admin', 'Resepsionis']);

// Page content here
```

#### Example 3: Single role check
```php
<?php
require_once 'includes/functions.php';

// Only Admin can access
checkPermission('Admin');

// Page content here
```

## Database Permission Rules

The `role_access` table defines what each role can do:

```sql
SELECT * FROM role_access;
```

**Columns:**
- `role` - User role (Direktur, Pegawai, Admin, Resepsionis)
- `ruangan_id` - Room ID (NULL = applies to all rooms)
- `can_book` - Can create bookings (TRUE/FALSE)
- `can_approve` - Can approve/reject bookings (TRUE/FALSE)
- `can_cancel` - Can cancel bookings (TRUE/FALSE)

### Current Access Rules

**Direktur (Director):**
- Can book all rooms ✓
- Can approve bookings ✓
- Can cancel bookings ✓

**Pegawai (Employee):**
- Can book all rooms except Ruang BOD ✓
- Cannot approve ✗
- Cannot cancel ✗

**Resepsionis (Receptionist):**
- Can book all rooms ✓
- Can approve bookings ✓
- Can cancel bookings ✓

**Admin:**
- Can book all rooms ✓
- Can approve bookings ✓
- Can cancel bookings ✓

## Error Page Features

When a user doesn't have permission, they see a friendly error page showing:
- Clear error message (403 Forbidden)
- Their current role
- Required role(s) to access the page
- Link back to dashboard

## Testing

### Test Case 1: Unauthorized Access
1. Login as `andi@ntp.co.id` (Pegawai role)
2. Try to access `manage-bookings.php`
3. Expected: 403 Forbidden error with message

### Test Case 2: Authorized Access
1. Login as `admin@ntp.co.id` (Admin role)
2. Access `manage-bookings.php`
3. Expected: Page loads normally

### Test Case 3: Non-logged-in User
1. Clear browser cookies/logout
2. Try to access any protected page (except index.php)
3. Expected: Redirected to login page

## Helper Functions Available

```php
// Check if user is logged in
isLoggedIn()

// Get current logged-in user data
getCurrentUser()

// Check if user has specific role(s)
hasRole($roles)

// Check permission (this is what does the 403 check)
checkPermission($requiredRoles = [])

// Redirect user to another page
redirect($url)
```

## Troubleshooting

### Issue: Still seeing "Forbidden" on pages I should access
- **Solution:** Check your user's role in the database: `SELECT * FROM users WHERE email = 'your@email.com';`
- Verify role access rules: `SELECT * FROM role_access WHERE role = 'YourRole';`

### Issue: Login works but dashboard is forbidden
- **Solution:** Update the database entries for your role in the `role_access` table

### Issue: Need to add new page with permissions
- **Solution:** Add at the top of your PHP file:
```php
<?php
require_once 'includes/functions.php';
checkPermission(['YourRole']);
```

## Adding New Roles or Permissions

### Step 1: Add role to users table
```sql
ALTER TABLE users MODIFY role ENUM('Direktur', 'Pegawai', 'Admin', 'Resepsionis', 'NewRole');
```

### Step 2: Add access rules
```sql
INSERT INTO role_access (role, ruangan_id, can_book, can_approve, can_cancel) 
SELECT 'NewRole', id, TRUE, FALSE, FALSE FROM rooms;
```

### Step 3: Use in checkPermission
```php
checkPermission(['NewRole']);
```

---

**Version:** 1.0  
**Last Updated:** November 26, 2025  
**System:** Room Booking System
