@echo off
echo ========================================
echo   Starting Real-Time Chat Services
echo ========================================
echo.

echo [1/3] Starting Laravel Reverb Server...
start "Laravel Reverb" cmd /k "php artisan reverb:start"
timeout /t 2 /nobreak >nul

echo [2/3] Starting Laravel Development Server...
start "Laravel Server" cmd /k "php artisan serve"
timeout /t 2 /nobreak >nul

echo [3/3] Starting Vite Dev Server...
start "Vite Dev" cmd /k "npm run dev"
timeout /t 2 /nobreak >nul

echo.
echo ========================================
echo   All Services Started Successfully!
echo ========================================
echo.
echo Laravel:  http://localhost:8000
echo Reverb:   http://localhost:8080
echo Vite:     http://localhost:5173
echo.
echo Press any key to stop all services...
pause >nul

taskkill /FI "WINDOWTITLE eq Laravel Reverb*" /T /F
taskkill /FI "WINDOWTITLE eq Laravel Server*" /T /F
taskkill /FI "WINDOWTITLE eq Vite Dev*" /T /F

echo All services stopped.
