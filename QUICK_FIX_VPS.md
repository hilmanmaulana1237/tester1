# QUICK FIX: Upload >1MB Gagal di VPS

## 🔴 Problem

File >1MB **tidak masuk ke database** di VPS (tapi di localhost bisa).

## 🎯 Root Cause

**Nginx membatasi request body size ke 1MB (default)**, jadi file >1MB di-reject sebelum sampai ke PHP/Laravel.

## ⚡ Quick Fix (5 menit)

### 1. Upload & Jalankan Script

```bash
# Upload scripts ke VPS
scp vps_diagnostic.sh vps_fix_upload_v2.sh user@your-vps:/home/user/

# SSH ke VPS
ssh user@your-vps

# Jalankan diagnostic (optional)
chmod +x vps_diagnostic.sh
./vps_diagnostic.sh

# Jalankan auto-fix
chmod +x vps_fix_upload_v2.sh
sudo ./vps_fix_upload_v2.sh
```

### 2. Laravel Setup

```bash
cd /path/to/laravel

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Create storage link
php artisan storage:link

# Fix permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Run migration (if not yet)
php artisan migrate --force
```

### 3. Test Upload

Upload file >1MB melalui aplikasi. Seharusnya sudah berhasil! ✅

---

## 🔧 Manual Fix (jika script gagal)

### Fix Nginx

```bash
# Edit config
sudo nano /etc/nginx/sites-available/your-site.conf

# Tambahkan di dalam server block:
server {
    client_max_body_size 100M;  # <-- ADD THIS

    # ... rest of config
}

# Test & restart
sudo nginx -t
sudo systemctl restart nginx
```

### Fix PHP-FPM

```bash
# Find PHP version
php -v

# Edit php.ini (ganti 8.2 dengan versi PHP Anda)
sudo nano /etc/php/8.2/fpm/php.ini

# Update settings:
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 512M
max_execution_time = 300

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

---

## 🐛 Troubleshooting

### Cek apakah Nginx yang bermasalah:

```bash
# Cek current setting
grep -r "client_max_body_size" /etc/nginx/

# Jika tidak ada atau <1MB, itu masalahnya!
```

### Cek apakah PHP yang bermasalah:

```bash
php -r "echo 'Upload: ' . ini_get('upload_max_filesize') . ' | Post: ' . ini_get('post_max_size');"

# Jika <10M, itu masalahnya!
```

### Monitor error logs:

```bash
# Terminal 1: Nginx errors
sudo tail -f /var/log/nginx/error.log

# Terminal 2: Laravel errors
tail -f storage/logs/laravel.log

# Terminal 3: Upload file
# Lihat error yang muncul
```

### Test manual dengan curl:

```bash
# Buat test file 2MB
dd if=/dev/zero of=test2mb.dat bs=1M count=2

# Upload ke Laravel endpoint
curl -X POST -F "file=@test2mb.dat" https://your-domain.com/livewire/upload-file

# Lihat response - harusnya sukses
```

---

## ✅ Success Checklist

-   [ ] `grep -r "client_max_body_size 100M" /etc/nginx/` → found ✅
-   [ ] `php -r "echo ini_get('upload_max_filesize');"` → 100M ✅
-   [ ] `php -r "echo ini_get('post_max_size');"` → 100M ✅
-   [ ] `ls -la public/storage` → symbolic link exists ✅
-   [ ] Upload file 2MB → berhasil masuk database ✅
-   [ ] Admin panel → gambar muncul ✅

---

## 📝 Files Created

1. **`vps_diagnostic.sh`** - Check current settings di VPS
2. **`vps_fix_upload_v2.sh`** - Auto-fix semua konfigurasi
3. **`VPS_TROUBLESHOOTING.md`** - Detailed troubleshooting guide

---

## 🆘 Still Not Working?

1. Check `.env`:

    ```env
    APP_URL=https://your-domain.com  # NO trailing slash!
    ```

2. Clear all caches:

    ```bash
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear
    ```

3. Check file exists in storage:

    ```bash
    ls -la storage/app/public/task-proofs/
    ```

4. Check database:

    ```bash
    php artisan tinker
    >>> \App\Models\UserTask::latest()->first()->verification_1_files
    ```

5. Enable debug mode **temporarily**:
    ```env
    APP_DEBUG=true
    ```
    Upload again, lihat error message lengkap.
    **SET BACK TO false AFTER DEBUG!**

---

## 💡 Key Points

-   **Nginx default limit = 1MB** ← This is why it fails!
-   **Fix requires BOTH** Nginx + PHP-FPM restart
-   **Must clear Laravel cache** after config changes
-   **Storage link** must exist: `php artisan storage:link`
-   **Permissions** matter: `775` for storage, `www-data` owner

---

Setelah menjalankan script, file >1MB seharusnya **langsung bisa masuk** ke database! 🚀
