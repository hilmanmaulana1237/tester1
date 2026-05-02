<?php

/**
 * Debug Script untuk Troubleshoot Session di Production
 * 
 * Cara pakai:
 * 1. Upload file ini ke folder routes/ atau public/
 * 2. Akses via browser: https://cuaninstan.my.id/debug-session.php
 * 3. Login dulu sebagai admin, baru akses script ini
 * 4. Lihat output untuk cari masalah
 * 
 * PENTING: HAPUS FILE INI SETELAH SELESAI DEBUG!
 */

// Load Laravel bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate HTTP request
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

?>
<!DOCTYPE html>
<html>

<head>
    <title>Debug Session - Production</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background: #1e1e1e;
            color: #fff;
        }

        .section {
            background: #2d2d2d;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .label {
            color: #4ec9b0;
            font-weight: bold;
        }

        .value {
            color: #ce9178;
        }

        .success {
            color: #4ec9b0;
        }

        .error {
            color: #f48771;
        }

        .warning {
            color: #dcdcaa;
        }

        pre {
            background: #1e1e1e;
            padding: 10px;
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <h1>🔍 Laravel Session Debug - Production</h1>

    <div class="section">
        <h2>Environment Info</h2>
        <p><span class="label">APP_ENV:</span> <span class="value"><?= config('app.env') ?></span></p>
        <p><span class="label">APP_DEBUG:</span> <span class="value"><?= config('app.debug') ? 'true' : 'false' ?></span></p>
        <p><span class="label">APP_URL:</span> <span class="value"><?= config('app.url') ?></span></p>
        <p><span class="label">Current URL:</span> <span class="value"><?= request()->fullUrl() ?></span></p>
        <p><span class="label">Request Scheme:</span> <span class="value"><?= request()->getScheme() ?> (<?= request()->secure() ? 'HTTPS ✅' : 'HTTP ❌' ?>)</span></p>
    </div>

    <div class="section">
        <h2>Session Configuration</h2>
        <p><span class="label">SESSION_DRIVER:</span> <span class="value"><?= config('session.driver') ?></span></p>
        <p><span class="label">SESSION_DOMAIN:</span> <span class="value"><?= config('session.domain') ?: 'null' ?></span></p>
        <p><span class="label">SESSION_SECURE_COOKIE:</span> <span class="value"><?= config('session.secure') ? 'true ✅' : 'false ❌' ?></span></p>
        <p><span class="label">SESSION_HTTP_ONLY:</span> <span class="value"><?= config('session.http_only') ? 'true' : 'false' ?></span></p>
        <p><span class="label">SESSION_SAME_SITE:</span> <span class="value"><?= config('session.same_site') ?></span></p>
        <p><span class="label">SESSION_LIFETIME:</span> <span class="value"><?= config('session.lifetime') ?> minutes</span></p>
    </div>

    <div class="section">
        <h2>Current Session</h2>
        <p><span class="label">Has Session:</span> <span class="value"><?= request()->hasSession() ? 'Yes ✅' : 'No ❌' ?></span></p>
        <?php if (request()->hasSession()): ?>
            <p><span class="label">Session ID:</span> <span class="value"><?= session()->getId() ?></span></p>
            <p><span class="label">Session Name:</span> <span class="value"><?= session()->getName() ?></span></p>
            <p><span class="label">Session Started:</span> <span class="value"><?= session()->isStarted() ? 'Yes' : 'No' ?></span></p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>Authentication Status</h2>
        <?php if (Auth::check()): ?>
            <p class="success">✅ User Authenticated</p>
            <p><span class="label">User ID:</span> <span class="value"><?= Auth::id() ?></span></p>
            <p><span class="label">User Email:</span> <span class="value"><?= Auth::user()->email ?></span></p>
            <p><span class="label">User Role:</span> <span class="value"><?= Auth::user()->role ?></span></p>
            <p><span class="label">Is Admin:</span> <span class="value"><?= in_array(Auth::user()->role, ['admin', 'superadmin']) ? 'Yes ✅' : 'No ❌' ?></span></p>
        <?php else: ?>
            <p class="error">❌ User NOT Authenticated</p>
            <p class="warning">⚠️ Login dulu sebagai admin, baru akses halaman ini!</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>Database Session Check</h2>
        <?php
        try {
            if (config('session.driver') === 'database') {
                $sessionId = session()->getId();
                $sessionData = DB::table('sessions')->where('id', $sessionId)->first();

                if ($sessionData) {
                    echo '<p class="success">✅ Session found in database</p>';
                    echo '<p><span class="label">Last Activity:</span> <span class="value">' . date('Y-m-d H:i:s', $sessionData->last_activity) . '</span></p>';
                    echo '<p><span class="label">User ID:</span> <span class="value">' . ($sessionData->user_id ?: 'null') . '</span></p>';
                    echo '<p><span class="label">IP Address:</span> <span class="value">' . $sessionData->ip_address . '</span></p>';
                    echo '<p><span class="label">User Agent:</span> <span class="value">' . substr($sessionData->user_agent, 0, 100) . '...</span></p>';
                } else {
                    echo '<p class="error">❌ Session NOT found in database</p>';
                }

                $totalSessions = DB::table('sessions')->count();
                echo '<p><span class="label">Total Sessions in DB:</span> <span class="value">' . $totalSessions . '</span></p>';
            } else {
                echo '<p class="warning">⚠️ Session driver is not database</p>';
            }
        } catch (Exception $e) {
            echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>Cookies Received</h2>
        <?php if (!empty($_COOKIE)): ?>
            <pre><?php print_r(array_keys($_COOKIE)); ?></pre>
            <?php
            $sessionCookieName = config('session.cookie');
            if (isset($_COOKIE[$sessionCookieName])) {
                echo '<p class="success">✅ Session cookie found: ' . $sessionCookieName . '</p>';
            } else {
                echo '<p class="error">❌ Session cookie NOT found: ' . $sessionCookieName . '</p>';
            }
            ?>
        <?php else: ?>
            <p class="error">❌ No cookies received from browser</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>Request Headers</h2>
        <pre><?php
                $headers = request()->headers->all();
                foreach ($headers as $key => $value) {
                    if (in_array(strtolower($key), ['cookie', 'x-forwarded-for', 'x-forwarded-proto', 'host'])) {
                        echo "$key: " . implode(', ', $value) . "\n";
                    }
                }
                ?></pre>
    </div>

    <div class="section">
        <h2>TrustProxies Check</h2>
        <?php
        $trustProxiesExists = class_exists('App\Http\Middleware\TrustProxies');
        echo '<p><span class="label">TrustProxies Middleware:</span> ';
        echo $trustProxiesExists ? '<span class="success">✅ Exists</span>' : '<span class="error">❌ Not Found</span>';
        echo '</p>';

        if ($trustProxiesExists) {
            $middleware = new App\Http\Middleware\TrustProxies(app());
            $reflection = new ReflectionClass($middleware);
            $proxiesProperty = $reflection->getProperty('proxies');
            $proxiesProperty->setAccessible(true);
            $proxies = $proxiesProperty->getValue($middleware);
            echo '<p><span class="label">Trusted Proxies:</span> <span class="value">' . ($proxies === '*' ? 'All (*)' : json_encode($proxies)) . '</span></p>';
        }
        ?>
    </div>

    <div class="section">
        <h2>Recommendations</h2>
        <?php
        $issues = [];

        if (config('app.env') === 'production' && config('app.debug') === true) {
            $issues[] = '❌ APP_DEBUG is TRUE in production - set to false!';
        }

        if (request()->secure() && !config('session.secure')) {
            $issues[] = '❌ HTTPS detected but SESSION_SECURE_COOKIE is false - set to true!';
        }

        if (!config('session.domain')) {
            $issues[] = '⚠️ SESSION_DOMAIN is null - consider setting to .cuaninstan.my.id';
        }

        if (!Auth::check()) {
            $issues[] = '⚠️ Not authenticated - login as admin first!';
        }

        if (!$trustProxiesExists) {
            $issues[] = '❌ TrustProxies middleware not found - create it!';
        }

        if (empty($issues)) {
            echo '<p class="success">✅ No obvious issues detected!</p>';
        } else {
            foreach ($issues as $issue) {
                echo '<p>' . $issue . '</p>';
            }
        }
        ?>
    </div>

    <div class="section">
        <p style="color: #f48771; font-weight: bold;">
            ⚠️ SECURITY WARNING: DELETE THIS FILE AFTER DEBUGGING!
        </p>
        <p>File location: <code><?= __FILE__ ?></code></p>
        <p>Delete command: <code>rm <?= __FILE__ ?></code></p>
    </div>
</body>

</html>