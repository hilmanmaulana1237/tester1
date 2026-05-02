#!/bin/bash

# AUTO FIX: Upload >1MB tidak masuk ke database di VPS
# Script ini FOKUS pada masalah Nginx + PHP-FPM

echo "=========================================="
echo "AUTO FIX: Upload >1MB di VPS"
echo "=========================================="
echo ""

# Must run as root
if [ "$EUID" -ne 0 ]; then 
    echo "❌ Please run as root: sudo ./vps_fix_upload_v2.sh"
    exit 1
fi

# Detect PHP version
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo "Detected PHP version: $PHP_VERSION"
echo ""

# ============================================
# STEP 1: FIX NGINX (MOST CRITICAL!)
# ============================================
echo "STEP 1: Fixing Nginx Configuration..."
echo "---------------------------------------"

# Find nginx config files
NGINX_CONF="/etc/nginx/nginx.conf"
SITE_CONFIGS=$(find /etc/nginx/sites-available/ -type f 2>/dev/null)

# Function to add/update client_max_body_size
fix_nginx_config() {
    local config_file=$1
    
    if [ -f "$config_file" ]; then
        echo "Fixing: $config_file"
        
        # Backup
        cp "$config_file" "${config_file}.backup.$(date +%Y%m%d_%H%M%S)"
        
        # Check if client_max_body_size exists
        if grep -q "client_max_body_size" "$config_file"; then
            # Update existing
            sed -i 's/client_max_body_size.*;/client_max_body_size 100M;/' "$config_file"
            echo "  ✅ Updated existing client_max_body_size to 100M"
        else
            # Add to http block
            if grep -q "http {" "$config_file"; then
                sed -i '/http {/a \    client_max_body_size 100M;' "$config_file"
                echo "  ✅ Added client_max_body_size 100M to http block"
            fi
            
            # Add to server block
            if grep -q "server {" "$config_file"; then
                sed -i '/server {/a \    client_max_body_size 100M;' "$config_file"
                echo "  ✅ Added client_max_body_size 100M to server block"
            fi
        fi
    fi
}

# Fix main nginx.conf
fix_nginx_config "$NGINX_CONF"

# Fix all site configs
for config in $SITE_CONFIGS; do
    fix_nginx_config "$config"
done

# Test nginx config
echo ""
echo "Testing nginx configuration..."
nginx -t
if [ $? -eq 0 ]; then
    echo "✅ Nginx config is valid"
else
    echo "❌ Nginx config has errors - please check manually"
    exit 1
fi

echo ""

# ============================================
# STEP 2: FIX PHP-FPM
# ============================================
echo "STEP 2: Fixing PHP-FPM Configuration..."
echo "---------------------------------------"

PHP_FPM_INI="/etc/php/$PHP_VERSION/fpm/php.ini"

if [ -f "$PHP_FPM_INI" ]; then
    echo "Fixing: $PHP_FPM_INI"
    
    # Backup
    cp "$PHP_FPM_INI" "${PHP_FPM_INI}.backup.$(date +%Y%m%d_%H%M%S)"
    
    # Update settings using sed (will uncomment if commented)
    sed -i 's/^;\?upload_max_filesize.*/upload_max_filesize = 100M/' "$PHP_FPM_INI"
    sed -i 's/^;\?post_max_size.*/post_max_size = 100M/' "$PHP_FPM_INI"
    sed -i 's/^;\?memory_limit.*/memory_limit = 512M/' "$PHP_FPM_INI"
    sed -i 's/^;\?max_execution_time.*/max_execution_time = 300/' "$PHP_FPM_INI"
    sed -i 's/^;\?max_input_time.*/max_input_time = 300/' "$PHP_FPM_INI"
    sed -i 's/^;\?max_input_vars.*/max_input_vars = 5000/' "$PHP_FPM_INI"
    
    echo "  ✅ Updated PHP-FPM php.ini"
else
    echo "  ❌ PHP-FPM php.ini not found at $PHP_FPM_INI"
fi

# Also update CLI php.ini
PHP_CLI_INI="/etc/php/$PHP_VERSION/cli/php.ini"
if [ -f "$PHP_CLI_INI" ]; then
    cp "$PHP_CLI_INI" "${PHP_CLI_INI}.backup.$(date +%Y%m%d_%H%M%S)"
    sed -i 's/^;\?upload_max_filesize.*/upload_max_filesize = 100M/' "$PHP_CLI_INI"
    sed -i 's/^;\?post_max_size.*/post_max_size = 100M/' "$PHP_CLI_INI"
    sed -i 's/^;\?memory_limit.*/memory_limit = 512M/' "$PHP_CLI_INI"
    echo "  ✅ Updated PHP-CLI php.ini"
fi

echo ""

# ============================================
# STEP 3: FIX PHP-FPM Pool CONFIG (IMPORTANT!)
# ============================================
echo "STEP 3: Checking PHP-FPM Pool Configuration..."
echo "---------------------------------------"

FPM_POOL_CONF="/etc/php/$PHP_VERSION/fpm/pool.d/www.conf"

if [ -f "$FPM_POOL_CONF" ]; then
    # Check if there are any restrictive limits
    if grep -q "php_admin_value\[upload_max_filesize\]" "$FPM_POOL_CONF"; then
        echo "  Found upload limits in pool config, updating..."
        cp "$FPM_POOL_CONF" "${FPM_POOL_CONF}.backup.$(date +%Y%m%d_%H%M%S)"
        
        sed -i 's/php_admin_value\[upload_max_filesize\].*/php_admin_value[upload_max_filesize] = 100M/' "$FPM_POOL_CONF"
        sed -i 's/php_admin_value\[post_max_size\].*/php_admin_value[post_max_size] = 100M/' "$FPM_POOL_CONF"
        
        echo "  ✅ Updated pool config"
    else
        echo "  ✅ No restrictive limits in pool config"
    fi
