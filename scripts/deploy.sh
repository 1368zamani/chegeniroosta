#!/usr/bin/env bash
# Deploy helper for chegeniroosta.ir
# Run as root:  sudo bash scripts/deploy.sh [branch]
set -euo pipefail

APP_DIR=/var/www/chegeniroosta
BRANCH="${1:-main}"
PHP=/usr/bin/php
FPM=php8.5-fpm
QUEUE=chegeniroosta-queue

cd "$APP_DIR"

echo "==> 1/6 backup (code + database)"
/root/comprehensive-backup.sh >/dev/null 2>&1 || echo "WARN: backup script failed, continuing"

echo "==> 2/6 fetch and fast-forward $BRANCH"
sudo -u www-data git fetch origin
sudo -u www-data git checkout "$BRANCH"
sudo -u www-data git pull --ff-only origin "$BRANCH"

echo "==> 3/6 composer install"
sudo -u www-data composer install --no-interaction --optimize-autoloader --no-progress

echo "==> 4/6 migrations"
sudo -u www-data "$PHP" artisan migrate --force

echo "==> 5/6 rebuild caches"
sudo -u www-data "$PHP" artisan optimize:clear
sudo -u www-data "$PHP" artisan optimize

echo "==> 6/6 reload services (required: opcache.validate_timestamps=0)"
systemctl reload "$FPM"
systemctl restart "$QUEUE"

echo "==> health check"
for h in chegeniroosta.ir visian.chegeniroosta.ir markazi.chegeniroosta.ir shahivand.chegeniroosta.ir; do
  printf '%-32s ' "$h"
  curl -s -o /dev/null -w '%{http_code}\n' --max-time 15 -H "Host: $h" https://127.0.0.1/ -k
done

echo "==> done"
