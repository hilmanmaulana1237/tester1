#!/bin/bash

# VPS Deployment Fix untuk Upload File >1MB
# Jalankan script ini di VPS sebagai root atau dengan sudo

echo "=== FIX UPLOAD FILE >1MB DI VPS ==="
echo ""

# 1. Update PHP Configuration
echo "1. Mengupdate PHP Configuration..."

# Cari versi PHP yang digunakan
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo "   PHP Version: $PHP_VERSION"

# Update php.ini untuk PHP-FPM
PHP_INI="/etc/php/$PHP_VERSION/fpm/php.ini"

if [ -f "$PHP_INI" ]; then
    echo "   Updating $PHP_INI"
    
    # Backup original
    cp "$PHP_INI" "$PHP_INI.backup.$(date +%Y%m%d_%H%M%S)"
    
    # Update settings
    sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 100M/' "$PHP_INI"
    sed -i 's/^post_max_size = .*/post_max_size = 100M/' "$PHP_INI"
    sed -i 's/^memory_limit = .*/memory_limit = 512M/' "$PHP_INI"
    sed -i 's/^max_execution_time = .*/max_execution_time = 300/' "$PHP_INI"
    sed -i 's/^max_input_time = .*/max_input_time = 300/' "$PHP_INI"
    
    echo "   ✅ PHP-FPM configuration updated"
else
    echo "   ❌ PHP-FPM php.ini not found at $PHP_INI"
fi

# Update php.ini untuk CLI (optional)
PHP_CLI_INI="/etc/php/$PHP_VERSION/cli/php.ini"
if [ -f "$PHP_CLI_INI" ]; then
    sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 100M/' "$PHP_CLI_INI"
    sed -i 's/^post_max_size = .*/post_max_size = 100M/' "$PHP_CLI_INI"
    sed -i 's/^memory_limit = .*/memory_limit = 512M/' "$PHP_CLI_INI"
    echo "   ✅ PHP-CLI configuration updated"
fi

echo ""

# 2. Update Nginx Configuration
echo "2. Mengupdate Nginx Configuration..."

NGINX_CONF="/etc/nginx/nginx.conf"
SITE_CONF="/etc/nginx/sites-available/default"

# Check if using Laravel Forge or custom config
if [ -d "/etc/nginx/sites-available" ]; then
    # Find Laravel site config
    LARAVEL_CONF=$(find /etc/nginx/sites-available -name "*.conf" -o -name "*laravel*" | head -1)
    
    if [ -n "$LARAVEL_CONF" ]; then
        SITE_CONF="$LARAVEL_CONF"
    fi
fi

if [ -f "$NGINX_CONF" ]; then
    # Check if client_max_body_size already exists
    if grep -q "client_max_body_size" "$NGINX_CONF"; then
        sed -i 's/client_max_body_size .*/client_max_body_size 100M;/' "$NGINX_CONF"
        echo "   ✅ Updated existing client_max_body_size in nginx.conf"
    else
        # Add to http block
        sed -i '/http {/a \    client_max_body_size 100M;' "$NGINX_CONF"
        echo "   ✅ Added client_max_body_size to nginx.conf"
    fi
fi

if [ -f "$SITE_CONF" ]; then
    echo "   Site config: $SITE_CONF"
    
    # Backup
    cp "$SITE_CONF" "$SITE_CONF.backup.$(date +%Y%m%d_%H%M%S)"
    
    # Add client_max_body_size to server block if not exists
    if grep -q "client_max_body_size" "$SITE_CONF"; then
        sed -i 's/client_max_body_size .*/client_max_body_size 100M;/' "$SITE_CONF"
    else
        sed -i '/server {/a \    client_max_body_size 100M;' "$SITE_CONF"
    fi
    
    echo "   ✅ Nginx site configuration updated"
fi

echo ""

# 3. Update MySQL Configuration
echo "3. Mengupdate MySQL Configuration..."

MYSQL_CONF="/etc/mysql/mysql.conf.d/mysqld.cnf"
if [ ! -f "$MYSQL_CONF" ]; then
    MYSQL_CONF="/etc/mysql/my.cnf"
fi

if [ -f "$MYSQL_CONF" ]; then
    cp "$MYSQL_CONF" "$MYSQL_CONF.backup.$(date +%Y%m%d_%H%M%S)"
    
    # Check if max_allowed_packet exists
    if grep -q "max_allowed_packet" "$MYSQL_CONF"; then
        sed -i 's/max_allowed_packet.*/max_allowed_packet = 64M/' "$MYSQL_CONF"
    else
        # Add under [mysqld] section
        sed -i '/\[mysqld\]/a max_allowed_packet = 64M' "$MYSQL_CONF"
    fi
    
    echo "   ✅ MySQL configuration updated"
else
    echo "   ❌ MySQL config not found"
fi

echo ""

# 4. Restart Services
echo "4. Restarting Services..."

# Restart PHP-FPM
systemctl restart "php$PHP_VERSION-fpm"
if [ $? -eq 0 ]; then
    echo "   ✅ PHP-FPM restarted"
else
    echo "   ❌ Failed to restart PHP-FPM"
fi

# Restart Nginx
systemctl restart nginx
if [ $? -eq 0 ]; then
    echo "   ✅ Nginx restarted"
else
    echo "   ❌ Failed to restart Nginx"
fi

# Restart MySQL
systemctl restart mysql
if [ $? -eq 0 ]; then
    echo "   ✅ MySQL restarted"
else
    echo "   ⚠️  MySQL restart failed or not installed"
fi

echo ""

# 5. Display Current Settings
echo "5. Verifying Settings..."
echo ""
echo "   PHP Settings:"
php -r "echo '   - upload_max_filesize: ' . ini_get('upload_max_filesize') . PHP_EOL;"
php -r "echo '   - post_max_size: ' . ini_get('post_max_size') . PHP_EOL;"
php -r "echo '   - memory_limit: ' . ini_get('memory_limit') . PHP_EOL;"
php -r "echo '   - max_execution_time: ' . ini_get('max_execution_time') . PHP_EOL;"

echo ""
echo "   Nginx Settings:"
if grep -r "client_max_body_size" /etc/nginx/ 2>/dev/null; then
    grep -r "client_max_body_size" /etc/nginx/ 2>/dev/null | head -3
    echo "   ✅ Found"
else
    echo "   ❌ Not found"
fi

echo ""
echo "=== SELESAI ==="
echo ""
echo "NEXT STEPS:"
echo "1. Run migration: php artisan migrate"
echo "2. Clear cache: php artisan cache:clear"
echo "3. Clear config: php artisan config:clear"
echo "4. Set storage permissions: chmod -R 775 storage"
echo "5. Create storage link: php artisan storage:link"
echo ""
echo "Test upload file >1MB untuk memastikan sudah berfungsi!"