fi

echo ""

# ============================================
# STEP 4: FIX MYSQL
# ============================================
echo "STEP 4: Fixing MySQL Configuration..."
echo "---------------------------------------"

MYSQL_CONF="/etc/mysql/mysql.conf.d/mysqld.cnf"
if [ ! -f "$MYSQL_CONF" ]; then
    MYSQL_CONF="/etc/mysql/my.cnf"
fi

if [ -f "$MYSQL_CONF" ]; then
    cp "$MYSQL_CONF" "${MYSQL_CONF}.backup.$(date +%Y%m%d_%H%M%S)"
    
    if grep -q "max_allowed_packet" "$MYSQL_CONF"; then
        sed -i 's/max_allowed_packet.*/max_allowed_packet = 64M/' "$MYSQL_CONF"
        echo "  ✅ Updated max_allowed_packet"
    else
        # Add to [mysqld] section
        sed -i '/\[mysqld\]/a max_allowed_packet = 64M' "$MYSQL_CONF"
        echo "  ✅ Added max_allowed_packet"
    fi
else
    echo "  ⚠️  MySQL config not found, skipping"
fi

echo ""

# ============================================
# STEP 5: RESTART SERVICES
# ============================================
echo "STEP 5: Restarting Services..."
echo "---------------------------------------"

# Restart PHP-FPM
systemctl restart "php${PHP_VERSION}-fpm"
if [ $? -eq 0 ]; then
    echo "  ✅ PHP-FPM restarted"
    sleep 2
else
    echo "  ❌ Failed to restart PHP-FPM"
fi

# Restart Nginx
systemctl restart nginx
if [ $? -eq 0 ]; then
    echo "  ✅ Nginx restarted"
    sleep 2
else
    echo "  ❌ Failed to restart Nginx"
fi

# Restart MySQL (optional, only if config changed)
if [ -f "$MYSQL_CONF" ]; then
    systemctl restart mysql 2>/dev/null || systemctl restart mariadb 2>/dev/null
    if [ $? -eq 0 ]; then
        echo "  ✅ MySQL restarted"
    else
        echo "  ⚠️  MySQL restart failed or not needed"
    fi
fi

echo ""

# ============================================
# STEP 6: VERIFY SETTINGS
# ============================================
echo "STEP 6: Verifying Current Settings..."
echo "---------------------------------------"

echo "PHP Settings:"
echo "  upload_max_filesize: $(php -r 'echo ini_get("upload_max_filesize");')"
echo "  post_max_size: $(php -r 'echo ini_get("post_max_size");')"
echo "  memory_limit: $(php -r 'echo ini_get("memory_limit");')"
echo ""

echo "Nginx Settings:"
grep -r "client_max_body_size" /etc/nginx/ 2>/dev/null | grep -v ".backup" | head -3
echo ""

# ============================================
# STEP 7: TEST UPLOAD
# ============================================
echo "STEP 7: Creating Test Endpoint..."
echo "---------------------------------------"

# Create a test PHP file
TEST_FILE="/tmp/upload_test.php"
cat > "$TEST_FILE" << 'EOF'
<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $size_mb = round($file['size'] / 1024 / 1024, 2);
        
        echo json_encode([
            'success' => true,
            'message' => 'File received by PHP!',
            'filename' => $file['name'],
            'size' => $file['size'],
            'size_mb' => $size_mb . ' MB',
            'tmp_name' => $file['tmp_name'],
            'error' => $file['error'],
            'max_upload' => ini_get('upload_max_filesize'),
            'max_post' => ini_get('post_max_size')
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No file uploaded',
            'post_size' => strlen(file_get_contents('php://input'))
        ]);
    }
} else {
    echo json_encode([
        'method' => 'GET',
        'message' => 'Upload endpoint ready',
        'max_upload' => ini_get('upload_max_filesize'),
        'max_post' => ini_get('post_max_size')
    ]);
}
?>
EOF

echo "Test file created at: $TEST_FILE"
echo ""
echo "To test upload manually:"
echo "  1. Copy this file to your Laravel public directory:"
echo "     cp $TEST_FILE /path/to/laravel/public/test_upload.php"
echo ""
echo "  2. Test with curl:"
echo "     curl -F 'file=@/path/to/large_file.jpg' https://your-domain.com/test_upload.php"
echo ""
echo "  3. Delete test file after testing:"
echo "     rm /path/to/laravel/public/test_upload.php"
echo ""

# ============================================
# COMPLETION
# ============================================
echo "=========================================="
echo "FIX COMPLETED!"
echo "=========================================="
echo ""
echo "✅ All configurations updated and services restarted"
echo ""
echo "NEXT STEPS IN LARAVEL:"
echo "  1. cd /path/to/laravel"
echo "  2. php artisan config:clear"
echo "  3. php artisan cache:clear"
echo "  4. php artisan storage:link"
echo "  5. chmod -R 775 storage bootstrap/cache"
echo "  6. chown -R www-data:www-data storage bootstrap/cache"
echo ""
echo "TEST UPLOAD:"
echo "  Try uploading a file >1MB through your application"
echo ""
echo "If still not working, check logs:"
echo "  - tail -f /var/log/nginx/error.log"
echo "  - tail -f /var/log/php${PHP_VERSION}-fpm.log"
echo "  - tail -f /path/to/laravel/storage/logs/laravel.log"
echo ""
