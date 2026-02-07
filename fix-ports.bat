@echo off
title UpScrolling - Fix Ports (Cleaner)
color 0C

echo ===================================================
echo   FIXING SERVERS (Killing Stuck Processes)
echo ===================================================
echo.

echo [1/4] Stopping Node.js (Signaling)...
taskkill /F /IM node.exe >nul 2>&1
if %ERRORLEVEL% EQU 0 ( echo    - Stopped Node.js ) else ( echo    - Node.js was not running )

echo [2/4] Stopping PHP (Laravel)...
taskkill /F /IM php.exe >nul 2>&1
if %ERRORLEVEL% EQU 0 ( echo    - Stopped PHP ) else ( echo    - PHP was not running )

echo [3/4] Stopping Go (SFU)...
taskkill /F /IM main.exe >nul 2>&1
if %ERRORLEVEL% EQU 0 ( echo    - Stopped SFU ) else ( echo    - SFU was not running )
taskkill /F /IM go.exe >nul 2>&1

echo [4/4] Closing Tunnels (SSH)...
taskkill /F /IM ssh.exe >nul 2>&1
if %ERRORLEVEL% EQU 0 ( echo    - Stopped Tunnels ) else ( echo    - Tunnels were not running )

echo.
echo ===================================================
echo   CLEANUP COMPLETE! 
echo ===================================================
echo.
echo Now you can run "start-share.bat" again without errors.
echo.
pause
