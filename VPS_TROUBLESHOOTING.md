# TROUBLESHOOTING: File >1MB Tidak Muncul di VPS

## Problem

-   File >1MB bisa di-upload
-   File masuk ke database
-   Tapi **TIDAK MUNCUL** di halaman admin

## Root Causes & Solutions

### 1. **Storage Link Tidak Ada** ❌

**Cek:**

```bash
ls -la /path/to/laravel/public/storage
```

**Fix:**

```bash
cd /path/to/laravel
php artisan storage:link
```

Harusnya ada symbolic link: `public/storage -> ../storage/app/public`

---

### 2. **File Permissions Salah** ❌

**Cek:**

```bash
ls -la storage/app/public/task-proofs/
```

**Fix:**

```bash
# Set ownership (sesuaikan dengan web server user)
sudo chown -R www-data:www-data storage/
sudo chown -R www-data:www-data bootstrap/cache/

# Set permissions
sudo chmod -R 775 storage/
sudo chmod -R 775 bootstrap/cache/

# Atau jika menggunakan user lain (misal: forge, ubuntu)
sudo chown -R forge:forge storage/ bootstrap/cache/
sudo chmod -R 775 storage/ bootstrap/cache/
```

---

### 3. **PHP-FPM & Nginx Configuration** ❌

#### A. PHP-FPM

**File:** `/etc/php/8.x/fpm/php.ini`

```ini
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 512M
max_execution_time = 300
max_input_time = 300
```

**Restart:**

```bash
sudo systemctl restart php8.2-fpm  # Sesuaikan versi PHP
```

#### B. Nginx

**File:** `/etc/nginx/sites-available/your-site.conf`

```nginx
server {
    # ... existing config

    client_max_body_size 100M;  # ADD THIS

    location ~ \.php$ {
        # ... existing php config
    }
}
```

**Test & Restart:**

```bash
sudo nginx -t
sudo systemctl restart nginx
```

---

### 4. **MySQL Packet Size** ❌

**File:** `/etc/mysql/mysql.conf.d/mysqld.cnf`

```ini
[mysqld]
max_allowed_packet = 64M
```

**Restart:**

```bash
sudo systemctl restart mysql
```

---

### 5. **Laravel Storage Disk Configuration** ❌

**File:** `config/filesystems.php`

```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',  // PASTIKAN INI BENAR!
    'visibility' => 'public',
    'throw' => false,
],
```

**File:** `.env`

```env
APP_URL=https://your-domain.com  # TANPA trailing slash!
```

**Clear cache:**

```bash
php artisan config:clear
php artisan cache:clear
```

---

### 6. **Selinux (CentOS/RHEL)** ❌

Jika menggunakan CentOS/RHEL, SELinux mungkin block akses:

```bash
# Check SELinux status
getenforce

# Set context untuk storage
sudo chcon -R -t httpd_sys_rw_content_t storage/

# Atau disable SELinux (not recommended for production)
sudo setenforce 0
```

---

## Quick Fix Script

Jalankan script ini di VPS:

```bash
# 1. Upload script ke VPS
scp vps_fix_upload.sh user@your-vps:/path/to/laravel/

# 2. SSH ke VPS
ssh user@your-vps

# 3. Masuk ke folder Laravel
cd /path/to/laravel

# 4. Jalankan script
sudo chmod +x vps_fix_upload.sh
sudo ./vps_fix_upload.sh

# 5. Laravel specific fixes
php artisan storage:link
php artisan config:clear
php artisan cache:clear
php artisan migrate

# 6. Set permissions
sudo chown -R www-data:www-data storage/ bootstrap/cache/
sudo chmod -R 775 storage/ bootstrap/cache/
```

---

## Manual Verification Steps

### Step 1: Check Storage Link

```bash
ls -la public/storage
# Should show: storage -> ../storage/app/public
```

### Step 2: Check File Exists

```bash
ls -la storage/app/public/task-proofs/*/verification-*/
# Should show uploaded files
```

### Step 3: Check File URL

Buka browser, coba akses:

```
https://your-domain.com/storage/task-proofs/9/verification-1/filename.png
```

**Harusnya muncul gambar!**

### Step 4: Check PHP Info

Buat file `public/info.php`:

```php
<?php phpinfo(); ?>
```

