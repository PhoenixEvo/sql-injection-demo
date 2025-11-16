@echo off
echo ========================================
echo Stopping SQL Injection Demo Environment
echo ========================================
echo.

REM Check if Docker is running
docker info >nul 2>&1
if errorlevel 1 (
    echo ERROR: Docker is not running!
    echo Cannot stop containers.
    pause
    exit /b 1
)

echo Stopping and removing containers...
docker-compose down

if errorlevel 1 (
    echo.
    echo WARNING: Some containers may not have stopped properly.
    echo You may need to stop them manually.
) else (
    echo.
    echo Containers stopped successfully!
)

echo.
echo ========================================
echo Demo environment stopped
echo ========================================
echo.
pause

