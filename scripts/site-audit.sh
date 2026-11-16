#!/usr/bin/env bash

# ============================================================
# CHEGENIROOSTA / VISIAN
# FULL READ-ONLY PRODUCTION SITE AUDIT
#
# Stack:
# Laravel / PHP / Nginx / SQLite / Vite
#
# IMPORTANT:
# This script is READ-ONLY.
# It does NOT:
#   - migrate database
#   - update packages
#   - restart services
#   - modify Nginx
#   - modify SSL
#   - modify .env
#   - delete files
#
# ============================================================

set +e

APP="/var/www/chegeniroosta"
DOMAIN="https://visian.chegeniroosta.ir"
HOST="visian.chegeniroosta.ir"

DATE="$(date +%Y%m%d-%H%M%S)"
REPORT_DIR="/root/site-audit-${DATE}"
REPORT="${REPORT_DIR}/report.txt"
ROUTES_JSON="${REPORT_DIR}/routes.json"

mkdir -p "$REPORT_DIR"

# ------------------------------------------------------------
# Logging
# ------------------------------------------------------------

exec > >(tee -a "$REPORT") 2>&1

OK_COUNT=0
WARN_COUNT=0
FAIL_COUNT=0

ok() {
    echo "[OK]      $1"
    OK_COUNT=$((OK_COUNT+1))
}

warn() {
    echo "[WARNING] $1"
    WARN_COUNT=$((WARN_COUNT+1))
}

fail() {
    echo "[FAIL]    $1"
    FAIL_COUNT=$((FAIL_COUNT+1))
}

info() {
    echo "[INFO]    $1"
}

section() {
    echo
    echo
    echo "================================================================"
    echo "$1"
    echo "================================================================"
}

command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# ============================================================
# START
# ============================================================

section "00. AUDIT START"

echo "Date:        $(date)"
echo "Hostname:    $(hostname)"
echo "Application: $APP"
echo "Domain:      $DOMAIN"
echo "Report:      $REPORT_DIR"
echo

if [ "$(id -u)" -ne 0 ]; then
    warn "Script is not running as root. Some checks may be incomplete."
else
    ok "Running as root"
fi

# ============================================================
# 01 SERVER
# ============================================================

section "01. SERVER INFORMATION"

echo "Hostname:"
hostname

echo
echo "Operating System:"
cat /etc/os-release 2>/dev/null

echo
echo "Kernel:"
uname -a

echo
echo "Uptime:"
uptime

echo
echo "CPU:"
nproc

echo
echo "Memory:"
free -h

echo
echo "Disk:"
df -h

echo
echo "Load:"
cat /proc/loadavg 2>/dev/null

# ============================================================
# 02 APPLICATION
# ============================================================

section "02. APPLICATION DIRECTORY"

if [ -d "$APP" ]; then
    ok "Application directory exists: $APP"
else
    fail "Application directory does not exist: $APP"
fi

cd "$APP" || exit 1

echo
echo "Current directory:"
pwd

echo
echo "Top-level files:"
ls -lah

echo
echo "Application size:"
du -sh "$APP" 2>/dev/null

# ============================================================
# 03 LARAVEL
# ============================================================

section "03. LARAVEL"

if [ -f artisan ]; then
    ok "artisan exists"
else
    fail "artisan does not exist"
fi

if [ -f composer.json ]; then
    ok "composer.json exists"
else
    fail "composer.json does not exist"
fi

if [ -f composer.lock ]; then
    ok "composer.lock exists"
else
    warn "composer.lock does not exist"
fi

if [ -f .env ]; then
    ok ".env exists"
else
    fail ".env does not exist"
fi

echo
echo "Laravel version:"
php artisan --version 2>&1

echo
echo "Laravel about:"
php artisan about 2>&1

# ============================================================
# 04 PHP
# ============================================================

section "04. PHP"

if command_exists php; then
    ok "PHP installed"
    php -v
else
    fail "PHP is not installed"
fi

echo
echo "PHP binary:"
which php 2>/dev/null

echo
echo "PHP modules:"
php -m 2>&1

echo
echo "PHP configuration:"
php --ini 2>&1

# ============================================================
# 05 COMPOSER
# ============================================================

section "05. COMPOSER"

if command_exists composer; then
    ok "Composer installed"
    composer --version 2>&1

    echo
    echo "Direct dependencies:"
    composer show --direct 2>&1
else
    warn "Composer command not found"
fi

# ============================================================
# 06 NODE / NPM / VITE
# ============================================================

section "06. NODE / NPM / VITE"

if command_exists node; then
    ok "Node installed"
    node -v
else
    warn "Node is not installed"
fi

if command_exists npm; then
    ok "npm installed"
    npm -v
else
    warn "npm is not installed"
fi