Akses: `https://your-domain.com/info.php`

Cari:

-   `upload_max_filesize` → should be 100M
-   `post_max_size` → should be 100M
-   `memory_limit` → should be 512M

**JANGAN LUPA DELETE `info.php` SETELAH CEK!**

### Step 5: Check Nginx Access Log

```bash
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log
```

Upload file, lalu cek log. Harusnya ada request ke `/storage/task-proofs/...`

### Step 6: Check Laravel Log

```bash
tail -f storage/logs/laravel.log
```

---

## Common Issues

### Issue 1: "404 Not Found" untuk gambar

**Cause:** Storage link tidak ada

**Fix:**

```bash
php artisan storage:link
```

### Issue 2: "403 Forbidden" untuk gambar

**Cause:** File permissions salah

**Fix:**

```bash
sudo chmod -R 775 storage/
sudo chown -R www-data:www-data storage/
```

### Issue 3: Gambar tidak load (blank/broken image)

**Cause 1:** APP_URL salah di `.env`

```env
# SALAH:
APP_URL=https://your-domain.com/

# BENAR:
APP_URL=https://your-domain.com
```

**Cause 2:** Storage URL salah di `filesystems.php`

```php
'url' => env('APP_URL').'/storage',
```

After fix:

```bash
php artisan config:clear
php artisan cache:clear
```

### Issue 4: Upload berhasil tapi data tidak masuk database

**Cause:** Migration belum dijalankan

**Fix:**

```bash
php artisan migrate --force
```

Pastikan kolom `verification_1_files` dan `verification_2_files` ada di tabel `user_tasks`.

---

## Environment Variables Check

**.env file harus benar:**

```env
APP_URL=https://your-domain.com  # TANPA trailing slash!
APP_ENV=production
APP_DEBUG=false  # IMPORTANT: set false di production!

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_user
DB_PASSWORD=your_password

FILESYSTEM_DISK=public  # Pastikan ini 'public'
```

**After update .env:**

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## Testing

### Test 1: Manual File Upload via SSH

```bash
# Create test file
cd storage/app/public/task-proofs
mkdir -p 999/verification-1
echo "test" > 999/verification-1/test.txt

# Check if accessible via URL
curl https://your-domain.com/storage/task-proofs/999/verification-1/test.txt
# Should return: test
```

### Test 2: Via Laravel Tinker

```bash
php artisan tinker

# Test Storage
>>> Storage::disk('public')->put('test.txt', 'Hello World');
>>> Storage::disk('public')->exists('test.txt');
>>> Storage::url('test.txt');

# Test URL generation
>>> $url = Storage::url('task-proofs/9/verification-1/file.jpg');
>>> echo $url;
```

### Test 3: Check Database

```bash
php artisan tinker

# Check if files are stored correctly
>>> $task = \App\Models\UserTask::find(9);
>>> $task->verification_1_files;  // Should return array
>>> print_r($task->verification_1_files);
```

---

## Final Checklist

-   [ ] `php artisan storage:link` dijalankan
-   [ ] Permissions: `storage/` = 775, owner = www-data
-   [ ] PHP settings: upload_max_filesize = 100M
-   [ ] Nginx: client_max_body_size = 100M
-   [ ] MySQL: max_allowed_packet = 64M
-   [ ] `.env`: APP_URL benar (tanpa trailing slash)
-   [ ] Migration dijalankan: kolom `verification_x_files` ada
-   [ ] Config cache cleared: `php artisan config:clear`
-   [ ] Services restarted: PHP-FPM, Nginx, MySQL
-   [ ] Test upload file >1MB berhasil
-   [ ] Gambar muncul di admin panel

---

## Contact for Help

Jika masih error setelah semua step di atas:

1. **Check Laravel Log:**

    ```bash
    tail -100 storage/logs/laravel.log
    ```

2. **Check Nginx Error Log:**

    ```bash
    sudo tail -100 /var/log/nginx/error.log
    ```

3. **Check PHP-FPM Log:**

    ```bash
    sudo tail -100 /var/log/php8.2-fpm.log
    ```

4. **Enable Debug Mode (temporarily):**

    ```env
    APP_DEBUG=true
    ```

    Try upload again, lihat error message detail.

    **IMPORTANT: Set back to `false` setelah debug!**
