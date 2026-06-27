$VtaDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$Php = "C:\xampp\php\php.exe"
$Mysql = "C:\xampp\mysql\bin\mysql.exe"
$Mysqld = "C:\xampp\mysql\bin\mysqld.exe"

Write-Host "=== VTA Portal - Development Server ===" -ForegroundColor Cyan

# Start MySQL if not running
$mysqlRunning = Get-Process mysqld -ErrorAction SilentlyContinue
if (-not $mysqlRunning) {
    Write-Host "[1/3] Starting MySQL..." -ForegroundColor Yellow
    Start-Process -FilePath $Mysqld -WindowStyle Hidden
    Start-Sleep -Seconds 3
} else {
    Write-Host "[1/3] MySQL already running" -ForegroundColor Green
}

# Confirm MySQL is up
try {
    & $Mysql -u root -e "SELECT 1" | Out-Null
    Write-Host "       MySQL connected" -ForegroundColor Green
} catch {
    Write-Host "       ERROR: Could not connect to MySQL" -ForegroundColor Red
    exit 1
}

# Run migrations if pending
Write-Host "[2/3] Checking migrations..." -ForegroundColor Yellow
Set-Location $VtaDir
$output = & $Php artisan migrate --force 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "       Migrations up to date" -ForegroundColor Green
} else {
    Write-Host "       Migration output: $output" -ForegroundColor Yellow
}

# Start Laravel dev server
Write-Host "[3/3] Starting Laravel dev server on http://localhost:8080" -ForegroundColor Yellow
Write-Host ""
Write-Host "Admin:   admin@vta.com / password" -ForegroundColor Green
Write-Host "Staff:   staff@vta.com / password" -ForegroundColor Green
Write-Host "Assoc:   associate@vta.com / password" -ForegroundColor Green
Write-Host ""
Write-Host "Press Ctrl+C to stop." -ForegroundColor Gray
Write-Host ""

& $Php artisan serve --port=8080