if [ -f package.json ]; then
    ok "package.json exists"

    echo
    echo "package.json:"
    cat package.json
else
    info "package.json not found"
fi

echo
echo "Vite files:"
find . -maxdepth 2 -type f \
    \( -name "vite.config.js" -o -name "vite.config.ts" \) \
    -print 2>/dev/null

echo
echo "Compiled frontend:"
if [ -d public/build ]; then
    ok "public/build exists"
    du -sh public/build 2>/dev/null
else
    warn "public/build does not exist"
fi

# ============================================================
# 07 ENVIRONMENT - SAFE
# ============================================================

section "07. ENVIRONMENT CONFIGURATION"

if [ -f .env ]; then

    echo "APP_NAME:"
    grep '^APP_NAME=' .env 2>/dev/null | sed 's/=.*$/=[HIDDEN]/'

    echo "APP_ENV:"
    grep '^APP_ENV=' .env 2>/dev/null

    echo "APP_DEBUG:"
    grep '^APP_DEBUG=' .env 2>/dev/null

    echo "APP_URL:"
    grep '^APP_URL=' .env 2>/dev/null

    echo "DB_CONNECTION:"
    grep '^DB_CONNECTION=' .env 2>/dev/null

    echo "DB_DATABASE:"
    grep '^DB_DATABASE=' .env 2>/dev/null | sed 's/=.*$/=[HIDDEN]/'

    echo "CACHE_STORE:"
    grep '^CACHE_STORE=' .env 2>/dev/null

    echo "SESSION_DRIVER:"
    grep '^SESSION_DRIVER=' .env 2>/dev/null

    echo "QUEUE_CONNECTION:"
    grep '^QUEUE_CONNECTION=' .env 2>/dev/null

    echo
    echo "Sensitive variables:"
    grep -E \
    '^(APP_KEY|DB_PASSWORD|DB_USERNAME|MAIL_PASSWORD|AWS_SECRET|AWS_ACCESS_KEY|STRIPE_|TELEGRAM_|TOKEN|PASSWORD|SECRET)' \
    .env 2>/dev/null \
    | sed 's/=.*$/=[HIDDEN]/'

else
    fail ".env does not exist"
fi

# ============================================================
# 08 ROUTES
# ============================================================

section "08. LARAVEL ROUTES"

echo "Route list:"
php artisan route:list 2>&1

echo
echo "Route count:"
php artisan route:list 2>/dev/null | wc -l

echo
echo "Routes JSON:"
php artisan route:list --json > "$ROUTES_JSON" 2>/dev/null

if [ -s "$ROUTES_JSON" ]; then
    ok "Route JSON generated"
else
    warn "Could not generate route JSON"
fi

# ============================================================
# 09 ROUTE FILES
# ============================================================

section "09. ROUTE FILES"

find routes -type f -print 2>/dev/null

