#!/usr/bin/env bash

set -e

echo "========================================"
echo " CHEGENIROOSTA PRODUCTION CHECK"
echo "========================================"

echo
echo "Laravel:"
php artisan --version

echo
echo "PHP:"
php -v | head -n 1

echo
echo "Composer:"
composer --version

echo
echo "Environment:"
grep "^APP_ENV=" .env || true
grep "^APP_DEBUG=" .env || true
grep "^APP_BASE_DOMAIN=" .env || true

echo
echo "Required Laravel files:"

test -f artisan && echo "artisan: OK"
test -f composer.json && echo "composer.json: OK"
test -f .env && echo ".env: OK"
test -f app/Http/Middleware/IdentifyVillage.php && echo "IdentifyVillage: OK"
test -f app/Http/Middleware/CheckVillageAccess.php && echo "CheckVillageAccess: OK"

echo
echo "Database:"
test -f database/database.sqlite && echo "SQLite database: OK"

echo
echo "Storage:"
test -d storage && echo "storage: OK"
test -d storage/app && echo "storage/app: OK"

echo
echo "Routes:"
php artisan route:list | grep -E "admin|village|sitemap" | head -n 30

echo
echo "Cache:"
php artisan optimize:clear

echo
echo "========================================"
echo " PRODUCTION CHECK COMPLETE"
echo "========================================"
