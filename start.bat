@echo off
echo ========================================
echo Starting SQL Injection Demo Environment
echo ========================================
echo.

REM Check if Docker is running
docker info >nul 2>&1
if errorlevel 1 (
    echo ERROR: Docker is not running!
    echo Please start Docker Desktop and try again.
    pause
    exit /b 1
)

echo Checking Docker containers...
docker-compose ps

echo.
echo Starting containers...
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
echo Demo environment is ready!
echo ========================================
echo.
echo Web application: http://localhost:8080
echo Database: localhost:3306
echo.
echo Press any key to open the application in your browser...
pause >nul

start http://localhost:8080

echo.
echo To stop the demo, run: end.bat
echo.
pause