echo
for FILE in routes/*.php; do
    [ -f "$FILE" ] || continue

    echo
    echo "---------------- $FILE ----------------"
    sed -n '1,400p' "$FILE"
done

# ============================================================
# 10 CONTROLLERS
# ============================================================

section "10. CONTROLLERS"

find app/Http/Controllers \
    -type f \
    -name "*.php" \
    -print 2>/dev/null

echo
echo "Controller count:"
find app/Http/Controllers \
    -type f \
    -name "*.php" \
    2>/dev/null | wc -l

# ============================================================
# 11 MODELS
# ============================================================

section "11. MODELS"

find app/Models \
    -type f \
    -name "*.php" \
    -print 2>/dev/null

echo
echo "Model count:"
find app/Models \
    -type f \
    -name "*.php" \
    2>/dev/null | wc -l

# ============================================================
# 12 MIDDLEWARE
# ============================================================

section "12. MIDDLEWARE"

find app/Http/Middleware \
    -type f \
    -name "*.php" \
    -print 2>/dev/null

# ============================================================
# 13 BLADE VIEWS
# ============================================================

section "13. BLADE VIEWS"

find resources/views \
    -type f \
    -name "*.blade.php" \
    -print 2>/dev/null

echo
echo "Blade view count:"
find resources/views \
    -type f \
    -name "*.blade.php" \
    2>/dev/null | wc -l

# ============================================================
# 14 FRONTEND
# ============================================================

section "14. FRONTEND ASSETS"

echo "CSS:"
find resources \
    -type f \
    \( -name "*.css" -o -name "*.scss" \) \
    -print 2>/dev/null

echo
echo "JavaScript:"
find resources \
    -type f \
    \( -name "*.js" -o -name "*.ts" \) \
    -print 2>/dev/null

echo
echo "Public CSS/JS:"
find public \
    -type f \
    \( -name "*.css" -o -name "*.js" \) \
    -print 2>/dev/null | head -500

echo
echo "Images:"
find public \
    -type f \
    \( -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" -o -name "*.webp" -o -name "*.gif" -o -name "*.svg" \) \
    -print 2>/dev/null | head -500

# ============================================================
# 15 DATABASE
# ============================================================

section "15. DATABASE"

echo "Database configuration:"
php artisan config:show database 2>&1

echo
echo "SQLite databases:"
find . \
    -type f \
    \( -name "*.sqlite" -o -name "*.sqlite3" \) \
    -print 2>/dev/null

if [ -f database/database.sqlite ]; then

    ok "database/database.sqlite exists"

    echo
    echo "Database size:"
    ls -lh database/database.sqlite

    if command_exists sqlite3; then

        echo
        echo "SQLite integrity:"
        sqlite3 database/database.sqlite \
            "PRAGMA integrity_check;" 2>&1

        echo
        echo "SQLite tables:"
        sqlite3 database/database.sqlite \
            ".tables" 2>&1

    else
        warn "sqlite3 command is not installed"
    fi

else
    warn "database/database.sqlite does not exist"
fi

echo
echo "Migration status:"
php artisan migrate:status 2>&1

# ============================================================
# 16 STORAGE
# ============================================================

section "16. STORAGE"

echo "storage:"
ls -lah storage 2>&1

echo
echo "storage/app:"
ls -lah storage/app 2>&1

echo
echo "storage/framework:"
find storage/framework \
    -maxdepth 2 \
    -type d \
    -print 2>/dev/null

echo
echo "storage/logs:"
ls -lah storage/logs 2>&1

echo
echo "public/storage:"
if [ -L public/storage ]; then
    ok "public/storage is a symbolic link"
    ls -lah public/storage
    readlink -f public/storage
else
    warn "public/storage symbolic link does not exist"
fi

# ============================================================
# 17 PERMISSIONS
# ============================================================

section "17. PERMISSIONS"

echo "Application:"
stat -c '%U:%G %a %n' "$APP" 2>/dev/null

echo
echo "storage:"
stat -c '%U:%G %a %n' storage 2>/dev/null

echo
echo "storage/app:"
stat -c '%U:%G %a %n' storage/app 2>/dev/null

echo
echo "storage/logs:"
stat -c '%U:%G %a %n' storage/logs 2>/dev/null

echo
echo "bootstrap/cache:"
stat -c '%U:%G %a %n' bootstrap/cache 2>/dev/null

echo
echo "database:"
stat -c '%U:%G %a %n' database 2>/dev/null

echo
if [ -w storage ]; then
    ok "storage writable"
else
    fail "storage NOT writable"
fi

if [ -w bootstrap/cache ]; then
    ok "bootstrap/cache writable"
else
    fail "bootstrap/cache NOT writable"
fi

if [ -w database ] || [ -w database/database.sqlite ]; then
    ok "database writable"
else
    warn "database may not be writable"
fi

# ============================================================
# 18 CACHE
# ============================================================

section "18. LARAVEL CACHE"

echo "Bootstrap cache:"
find bootstrap/cache \
    -maxdepth 1 \
    -type f \
    -printf '%f\n' 2>/dev/null

echo
echo "Framework cache:"
find storage/framework \
    -maxdepth 2 \
    -type f \
    -print 2>/dev/null | head -300

# ============================================================
# 19 LOGS
# ============================================================

section "19. LARAVEL LOGS"

echo "Log files:"
find storage/logs \
    -type f \
    -print 2>/dev/null

LATEST_LOG="$(
    find storage/logs \
    -type f \
    -printf '%T@ %p\n' 2>/dev/null \
    | sort -nr \
    | head -1 \
    | cut -d' ' -f2-
)"

echo
if [ -n "$LATEST_LOG" ] && [ -f "$LATEST_LOG" ]; then

    echo "Latest log:"
    echo "$LATEST_LOG"

    echo
    echo "Last 300 lines:"
    tail -n 300 "$LATEST_LOG"

else

    info "No Laravel log file found"

fi

echo
echo "Laravel error count:"
grep -RniE \
    "ERROR|CRITICAL|EMERGENCY|Exception|Fatal error" \
    storage/logs 2>/dev/null | wc -l

# ============================================================
# 20 NGINX
# ============================================================

section "20. NGINX FULL AUDIT"

if command_exists nginx; then

    ok "Nginx installed"

    echo
    echo "Version:"
    nginx -v 2>&1

    echo
    echo "Binary:"
    which nginx

    echo
    echo "Service active:"
    systemctl is-active nginx 2>&1

    echo
    echo "Service enabled:"
    systemctl is-enabled nginx 2>&1

    echo
    echo "Service status:"
    systemctl status nginx --no-pager -l 2>&1

    echo
    echo "Configuration test:"
    nginx -t 2>&1

    echo
    echo "Enabled sites:"
    find /etc/nginx/sites-enabled \
        -maxdepth 1 \
        -type l \
        -ls 2>/dev/null

    echo
    echo "Available sites:"
    find /etc/nginx/sites-available \
        -maxdepth 1 \
        -type f \
        -ls 2>/dev/null

    echo
    echo "Server names:"
    nginx -T 2>/dev/null \
        | grep -E "server_name" \
        | head -300

    echo
    echo "Document roots:"
    nginx -T 2>/dev/null \
        | grep -E "^[[:space:]]*root " \
        | head -300

    echo
    echo "Listen directives:"
    nginx -T 2>/dev/null \
        | grep -E "^[[:space:]]*listen " \
        | head -300

    echo
    echo "PHP-FPM:"
    nginx -T 2>/dev/null \
        | grep -Ei "fastcgi_pass|php[0-9]" \
        | head -300

    echo
    echo "SSL configuration:"
    nginx -T 2>/dev/null \
        | grep -Ei \
        "ssl_certificate|ssl_protocols|ssl_ciphers|ssl_session|ssl_stapling" \
        | head -300

    echo
    echo "Security headers:"
    nginx -T 2>/dev/null \
        | grep -Ei \
        "add_header|Strict-Transport|Content-Security|X-Frame|X-Content|Referrer|Permissions-Policy" \
        | head -300

else

    fail "Nginx is not installed"

fi

# ============================================================
# 21 PHP-FPM
# ============================================================

section "21. PHP-FPM"

echo "Processes:"
ps aux | grep '[p]hp-fpm' 2>&1

echo
echo "PHP-FPM services:"
systemctl list-units \
    --type=service \
    --all 2>/dev/null \
    | grep -Ei "php.*fpm" \
    | head -100

echo
echo "PHP-FPM sockets:"
ss -lx 2>/dev/null | grep -Ei "php|fpm" || true

echo
echo "PHP-FPM ports:"
ss -lntp 2>/dev/null | grep -Ei "php|fpm" || true

# ============================================================
# 22 SERVICES
# ============================================================

section "22. IMPORTANT SERVICES"

for SERVICE in \
    nginx \
    php8.5-fpm \
    php8.4-fpm \
    php8.3-fpm \
    cron \
    supervisor
do

    if systemctl list-unit-files \
        "${SERVICE}.service" >/dev/null 2>&1; then

        echo
        echo "----- $SERVICE -----"

        systemctl is-active "$SERVICE" 2>&1
        systemctl is-enabled "$SERVICE" 2>&1

    fi

done

# ============================================================
# 23 QUEUE
# ============================================================

section "23. QUEUE / WORKERS"

grep '^QUEUE_CONNECTION=' .env 2>/dev/null

echo
echo "Laravel workers:"
ps aux | grep -E \
    '[p]hp artisan (queue:work|queue:listen)' \
    2>&1

echo
echo "Supervisor:"
if command_exists supervisorctl; then
    supervisorctl status 2>&1
else
    info "Supervisor not installed"
fi

# ============================================================
# 24 CRON
# ============================================================

section "24. CRON"

echo "Root crontab:"
crontab -l 2>&1

echo
echo "System cron references:"
grep -Rni \
    "artisan\|chegeniroosta" \
    /etc/cron* 2>/dev/null

# ============================================================
# 25 WEBSITE BASIC HTTP
# ============================================================

section "25. WEBSITE HTTP / HTTPS"

echo "HTTPS HEAD:"
curl -I \
    -L \
    --connect-timeout 10 \
    --max-time 30 \
    "$DOMAIN/" 2>&1

echo
echo "HTTPS status:"
curl -L \
    -s \
    -o /dev/null \
    -w \
"HTTP_CODE=%{http_code}
TIME=%{time_total}s
SIZE=%{size_download}
REMOTE_IP=%{remote_ip}
EFFECTIVE_URL=%{url_effective}
SSL_VERIFY=%{ssl_verify_result}
" \
    --connect-timeout 10 \
    --max-time 30 \
    "$DOMAIN/"

# ============================================================
# 26 COMMON URL TEST
# ============================================================

section "26. COMMON URL TESTS"

URLS=(
    "/"
    "/up"
    "/robots.txt"
    "/sitemap.xml"
    "/favicon.ico"
    "/news"
    "/gallery"
    "/contact"
    "/about"
)

for URL in "${URLS[@]}"; do

    RESULT="$(
        curl -L \
        -s \
        -o /dev/null \
        -w "%{http_code}|%{time_total}|%{url_effective}" \
        --connect-timeout 10 \
        --max-time 20 \
        "$DOMAIN$URL"
    )"

    CODE="$(echo "$RESULT" | cut -d'|' -f1)"
    TIME="$(echo "$RESULT" | cut -d'|' -f2)"
    FINAL="$(echo "$RESULT" | cut -d'|' -f3)"

    printf "%-25s HTTP=%-5s TIME=%-8s FINAL=%s\n" \
        "$URL" "$CODE" "$TIME" "$FINAL"

done

# ============================================================
# 27 AUTOMATIC GET ROUTE TEST
# ============================================================

section "27. AUTOMATIC GET ROUTE TEST"

if [ -s "$ROUTES_JSON" ]; then

python3 - "$ROUTES_JSON" "$DOMAIN" <<'PY'

import json
import sys
import subprocess

routes_file = sys.argv[1]
domain = sys.argv[2].rstrip("/")

try:
    with open(routes_file, encoding="utf-8") as f:
        routes = json.load(f)
except Exception as e:
    print("Could not parse routes.json:", e)
    sys.exit(0)

if isinstance(routes, dict):
    routes = routes.get("routes", [])

for route in routes:

    methods = route.get("method", route.get("methods", ""))
    uri = route.get("uri", "")

    if isinstance(methods, list):
        methods = ",".join(methods)

    if not uri:
        continue

    if "GET" not in str(methods):
        continue

    if "{" in uri:
        print(f"[SKIP PARAM] {uri}")
        continue

    if uri.startswith("_ignition"):
        continue

    url = domain + "/" + uri.lstrip("/")

    try:

        result = subprocess.run(
            [
                "curl",
                "-L",
                "-s",
                "-o",
                "/dev/null",
                "-w",
                "%{http_code}|%{time_total}|%{url_effective}",
                "--connect-timeout",
                "10",
                "--max-time",
                "20",
                url
            ],
            capture_output=True,
            text=True
        )

        print(f"{uri:65} {result.stdout}")

    except Exception as e:
        print(f"{uri:65} ERROR {e}")

PY

else

    warn "routes.json unavailable"

fi

# ============================================================
# 28 GIT
# ============================================================

section "28. GIT"

if [ -d .git ]; then

    ok "Git repository exists"

    echo
    echo "Status:"
    git status --short 2>&1

    echo
    echo "Branch:"
    git branch --show-current 2>&1

    echo
    echo "Recent commits:"
    git log -10 --oneline 2>&1

else

    info "Git repository not found"

fi

# ============================================================
# 29 SECURITY
# ============================================================

section "29. BASIC SECURITY CHECK"

echo "APP_DEBUG:"
grep '^APP_DEBUG=' .env 2>/dev/null

echo
echo "Public .env:"
curl -L \
    -s \
    -o /dev/null \
    -w "HTTP_CODE=%{http_code}\n" \
    --max-time 10 \
    "$DOMAIN/.env"

echo
echo "Public artisan:"
curl -L \
    -s \
    -o /dev/null \
    -w "HTTP_CODE=%{http_code}\n" \
    --max-time 10 \
    "$DOMAIN/artisan"

echo
echo "Public composer.json:"
curl -L \
    -s \
    -o /dev/null \
    -w "HTTP_CODE=%{http_code}\n" \
    --max-time 10 \
    "$DOMAIN/composer.json"

echo
echo "Public package.json:"
curl -L \
    -s \
    -o /dev/null \
    -w "HTTP_CODE=%{http_code}\n" \
    --max-time 10 \
    "$DOMAIN/package.json"

echo
echo "Public database:"
curl -L \
    -s \
    -o /dev/null \
    -w "HTTP_CODE=%{http_code}\n" \
    --max-time 10 \
    "$DOMAIN/database/database.sqlite"

# ============================================================
# 30 LARGE FILES
# ============================================================

section "30. LARGE FILES"

find "$APP" \
    -type f \
    -printf '%s %p\n' 2>/dev/null \
    | sort -nr \
    | head -50 \
    | awk '{
        printf "%.2f MB  %s\n",
        $1/1024/1024,
        substr($0,index($0,$2))
    }'

# ============================================================
# 31 RECENT FILES
# ============================================================

section "31. RECENTLY MODIFIED FILES"

find "$APP" \
    -type f \
    -mtime -14 \
    -not -path "$APP/vendor/*" \
    -not -path "$APP/node_modules/*" \
    -not -path "$APP/storage/framework/*" \
    -not -path "$APP/storage/logs/*" \
    -printf '%TY-%Tm-%Td %TH:%TM  %p\n' \
    2>/dev/null \
    | sort -r \
    | head -300

# ============================================================
# 32 PHP SYNTAX
# ============================================================

section "32. PHP SYNTAX CHECK"

PHP_ERRORS=0

while IFS= read -r FILE; do

    OUTPUT="$(php -l "$FILE" 2>&1)"

    if echo "$OUTPUT" | grep -q \
        "No syntax errors detected"; then
        :
    else
        echo "$OUTPUT"
        PHP_ERRORS=$((PHP_ERRORS+1))
    fi

done < <(
    find app routes config database \
        -type f \
        -name "*.php" \
        2>/dev/null
)

if [ "$PHP_ERRORS" -eq 0 ]; then
    ok "No PHP syntax errors detected"
else
    fail "$PHP_ERRORS PHP files have syntax errors"
fi

# ============================================================
# 33 BLADE CHECK
# ============================================================

section "33. BLADE VIEW CHECK"

php artisan view:cache 2>&1
BLADE_RC=$?

if [ "$BLADE_RC" -eq 0 ]; then
    ok "Blade compilation successful"
else
    fail "Blade compilation failed"
fi

# Clear only the compiled views generated by the check.
php artisan view:clear 2>&1

# ============================================================
# 34 CONFIG
# ============================================================

section "34. LARAVEL CONFIGURATION"

php artisan config:show app 2>&1

echo
php artisan config:show database 2>&1

echo
php artisan config:show cache 2>&1

echo
php artisan config:show session 2>&1

echo
php artisan config:show queue 2>&1

# ============================================================
# 35 COMPOSER AUDIT
# ============================================================

section "35. COMPOSER SECURITY AUDIT"

if command_exists composer; then
    composer audit 2>&1
else
    info "Composer unavailable"
fi

# ============================================================
# 36 PUBLIC ROOT
# ============================================================

section "36. PUBLIC DIRECTORY / DOCUMENT ROOT"

echo "Nginx roots:"
nginx -T 2>/dev/null \
    | grep -E "^[[:space:]]*root " \
    | head -200

echo
echo "public/index.php:"
ls -lah public/index.php 2>&1

# ============================================================
# 37 SSL / TLS
# ============================================================

section "37. SSL / TLS FULL AUDIT"

CERT_TMP="$REPORT_DIR/certificate.pem"

echo "Domain:"
echo "$HOST"

echo
echo "DNS:"
getent hosts "$HOST" 2>&1

echo
echo "IPv4:"
getent ahostsv4 "$HOST" 2>/dev/null

echo
echo "IPv6:"
getent ahostsv6 "$HOST" 2>/dev/null

# ------------------------------------------------------------
# HTTP
# ------------------------------------------------------------

echo
echo "----- HTTP :80 -----"

curl -I \
    --connect-timeout 10 \
    --max-time 20 \
    "http://$HOST/" 2>&1

echo
echo "HTTP redirect:"
curl -s \
    -o /dev/null \
    -w \
"HTTP_CODE=%{http_code}
REDIRECT=%{redirect_url}
TIME=%{time_total}s
" \
    --connect-timeout 10 \
    --max-time 20 \
    "http://$HOST/"

# ------------------------------------------------------------
# HTTPS
# ------------------------------------------------------------

echo
echo "----- HTTPS :443 -----"

curl -I \
    --connect-timeout 10 \
    --max-time 20 \
    "https://$HOST/" 2>&1

echo
echo "HTTPS status:"
curl -s \
    -o /dev/null \
    -w \
"HTTP_CODE=%{http_code}
TIME=%{time_total}s
REMOTE_IP=%{remote_ip}
SSL_VERIFY=%{ssl_verify_result}
" \
    --connect-timeout 10 \
    --max-time 20 \
    "https://$HOST/"

# ------------------------------------------------------------
# Certificate
# ------------------------------------------------------------

echo
echo "----- CERTIFICATE -----"

echo | openssl s_client \
    -connect "$HOST:443" \
    -servername "$HOST" \
    -showcerts \
    2>/dev/null \
    | openssl x509 \
    -out "$CERT_TMP" 2>/dev/null

if [ -s "$CERT_TMP" ]; then

    ok "SSL certificate retrieved"

    echo
    echo "Subject:"
    openssl x509 -in "$CERT_TMP" -noout -subject

    echo
    echo "Issuer:"
    openssl x509 -in "$CERT_TMP" -noout -issuer

    echo
    echo "Serial:"
    openssl x509 -in "$CERT_TMP" -noout -serial

    echo
    echo "Start date:"
    openssl x509 -in "$CERT_TMP" -noout -startdate

    echo
    echo "End date:"
    openssl x509 -in "$CERT_TMP" -noout -enddate

    END_DATE="$(
        openssl x509 \
        -in "$CERT_TMP" \
        -noout \
        -enddate \
        | cut -d= -f2
    )"

    END_TS="$(date -d "$END_DATE" +%s 2>/dev/null)"
    NOW_TS="$(date +%s)"

    if [ -n "$END_TS" ]; then

        DAYS_LEFT=$(( (END_TS - NOW_TS) / 86400 ))

        echo
        echo "Days remaining: $DAYS_LEFT"

        if [ "$DAYS_LEFT" -lt 0 ]; then
            fail "SSL certificate EXPIRED"
        elif [ "$DAYS_LEFT" -lt 15 ]; then
            fail "SSL expires in less than 15 days"
        elif [ "$DAYS_LEFT" -lt 30 ]; then
            warn "SSL expires in less than 30 days"
        else
            ok "SSL certificate expiration is healthy"
        fi

    fi

    echo
    echo "Subject Alternative Names:"
    openssl x509 \
        -in "$CERT_TMP" \
        -noout \
        -ext subjectAltName

    echo
    echo "Signature algorithm:"
    openssl x509 \
        -in "$CERT_TMP" \
        -noout \
        -text \
        | grep "Signature Algorithm" \
        | head -2

else

    fail "Could not retrieve SSL certificate"

fi

# ------------------------------------------------------------
# Certificate chain
# ------------------------------------------------------------

echo
echo "----- CERTIFICATE CHAIN -----"

openssl s_client \
    -connect "$HOST:443" \
    -servername "$HOST" \
    -showcerts \
    -verify_return_error \
    </dev/null \
    2>&1 | tail -100

# ------------------------------------------------------------
# TLS 1.2
# ------------------------------------------------------------

echo
echo "----- TLS 1.2 -----"

TLS12="$(
    openssl s_client \
    -connect "$HOST:443" \
    -servername "$HOST" \
    -tls1_2 \
    </dev/null 2>&1
)"

echo "$TLS12" | tail -50

if echo "$TLS12" | grep -q "Protocol.*TLSv1.2"; then
    ok "TLS 1.2 supported"
else
    warn "TLS 1.2 could not be confirmed"
fi

# ------------------------------------------------------------
# TLS 1.3
# ------------------------------------------------------------

echo
echo "----- TLS 1.3 -----"

TLS13="$(
    openssl s_client \
    -connect "$HOST:443" \
    -servername "$HOST" \
    -tls1_3 \
    </dev/null 2>&1
)"

echo "$TLS13" | tail -50

if echo "$TLS13" | grep -q "Protocol.*TLSv1.3"; then
    ok "TLS 1.3 supported"
else
    warn "TLS 1.3 not detected"
fi

# ------------------------------------------------------------
# Weak TLS
# ------------------------------------------------------------

echo
echo "----- WEAK TLS CHECK -----"

for PROTOCOL in tls1 tls1_1; do

    OUTPUT="$(
        openssl s_client \
        -connect "$HOST:443" \
        -servername "$HOST" \
        "-$PROTOCOL" \
        </dev/null 2>&1
    )"

    if echo "$OUTPUT" | grep -qE \
        "Protocol.*TLSv1|Cipher is"; then

        warn "$PROTOCOL appears accepted"

    else

        ok "$PROTOCOL rejected"

    fi

done

# ------------------------------------------------------------
# HSTS
# ------------------------------------------------------------

echo
echo "----- HSTS -----"

HSTS="$(
    curl -s -I \
    --connect-timeout 10 \
    --max-time 20 \
    "https://$HOST/" \
    2>/dev/null \
    | grep -i "^strict-transport-security:"
)"

if [ -n "$HSTS" ]; then
    ok "HSTS enabled"
    echo "$HSTS"
else
    warn "HSTS not detected"
fi

# ------------------------------------------------------------
# HTTPS security headers
# ------------------------------------------------------------

echo
echo "----- SECURITY HEADERS -----"

curl -s -I \
    --connect-timeout 10 \
    --max-time 20 \
    "https://$HOST/" \
    2>&1 \
    | grep -Ei \
    "strict-transport|content-security|x-frame|x-content|referrer|permissions-policy|set-cookie"

# ------------------------------------------------------------
# Nginx certificate files
# ------------------------------------------------------------

echo
echo "----- NGINX SSL FILES -----"

CERT_FILES="$(
    nginx -T 2>/dev/null \
    | grep -E "ssl_certificate(_key)? " \
    | sed 's/;//g' \
    | awk '{print $2}' \
    | sort -u
)"

if [ -n "$CERT_FILES" ]; then

    while IFS= read -r FILE; do

        [ -z "$FILE" ] && continue

        echo
        echo "File: $FILE"

        if [ -f "$FILE" ]; then

            ok "File exists"

            ls -lh "$FILE"

            if [[ "$FILE" != *".key" ]]; then
                openssl x509 \
                    -in "$FILE" \
                    -noout \
                    -subject \
                    -issuer \
                    -dates 2>&1
            fi

        else

            fail "File does not exist: $FILE"

        fi

    done <<< "$CERT_FILES"

else

    warn "No ssl_certificate directive detected"

fi

# ------------------------------------------------------------
# Ports
# ------------------------------------------------------------

echo
echo "----- PORT 80 / 443 -----"

ss -lntp 2>/dev/null \
    | grep -E ':(80|443)[[:space:]]' || \
    warn "Could not confirm port 80/443"

# ============================================================
# 38 DNS / NETWORK
# ============================================================

section "38. DNS / NETWORK"

echo "DNS:"
getent hosts "$HOST" 2>&1

echo
echo "Routes:"
ip route 2>&1

echo
echo "Listening TCP ports:"
ss -lntp 2>&1

# ============================================================
# 39 SYSTEM FAILED SERVICES
# ============================================================

section "39. FAILED SYSTEM SERVICES"

systemctl --failed --no-pager 2>&1

# ============================================================
# 40 SYSTEM ERRORS
# ============================================================

section "40. RECENT SYSTEM ERRORS"

journalctl \
    -p err \
    -n 150 \
    --no-pager 2>&1

# ============================================================
# 41 NGINX ERROR LOG
# ============================================================

section "41. NGINX ERROR LOG"

if [ -f /var/log/nginx/error.log ]; then

    echo "Last 300 lines:"
    tail -n 300 /var/log/nginx/error.log

    echo
    echo "Error keywords:"
    grep -Ei \
        "error|crit|alert|emerg|upstream|connect\(\) failed|timeout|permission" \
        /var/log/nginx/error.log \
        | tail -200

else

    info "/var/log/nginx/error.log not found"

fi

# ============================================================
# 42 NGINX ACCESS LOG
# ============================================================

section "42. NGINX ACCESS LOG"

if [ -f /var/log/nginx/access.log ]; then

    tail -n 150 /var/log/nginx/access.log

else

    info "/var/log/nginx/access.log not found"

fi

# ============================================================
# 43 TODO DEBUG SEARCH
# ============================================================

section "43. DEBUG / TODO SEARCH"

grep -RniE \
    "TODO|FIXME|dd\(|dump\(|var_dump\(|print_r\(|ray\(" \
    app routes resources config \
    --exclude-dir=vendor \
    --exclude-dir=node_modules \
    2>/dev/null \
    | head -500

# ============================================================
# 44 STORAGE LINK
# ============================================================

section "44. STORAGE LINK"

if [ -L public/storage ]; then

    TARGET="$(readlink -f public/storage)"

    echo "public/storage -> $TARGET"

    if [ -d "$TARGET" ]; then
        ok "Storage target exists"
    else
        fail "Storage target does not exist"
    fi

else

    warn "public/storage is not a symbolic link"

fi

# ============================================================
# 45 FILE COUNT
# ============================================================

section "45. FILE STATISTICS"

echo "PHP:"
find app routes config database \
    -type f \
    -name "*.php" \
    2>/dev/null | wc -l

echo "Blade:"
find resources/views \
    -type f \
    -name "*.blade.php" \
    2>/dev/null | wc -l

echo "CSS:"
find resources public \
    -type f \
    -name "*.css" \
    2>/dev/null | wc -l

echo "JavaScript:"
find resources public \
    -type f \
    -name "*.js" \
    2>/dev/null | wc -l

echo "Images:"
find public \
    -type f \
    \( -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" -o -name "*.webp" -o -name "*.gif" -o -name "*.svg" \) \
    2>/dev/null | wc -l

# ============================================================
# 46 FINAL SUMMARY
# ============================================================

section "46. FINAL AUDIT SUMMARY"

echo
echo "============================================================"
echo "                    AUDIT RESULT"
echo "============================================================"

echo
echo "OK:       $OK_COUNT"
echo "WARNING:  $WARN_COUNT"
echo "FAIL:     $FAIL_COUNT"

echo
echo "Application:"
echo "$APP"

echo
echo "Website:"
echo "$DOMAIN"

echo
echo "Report directory:"
echo "$REPORT_DIR"

echo
echo "Main report:"
echo "$REPORT"

echo
echo "Routes JSON:"
echo "$ROUTES_JSON"

echo
echo "Certificate:"
echo "$CERT_TMP"

echo
echo "============================================================"
echo "FAIL / WARNING LINES"
echo "============================================================"

grep -E \
    '^\[(FAIL|WARNING)\]' \
    "$REPORT" 2>/dev/null || \
    echo "No FAIL/WARNING lines found."

echo
echo "============================================================"
echo "AUDIT FINISHED"
echo "============================================================"

echo
echo "IMPORTANT:"
echo "This audit did not intentionally modify application files,"
echo "database data, Nginx configuration, SSL configuration,"
echo "environment secrets, packages, or system services."
echo
