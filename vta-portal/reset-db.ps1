$VtaDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$Php = "C:\xampp\php\php.exe"

Write-Host "=== VTA Portal - Reset Database ===" -ForegroundColor Cyan
Write-Host "WARNING: This will delete all data!" -ForegroundColor Red
$confirm = Read-Host "Type 'reset' to continue"
if ($confirm -ne "reset") {
    Write-Host "Cancelled." -ForegroundColor Yellow
    exit
}

Set-Location $VtaDir

Write-Host "[1/2] Dropping all tables and re-running migrations..." -ForegroundColor Yellow
& $Php artisan migrate:fresh --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Migrations failed" -ForegroundColor Red
    exit 1
}

Write-Host "[2/2] Running seeders..." -ForegroundColor Yellow
& $Php artisan db:seed --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Seeding failed" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Database reset complete!" -ForegroundColor Green
Write-Host "Admin:   admin@vta.com / password" -ForegroundColor Green
Write-Host "Staff:   staff@vta.com / password" -ForegroundColor Green
Write-Host "Assoc:   associate@vta.com / password" -ForegroundColor Green
