# Fix Upload File >1MB ke Database

## Masalah

File lebih dari 1MB tidak masuk ke database karena:

1. **VARCHAR(255) Limit**: Field `verification_1_status` dan `verification_2_status` terlalu pendek untuk menyimpan file paths panjang
2. **MySQL Packet Size**: Default max_allowed_packet terlalu kecil
3. **PHP Upload Limits**: Default limits terlalu rendah

## Solusi

### 1. Migration Database (SUDAH DIBUAT)

File: `database/migrations/2025_12_01_000001_fix_user_tasks_verification_fields.php`

**Perubahan:**

-   `verification_1_status`: VARCHAR(255) → TEXT
-   `verification_2_status`: VARCHAR(255) → TEXT
-   Tambah kolom: `verification_1_files` (TEXT)
-   Tambah kolom: `verification_2_files` (TEXT)

**Jalankan migration:**

```bash
php artisan migrate
```

### 2. Update Model (SUDAH DIUPDATE)

File: `app/Models/UserTask.php`

**Perubahan:**

-   Tambah `verification_1_files` dan `verification_2_files` ke fillable
-   Cast ke array untuk otomatis encode/decode JSON

### 3. Update TaskWorkWizard (SUDAH DIUPDATE)

File: `app/Livewire/TaskWorkWizard.php`

**Perubahan:**

-   Pisahkan file paths dan description
-   File paths disimpan sebagai JSON array di kolom `verification_x_files`
-   Description tetap di `verification_x_status`

**Sebelum:**

```php
'verification_1_status' => 'Submitted at ... Files: path1, path2, path3. Description: ...'
```

**Sesudah:**

```php
'verification_1_status' => 'Submitted at ... Description: ...',
'verification_1_files' => json_encode(['path1', 'path2', 'path3'])
```

### 4. PHP Configuration

#### File: `.user.ini` (SUDAH DIBUAT)

```ini
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 512M
max_execution_time = 300
```

#### File: `public/.htaccess` (SUDAH DIUPDATE)

```apache
php_value upload_max_filesize 100M
php_value post_max_size 100M
php_value memory_limit 512M
```

### 5. MySQL Configuration (UNTUK VPS)

Edit file `/etc/mysql/my.cnf` atau `/etc/my.cnf`:

```ini
[mysqld]
max_allowed_packet = 64M
innodb_log_file_size = 128M
innodb_buffer_pool_size = 256M
```

Atau jalankan di MySQL console:

```sql
SET GLOBAL max_allowed_packet=67108864; -- 64MB
```

Restart MySQL:

```bash
sudo systemctl restart mysql
```

## Cara Deploy ke VPS

### 1. Upload Files

```bash
cd /var/www/html/your-project
git pull origin main
```

### 2. Jalankan Migration

```bash
php artisan migrate
```

### 3. Set Permissions

```bash
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

### 4. Update PHP Config di VPS

**Untuk PHP-FPM**, edit `/etc/php/8.2/fpm/php.ini`:

```ini
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
memory_limit = 512M
```

**Untuk Nginx**, edit `/etc/nginx/sites-available/your-site`:

```nginx
client_max_body_size 100M;
```

### 5. Restart Services

```bash
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx  # atau apache2
sudo systemctl restart mysql
```

### 6. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Testing

### 1. Test Upload File Besar

1. Login ke aplikasi
2. Ambil task
3. Upload file >1MB (misalnya foto 2-3MB)
4. Submit proof
5. Check database:

```sql
SELECT id, status, verification_1_status, verification_1_files
FROM user_tasks
WHERE id = [last_id];
```

### 2. Check File Tersimpan

```bash
ls -lh storage/app/public/task-proofs/
```

### 3. Monitor Log

```bash
tail -f storage/logs/laravel.log
```

## Troubleshooting

### Error: "SQLSTATE[22001]: String data, right truncated"

**Penyebab:** Kolom masih VARCHAR(255)
**Solusi:** Jalankan migration lagi

```bash
php artisan migrate:fresh  # HATI-HATI: Hapus semua data
# atau
php artisan migrate
```

### Error: "Got a packet bigger than max_allowed_packet"

**Penyebab:** MySQL packet size terlalu kecil
**Solusi:**

```sql
SET GLOBAL max_allowed_packet=67108864;
```

### Error: "The file failed to upload"

**Penyebab:** PHP upload limits
**Solusi:** Check `phpinfo()`:

```php
<?php phpinfo(); ?>
```

Pastikan:

-   upload_max_filesize >= 100M
-   post_max_size >= 100M
-   memory_limit >= 512M

### File Upload Timeout

**Penyebab:** Koneksi mobile lambat
**Solusi:**

-   Compress gambar sebelum upload
-   Gunakan format WebP/JPEG (bukan PNG untuk foto)
-   Check max_execution_time sudah 300 detik

## Files Changed

1. ✅ `database/migrations/2025_12_01_000001_fix_user_tasks_verification_fields.php` - NEW
2. ✅ `app/Models/UserTask.php` - Updated fillable & casts
3. ✅ `app/Livewire/TaskWorkWizard.php` - Updated submit methods
4. ✅ `.user.ini` - NEW (PHP config)
5. ✅ `public/.htaccess` - Updated (PHP limits)

## Keuntungan Solusi Ini

1. **Scalable**: Tidak ada batasan jumlah/panjang file paths
2. **Clean**: File paths terpisah dari status message
3. **Efficient**: JSON array mudah di-parse oleh admin panel
4. **Flexible**: Bisa simpan metadata tambahan per file jika perlu

## Akses File dari Admin

Untuk menampilkan file di admin panel Filament:

```php
// Get files array
$files = json_decode($userTask->verification_1_files, true);

// Display
foreach ($files as $file) {
    echo Storage::url($file);
}
```
