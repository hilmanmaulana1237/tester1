# 🔧 Fix 403 Forbidden di Production (HTTPS)

## 🔍 Masalah

Admin tidak bisa akses `/admin` ketika `APP_ENV=production`, tapi bisa ketika `APP_ENV=local`.

## ✅ Solusi Lengkap

### Step 1: Upload File Debug ke VPS

```bash
# Di VPS, masuk ke folder public
cd /var/www/html/cuaninstan/public

# Upload file debug-session.php (sudah dibuat di project)
# Atau copy paste manual dengan nano
nano debug-session.php
# Paste isi file, lalu Ctrl+O, Enter, Ctrl+X
```

### Step 2: Test Session di Browser

1. Login sebagai admin di: `https://cuaninstan.my.id/login`
2. Setelah login, akses: `https://cuaninstan.my.id/debug-session.php`
3. Screenshot hasilnya dan lihat bagian yang **❌ error**

### Step 3: Fix Berdasarkan Error

#### Fix A: Session Cookie Tidak Ter-set (Paling Sering Terjadi!)

**Gejala**: Debug menunjukkan "Session cookie NOT found"

**Solusi**: Edit `.env` di VPS

```bash
cd /var/www/html/cuaninstan
nano .env
```

Pastikan ini:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cuaninstan.my.id

SESSION_DRIVER=database
SESSION_DOMAIN=.cuaninstan.my.id
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_PATH=/
```

**PENTING**: `SESSION_DOMAIN` harus ada titik di depan! → `.cuaninstan.my.id`

Setelah edit:

```bash
php artisan config:clear
php artisan cache:clear
sudo systemctl restart php8.3-fpm
```

#### Fix B: Database Session Table Error

**Gejala**: Debug menunjukkan "Session NOT found in database"

**Solusi**:

```bash
# Cek table sessions ada
php artisan tinker
```

Di tinker:

```php
DB::table('sessions')->count();
// Kalau error, run migrate
exit
```

Kalau error, jalankan:

```bash
php artisan migrate --force
```

#### Fix C: Permission Storage Error

**Gejala**: Debug menunjukkan permission error

**Solusi**:

```bash
sudo chown -R www-data:www-data /var/www/html/cuaninstan
sudo chmod -R 755 /var/www/html/cuaninstan
sudo chmod -R 775 /var/www/html/cuaninstan/storage
sudo chmod -R 775 /var/www/html/cuaninstan/bootstrap/cache
```

#### Fix D: TrustProxies Tidak Active

**Gejala**: Debug menunjukkan "TrustProxies middleware not found"

**Solusi**: Pastikan file sudah di-push ke git dan di-pull di VPS

```bash
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan config:clear
```

### Step 4: Test Login Admin

1. Logout dulu: `https://cuaninstan.my.id/logout`
2. Login lagi: `https://cuaninstan.my.id/login`
    - Email: `admin1@gmail.com`
    - Password: `password`
3. Akses admin: `https://cuaninstan.my.id/admin`

### Step 5: Cek Log Jika Masih Error

```bash
# Log Laravel
tail -50 /var/www/html/cuaninstan/storage/logs/laravel.log

# Log Nginx
sudo tail -50 /var/log/nginx/error.log

# Log PHP-FPM
sudo tail -50 /var/log/php8.3-fpm.log
```

Cari line yang contain `AdminMiddleware` untuk lihat debug info.

### Step 6: Hapus File Debug (PENTING!)

Setelah selesai, **WAJIB hapus** file debug:

```bash
rm /var/www/html/cuaninstan/public/debug-session.php
```

---

## 🎯 Checklist .env Production

```env
✅ APP_ENV=production
✅ APP_DEBUG=false
✅ APP_URL=https://cuaninstan.my.id
✅ SESSION_DRIVER=database
✅ SESSION_DOMAIN=.cuaninstan.my.id        ← PENTING! Ada titik di depan
✅ SESSION_SECURE_COOKIE=true              ← PENTING untuk HTTPS
✅ SESSION_HTTP_ONLY=true
✅ SESSION_SAME_SITE=lax
✅ SESSION_PATH=/
```

---

## 🔥 Quick Fix Script

Copy paste command ini di VPS:

```bash
cd /var/www/html/cuaninstan

# Backup .env
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)

# Update .env
cat > .env.session.fix << 'EOF'
SESSION_DRIVER=database
SESSION_DOMAIN=.cuaninstan.my.id
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_PATH=/
EOF

# Merge ke .env (manual check dulu)
cat .env.session.fix

# Kalau sudah yakin, edit manual dengan nano
nano .env

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Restart services
sudo systemctl restart php8.3-fpm nginx

# Test
curl -I https://cuaninstan.my.id/admin
```

---

## 🐛 Common Errors & Solutions

### Error: "Session driver [database] not configured"

```bash
# Check .env
grep SESSION_DRIVER .env

# Should be: SESSION_DRIVER=database
# Run migration if needed
php artisan migrate --force
```

### Error: "CSRF token mismatch"

```bash
# Clear everything
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Restart browser and try again
```

### Error: "Too many redirects"

```bash
# Check Nginx config for proxy settings
sudo nano /etc/nginx/sites-available/cuaninstan

# Should have these inside location block:
# proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
# proxy_set_header X-Forwarded-Proto $scheme;
# proxy_set_header Host $host;
```

### Error: "Session store not set on request"

```bash
# Check if StartSession middleware is loaded
php artisan route:list | grep admin

# Should see StartSession in middleware column
# If not, check bootstrap/app.php
```

---

## 📞 Masih Belum Bisa?

Kirim hasil dari command ini:

```bash
# Environment check
cd /var/www/html/cuaninstan
php -v
php artisan --version

# .env check (hide password dulu!)
grep -E "APP_ENV|APP_DEBUG|SESSION_" .env | grep -v "PASSWORD"

# Session table check
php artisan tinker --execute="echo DB::table('sessions')->count();"

# Permission check
ls -la storage/framework/sessions/ 2>/dev/null || echo "Directory not found"

# Nginx config check (hide sensitive info)
sudo cat /etc/nginx/sites-available/cuaninstan | grep -A 5 "location /"

# PHP-FPM status
sudo systemctl status php8.3-fpm | head -10
```

Screenshot dan kirim hasilnya! 🚀
