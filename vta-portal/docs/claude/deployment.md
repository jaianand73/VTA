# VTA Portal — Deployment Guide

## Environment Summary

| Item | Local (dev) | Production (Bluehost) |
|------|------------|----------------------|
| URL | `http://localhost/vta-portal/public` | `http://129.121.92.159/vta-portal/public` |
| Code path | `C:\xampp\htdocs\VTA_NEW\vta-portal` | `/var/www/nett-apps/vta-portal/` |
| DB name | `vta_portal` | `vta_portal` |
| DB user | `root` (XAMPP default) | `vtauser` |
| DB password | (blank or root) | `VtaPortal@2026!` |
| DB host | `localhost` | `localhost` |

## SSH / SCP Access

**ALWAYS use PowerShell tool with `dangerouslyDisableSandbox: true`. Bash tool fails with exit code 58 (sandbox blocks outbound SSH).**

SSH key: `D:\blue\bluehost-key`
SSH user: `root`
SSH host: `129.121.92.159`

### Connect via SSH
```powershell
ssh -i D:\blue\bluehost-key -o StrictHostKeyChecking=no root@129.121.92.159 "your command here"
```

### Upload a file via SCP
```powershell
scp -i D:\blue\bluehost-key -o StrictHostKeyChecking=no "C:\local\path\file.php" root@129.121.92.159:/remote/path/
```

### Run a PHP script on the server
```powershell
# 1. SCP to /tmp/
scp -i D:\blue\bluehost-key -o StrictHostKeyChecking=no "C:\...\script.php" root@129.121.92.159:/tmp/
# 2. Execute
ssh -i D:\blue\bluehost-key -o StrictHostKeyChecking=no root@129.121.92.159 "php /tmp/script.php"
# 3. Clean up
ssh -i D:\blue\bluehost-key -o StrictHostKeyChecking=no root@129.121.92.159 "rm /tmp/script.php"
```

## Standard Deploy Workflow

When a file is changed locally and needs to go to production:

### 1. Upload changed file(s)
```powershell
scp -i D:\blue\bluehost-key -o StrictHostKeyChecking=no "C:\xampp\htdocs\VTA_NEW\vta-portal\app\Http\Controllers\SomeController.php" root@129.121.92.159:/var/www/nett-apps/vta-portal/app/Http/Controllers/
```

### 2. Clear caches (always run after deploying PHP or config files)
```powershell
ssh -i D:\blue\bluehost-key -o StrictHostKeyChecking=no root@129.121.92.159 "cd /var/www/nett-apps/vta-portal && php artisan route:clear && php artisan view:clear && php artisan config:clear && php artisan cache:clear"
```

### 3. Run a migration (if a new migration was added)
```powershell
ssh -i D:\blue\bluehost-key -o StrictHostKeyChecking=no root@129.121.92.159 "cd /var/www/nett-apps/vta-portal && php artisan migrate --force"
```

## Common Artisan Commands

```bash
php artisan route:clear       # Clear route cache
php artisan view:clear        # Clear compiled views
php artisan config:clear      # Clear config cache
php artisan cache:clear       # Clear application cache
php artisan migrate --force   # Run pending migrations (--force needed in production)
php artisan migrate:status    # Show migration status
php artisan tinker            # Laravel REPL
```

## Critical Deployment Rules

- **NEVER SCP or deploy `seed_rich_data.php` or any seeder file to Bluehost.**
- Always clear caches after deploying any `.php` file.
- Always clear route cache after adding or changing routes in `web.php`.
- Always clear view cache after changing Blade templates.
- DB scripts: write locally → SCP to `/tmp/` → run → delete immediately.

## Migration Files (local path)

`C:\xampp\htdocs\VTA_NEW\vta-portal\database\migrations\`

When adding a new column or table:
1. Create migration locally: `php artisan make:migration add_xyz_to_table`
2. Test locally: `php artisan migrate`
3. SCP migration file to production
4. Run on production: `php artisan migrate --force`

## Where Files Live on Production

```
/var/www/nett-apps/vta-portal/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   └── Services/
├── database/
│   └── migrations/
├── resources/
│   └── views/
├── routes/
│   └── web.php
└── storage/
    └── app/vta-documents/   ← uploaded documents (private)
```
