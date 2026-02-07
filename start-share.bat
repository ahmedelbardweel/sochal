@echo off
title UpScrolling Public Share Manager (SSH Method)
color 0E

echo ===================================================
echo   UpScrolling FREE Public Share (via SSH Tunnels)
echo ===================================================
echo   NOTE: This uses SSH to bypass LocalTunnel errors.
echo   First time running? Type 'yes' if asked "Are you sure..."
echo ===================================================
echo.

echo [1/4] Starting Laravel Server (Port 8000)...
start "Laravel App" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"

echo [2/4] Starting Signaling Server (Port 8080)...
start "Signaling Server" cmd /k "cd signaling-gateway && npm run dev"

echo [3/4] Starting SFU Server (Port 8888)...
start "SFU Server" cmd /k "cd sfu-server && go run main.go"

echo.
echo ===================================================
echo   STARTING PUBLIC TUNNELS (localhost.run)
echo ===================================================
echo.
echo We will open 3 windows. Each will give you a random URL 
echo looking like "https://something.localhost.run".
echo.

echo [Tunnel 1] Exposing Main Website (Port 8000)...
start "PUBLIC URL - WEBSITE" cmd /k "ssh -R 80:127.0.0.1:8000 nokey@localhost.run"

echo [Tunnel 2] Exposing Signaling (Port 8080)...
start "PUBLIC URL - SIGNALING" cmd /k "ssh -R 80:127.0.0.1:8080 nokey@localhost.run"

echo [Tunnel 3] Exposing SFU (Port 8888)...
start "PUBLIC URL - SFU" cmd /k "ssh -R 80:127.0.0.1:8888 nokey@localhost.run"

echo.
echo ===================================================
echo   INSTRUCTIONS
echo ===================================================
echo 1. Wait for the 3 "PUBLIC URL" windows.
echo 2. If prompted "Are you sure you want to continue connecting", type "yes" and Enter.
echo 3. Copy the "https://..." URL from the WEBSITE window and open it on your phone.
echo 4. Go to profile -> Settings -> Developer Settings.
echo 5. Copy & Paste the SIGNALING URL (e.g. https://xyz.localhost.run)
echo 6. Copy & Paste the SFU URL (e.g. https://abc.localhost.run)
echo    *IMPORTANT: Remove the trailing slash / if present!*
echo 7. Save and refresh!
echo.
pause
