@echo off
REM Fix File Permissions for AC Controller System (Windows)
REM This checks if required directories exist locally

echo ==========================================
echo Checking Directory Structure (Windows)
echo ==========================================
echo.

REM Check if storage directory exists
if exist "storage\" (
    echo [OK] storage\ directory exists
) else (
    echo [CREATE] Creating storage\ directory...
    mkdir "storage"
)

REM Check if cache directory exists
if exist "storage\cache\" (
    echo [OK] storage\cache\ directory exists
) else (
    echo [CREATE] Creating storage\cache\ directory...
    mkdir "storage\cache"
    echo. > "storage\cache\.gitkeep"
)

REM Check if logs directory exists
if exist "storage\logs\" (
    echo [OK] storage\logs\ directory exists
) else (
    echo [CREATE] Creating storage\logs\ directory...
    mkdir "storage\logs"
    echo. > "storage\logs\.gitkeep"
)

REM Check .env file
if exist ".env" (
    echo [OK] .env file exists
) else (
    echo [WARNING] .env file NOT found!
    if exist ".env.example" (
        echo [INFO] .env.example found - copy it to .env and configure
    )
)

echo.
echo ==========================================
echo Directory Check Complete!
echo ==========================================
echo.
echo Summary:
echo   - storage\ directory: OK
echo   - storage\cache\ directory: OK
echo   - storage\logs\ directory: OK
echo.
echo For online server:
echo   1. Upload all files including empty directories
echo   2. Run fix-permissions.sh via SSH (Linux)
echo   3. Or set permissions via cPanel File Manager
echo.
pause
