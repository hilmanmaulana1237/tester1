# 🚀 Panduan Deploy ke Production VPS

## ⚠️ Masalah "403 Forbidden" di Production

Masalah ini terjadi karena:

1. **Session cookie tidak berfungsi di HTTPS** - Laravel butuh `SESSION_SECURE_COOKIE=true`
2. **Proxy tidak trusted** - Request dari Cloudflare/Nginx dianggap tidak valid
3. **APP_DEBUG=true di production** - Bisa expose sensitive data

---

## ✅ Solusi yang Sudah Diterapkan

### 1. TrustProxies Middleware (SUDAH DIBUAT)

File: `app/Http/Middleware/TrustProxies.php`

-   Middleware ini membuat Laravel trust semua proxy (Cloudflare, Nginx, dll)
-   Sudah otomatis registered di `bootstrap/app.php`

### 2. File .env.production (TEMPLATE SUDAH DIBUAT)

File: `.env.production`

-   Template environment untuk production
-   **PENTING**: Edit sesuai kredensial VPS kamu!

---

## 📋 Langkah Deploy ke VPS

### A. Di Local (Windows - Persiapan)

1. **Commit semua perubahan terbaru**

```bash
git add .
git commit -m "fix: Add TrustProxies middleware for production HTTPS support"
git push origin main
```

---

### B. Di VPS (via SSH)

#### 1. Login ke VPS

```bash
ssh root@your_vps_ip
# atau
ssh username@cuaninstan.my.id
```

#### 2. Masuk ke folder project

```bash
cd /var/www/html/cuaninstan
# atau sesuaikan path project kamu
```

#### 3. Pull perubahan terbaru

```bash
git pull origin main
```

#### 4. Copy dan edit .env untuk production

```bash
# Backup .env lama
cp .env .env.backup

# Copy template production
cp .env.production .env

# Edit dengan nano
nano .env
```

**Edit bagian ini di .env:**

```env
DB_DATABASE=nama_database_vps_kamu
DB_USERNAME=user_database_vps
DB_PASSWORD=password_database_vps

SESSION_DOMAIN=.cuaninstan.my.id
SESSION_SECURE_COOKIE=true

MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=email_kamu@gmail.com
MAIL_PASSWORD=app_password_gmail
```

Simpan: `Ctrl+O` → Enter → `Ctrl+X`

#### 5. Install/Update dependencies

```bash
composer install --optimize-autoloader --no-dev
```

#### 6. Clear dan rebuild cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 7. Set permission yang benar

```bash
# Set owner (sesuaikan dengan user web server kamu)
sudo chown -R www-data:www-data /var/www/html/cuaninstan
# atau
sudo chown -R nginx:nginx /var/www/html/cuaninstan

# Set permission
sudo chmod -R 755 /var/www/html/cuaninstan
sudo chmod -R 775 /var/www/html/cuaninstan/storage
sudo chmod -R 775 /var/www/html/cuaninstan/bootstrap/cache
```

#### 8. Restart services

```bash
# Restart PHP-FPM (sesuaikan versi PHP kamu)
sudo systemctl restart php8.3-fpm
# atau
sudo systemctl restart php-fpm

# Restart Nginx
sudo systemctl restart nginx
# atau
sudo systemctl restart apache2
```

#### 9. Test database connection

```bash
php artisan migrate:status
```

Jika belum migrate:

```bash
php artisan migrate --force
```

---

## 🔍 Troubleshooting

### Jika masih 403 setelah deploy:

#### 1. Cek log Laravel

```bash
tail -f /var/www/html/cuaninstan/storage/logs/laravel.log
```

#### 2. Cek Nginx error log

```bash
sudo tail -f /var/log/nginx/error.log
```

#### 3. Cek PHP-FPM log

```bash
sudo tail -f /var/log/php8.3-fpm.log
```

#### 4. Pastikan .env sudah benar

```bash
cat .env | grep -E "APP_ENV|APP_DEBUG|SESSION_SECURE|SESSION_DOMAIN"
```

Harus output:

```
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.cuaninstan.my.id
```

#### 5. Test session table ada

```bash
php artisan tinker
```

Di tinker:

```php
DB::table('sessions')->count();
// Harus return angka (0 atau lebih), bukan error
exit
```

#### 6. Cek permission storage

```bash
ls -la storage/
ls -la storage/framework/sessions/
```

Pastikan owner adalah `www-data` atau `nginx` dan permission `775`.

---

## 🔐 Security Checklist Production

-   [x] `APP_ENV=production`
-   [x] `APP_DEBUG=false`
-   [x] `SESSION_SECURE_COOKIE=true`
-   [x] `SESSION_DOMAIN=.cuaninstan.my.id`
-   [x] Database password strong
-   [x] File permission benar (755/775)
-   [x] TrustProxies middleware active
-   [x] Config cached untuk performa

---

## 📞 Cara Login Admin Setelah Deploy

1. Buka: https://cuaninstan.my.id/admin
2. Login dengan:

    - **Email**: `superadmin@gmail.com`
    - **Password**: `password`

    Atau:

    - **Email**: `admin1@gmail.com`
    - **Password**: `password`

3. **WAJIB ganti password** setelah login pertama kali!

---

## 🎯 Quick Commands (Simpan untuk nanti)

**Clear semua cache:**

```bash
php artisan optimize:clear
```

**Rebuild cache production:**

```bash
php artisan optimize
```

**Restart services:**

```bash
sudo systemctl restart php8.3-fpm nginx
```

**Cek status:**

```bash
sudo systemctl status php8.3-fpm
sudo systemctl status nginx
```

---

## 📝 Catatan Penting

1. **Jangan pernah set `APP_DEBUG=true` di production!**

    - Ini akan expose database password, API keys, dll di error page.

2. **Selalu backup sebelum deploy:**

    ```bash
    # Backup database
    mysqldump -u root -p cuaninstan_db > backup_$(date +%F).sql

    # Backup files
    tar -czf backup_files_$(date +%F).tar.gz /var/www/html/cuaninstan
    ```

3. **Test di local dulu dengan HTTPS:**

    - Gunakan Laravel Valet (Mac) atau Laragon SSL (Windows)
    - Pastikan tidak ada error 403 di local HTTPS

4. **Monitor log setelah deploy:**
    ```bash
    # Monitor live
    tail -f storage/logs/laravel.log
    ```

---

**Butuh bantuan?**

-   Cek log error dengan command di atas
-   Atau kirim screenshot error ke developer
