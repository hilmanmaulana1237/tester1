#!/bin/bash

# DIAGNOSTIC: Check Upload Limits di VPS
# Jalankan script ini untuk menemukan bottleneck

echo "=========================================="
echo "DIAGNOSTIC: File Upload >1MB di VPS"
echo "=========================================="
echo ""

# 1. Check PHP Version
echo "1. PHP VERSION:"
php -v | head -1
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo ""

# 2. Check PHP-FPM Settings (CRITICAL!)
echo "2. PHP-FPM SETTINGS (yang sedang berjalan):"
echo "   upload_max_filesize: $(php -r 'echo ini_get("upload_max_filesize");')"
echo "   post_max_size: $(php -r 'echo ini_get("post_max_size");')"
echo "   memory_limit: $(php -r 'echo ini_get("memory_limit");')"
echo "   max_execution_time: $(php -r 'echo ini_get("max_execution_time");')"
echo "   max_input_time: $(php -r 'echo ini_get("max_input_time");')"
echo ""

# 3. Check php.ini location
echo "3. PHP INI FILES:"
echo "   Loaded: $(php -r 'echo php_ini_loaded_file();')"
echo "   Scan dir: $(php -r 'echo php_ini_scanned_files();' | head -c 100)..."
echo ""

# 4. Check Nginx client_max_body_size (CRITICAL!)
echo "4. NGINX SETTINGS:"
if command -v nginx &> /dev/null; then
    echo "   Nginx version: $(nginx -v 2>&1)"
    echo "   Config test: $(nginx -t 2>&1 | grep -E 'successful|failed')"
    echo ""
    echo "   Checking client_max_body_size in configs:"
    
    # Search in all nginx configs
    grep -r "client_max_body_size" /etc/nginx/ 2>/dev/null | head -5
    
    if [ $? -ne 0 ]; then
        echo "   ⚠️  WARNING: client_max_body_size NOT FOUND in any nginx config!"
        echo "   This is likely THE PROBLEM - Nginx default is 1MB!"
    fi
else
    echo "   ❌ Nginx not found"
fi
echo ""

# 5. Check if we're behind a proxy (Apache)
echo "5. APACHE CHECK:"
if command -v apache2 &> /dev/null || command -v httpd &> /dev/null; then
    echo "   ⚠️  Apache detected - check LimitRequestBody"
    if [ -f /etc/apache2/apache2.conf ]; then
        grep "LimitRequestBody" /etc/apache2/apache2.conf
    fi
    if [ -f /etc/httpd/conf/httpd.conf ]; then
        grep "LimitRequestBody" /etc/httpd/conf/httpd.conf
    fi
else
    echo "   Apache not detected"
fi
echo ""

# 6. Check MySQL max_allowed_packet
echo "6. MYSQL SETTINGS:"
if command -v mysql &> /dev/null; then
    echo "   max_allowed_packet: $(mysql -e "SHOW VARIABLES LIKE 'max_allowed_packet';" 2>/dev/null | grep max_allowed_packet | awk '{print $2}')"
else
    echo "   MySQL client not found"
fi
echo ""

# 7. Check Laravel storage
echo "7. LARAVEL STORAGE:"
if [ -f "artisan" ]; then
    echo "   Laravel detected"
    echo "   Storage link: $(ls -la public/storage 2>/dev/null | grep -o '\->.* ' || echo 'NOT CREATED')"
    echo "   Storage permissions: $(stat -c '%a' storage 2>/dev/null || stat -f '%Lp' storage 2>/dev/null)"
    echo "   Storage owner: $(stat -c '%U:%G' storage 2>/dev/null || stat -f '%Su:%Sg' storage 2>/dev/null)"
else
    echo "   Not in Laravel directory"
fi
echo ""

# 8. Check disk space
echo "8. DISK SPACE:"
df -h . | tail -1
echo ""

echo "=========================================="
echo "ANALYSIS:"
echo "=========================================="

# Check PHP upload limit
UPLOAD_LIMIT=$(php -r 'echo ini_get("upload_max_filesize");')
POST_LIMIT=$(php -r 'echo ini_get("post_max_size");')

echo "PHP upload_max_filesize: $UPLOAD_LIMIT"
echo "PHP post_max_size: $POST_LIMIT"

# Convert to bytes for comparison (simple check for M)
if [[ $UPLOAD_LIMIT == *"M"* ]]; then
    UPLOAD_MB=${UPLOAD_LIMIT%M}
    if [ "$UPLOAD_MB" -lt 10 ]; then
        echo "❌ PROBLEM: upload_max_filesize too low ($UPLOAD_LIMIT)"
        echo "   FIX: Update /etc/php/$PHP_VERSION/fpm/php.ini"
    fi
fi

if [[ $POST_LIMIT == *"M"* ]]; then
    POST_MB=${POST_LIMIT%M}
    if [ "$POST_MB" -lt 10 ]; then
        echo "❌ PROBLEM: post_max_size too low ($POST_LIMIT)"
        echo "   FIX: Update /etc/php/$PHP_VERSION/fpm/php.ini"
    fi
fi

# Check Nginx
if ! grep -r "client_max_body_size" /etc/nginx/ &>/dev/null; then
    echo ""
    echo "❌ CRITICAL PROBLEM: Nginx client_max_body_size NOT SET"
    echo "   Nginx default = 1MB (this is why files >1MB fail!)"
    echo "   FIX: Add 'client_max_body_size 100M;' to nginx config"
fi

echo ""
echo "=========================================="
echo "RECOMMENDED ACTIONS:"
echo "=========================================="
echo "Run the auto-fix script:"
echo "  sudo ./vps_fix_upload_v2.sh"
echo ""
