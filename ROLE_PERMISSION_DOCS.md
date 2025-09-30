# Role & Permission System Documentation

## Implementasi Method untuk Model User dan Role

Berikut adalah dokumentasi method-method yang telah diimplementasikan untuk sistem Role & Permission:

## Model User

### Method Pengecekan Permission
```php
// Cek apakah user memiliki permission tertentu
$user->hasPermission('view_users'); // true/false

// Cek apakah user memiliki salah satu dari beberapa permission
$user->hasAnyPermission(['view_users', 'edit_users']); // true/false

// Cek apakah user memiliki semua permission yang disebutkan  
$user->hasAllPermissions(['view_users', 'edit_users']); // true/false
```

### Method Pengecekan Role
```php
// Cek apakah user memiliki role tertentu
$user->hasRole('admin'); // true/false

// Mendapatkan nama role user
$user->getRoleName(); // 'admin' atau null

// Mendapatkan collection permissions user
$user->getPermissions(); // Collection of permissions
```

### Method Assign/Revoke Role
```php
// Assign role ke user (akan update/replace role sebelumnya jika ada)
$user->assignRole('admin');

// Revoke role tertentu dari user
$user->revokeRole('admin');

// Revoke semua role dari user
$user->revokeAllRoles();
```

## Model Role

### Method Pengecekan Permission
```php
// Cek apakah role memiliki permission tertentu
$role->hasPermission('view_users'); // true/false

// Cek apakah role memiliki salah satu dari beberapa permission
$role->hasAnyPermission(['view_users', 'edit_users']); // true/false

// Cek apakah role memiliki semua permission yang disebutkan
$role->hasAllPermissions(['view_users', 'edit_users']); // true/false
```

### Method Give/Revoke Permission
```php
// Berikan permission ke role
$role->givePermissionTo('view_users');

// Berikan multiple permissions sekaligus
$role->giveMultiplePermissions(['view_users', 'edit_users']);

// Cabut permission dari role
$role->revokePermissionTo('view_users');

// Cabut multiple permissions sekaligus
$role->revokeMultiplePermissions(['view_users', 'edit_users']);

// Sync permissions (replace semua permission dengan yang baru)
$role->syncPermissions(['view_users', 'create_users']);

// Cabut semua permissions dari role
$role->revokeAllPermissions();
```

## Model Permission

### Method Tambahan
```php
// Cek apakah permission di-assign ke role tertentu
$permission->isAssignedToRole('admin'); // true/false

// Dapatkan users yang memiliki permission ini melalui role mereka
$permission->users(); // Query builder untuk users

// Scope: Filter by group
Permission::byGroup('user_management')->get();

// Scope: Filter by name pattern
Permission::byName('user')->get(); // Semua permission yang mengandung kata 'user'
```

## Contoh Penggunaan

### 1. Setup Role dan Permission
```php
// Buat role
$adminRole = Role::create([
    'name' => 'admin',
    'display_name' => 'Administrator',
    'description' => 'Full access'
]);

// Berikan permissions ke role
$adminRole->givePermissionTo('view_users');
$adminRole->givePermissionTo('create_users');
$adminRole->givePermissionTo('edit_users');

// Atau berikan multiple sekaligus
$adminRole->giveMultiplePermissions([
    'view_users', 'create_users', 'edit_users', 'delete_users'
]);
```

### 2. Assign Role ke User
```php
$user = User::find(1);

// Assign role (akan replace role lama jika ada)
$user->assignRole('admin');

// Cek permission
if ($user->hasPermission('edit_users')) {
    // User dapat mengedit users
}

// Cek multiple permissions
if ($user->hasAnyPermission(['edit_users', 'delete_users'])) {
    // User memiliki salah satu permission
}
```

### 3. Update Role Permission
```php
$role = Role::where('name', 'admin')->first();

// Tambah permission baru
$role->givePermissionTo('manage_settings');

// Hapus permission
$role->revokePermissionTo('delete_users');

// Replace semua permission
$role->syncPermissions(['view_users', 'edit_users']);
```

## Catatan Penting

1. **Behavior assignRole():** Jika user sudah memiliki role sebelumnya, role akan di-update/replace (sesuai permintaan tim)

2. **Exception Handling:** Method akan throw exception jika role/permission tidak ditemukan

3. **Database Structure:** 
   - Table `users` memiliki kolom `role_id` (belongsTo relationship)
   - Table `role_permissions` adalah pivot table untuk many-to-many relationship

4. **Testing:** Semua method sudah ditest dan berjalan dengan baik ✅

## Status Implementasi
- ✅ Method pengecekan permission untuk User
- ✅ `$user->assignRole($roleName)` dengan logic update/replace
- ✅ `$user->revokeRole($roleName)`  
- ✅ `$role->givePermissionTo($permissionName)`
- ✅ `$role->revokePermissionTo($permissionName)`
- ✅ Method tambahan untuk fleksibilitas development
- ✅ Unit testing completed

Bagian **Orang 2 (Tim Fondasi)** sudah selesai! 🚀