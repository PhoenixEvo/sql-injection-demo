@echo off
echo ========================================
echo Resetting SQL Injection Demo Environment
echo ========================================
echo.
echo WARNING: This will remove all containers and volumes!
echo All data will be lost.
echo.
set /p confirm="Are you sure? (yes/no): "

if /i not "%confirm%"=="yes" (
    echo Operation cancelled.
    pause
    exit /b 0
)

REM Check if Docker is running
docker info >nul 2>&1
if errorlevel 1 (
    echo ERROR: Docker is not running!
    pause
    exit /b 1
)

echo.
echo Stopping containers...
docker-compose down -v

if errorlevel 1 (
    echo.
    echo WARNING: Some containers may not have stopped properly.
)

echo.
echo Starting fresh containers...
docker-compose up -d

if errorlevel 1 (
    echo.
    echo ERROR: Failed to start containers!
    pause
    exit /b 1
)

echo.
echo Waiting for database to initialize...
timeout /t 5 /nobreak >nul

echo.
echo ========================================
echo Demo environment reset successfully!
echo ========================================
echo.
echo Web application: http://localhost:8080
echo.
pause

