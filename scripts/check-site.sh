#!/bin/bash
# ═══════════════════════════════════════════════════
#  چک سلامت chegeniroosta.ir (نسخه ۲ — اصلاح‌شده)
# ═══════════════════════════════════════════════════

BASE="https://chegeniroosta.ir"
SECTION="https://visian.chegeniroosta.ir"
COOKIE=$(mktemp)

GREEN='\033[0;32m'; RED='\033[0;31m'; YELLOW='\033[1;33m'; BLUE='\033[0;34m'; NC='\033[0m'
pass=0; fail=0; warn=0

check() {
    local name="$1"; local url="$2"; local expect="${3:-200}"
    local code=$(curl -sL -o /dev/null -w "%{http_code}" --max-time 15 "$url" 2>/dev/null)
    if [ "$code" = "$expect" ]; then
        echo -e "  ${GREEN}✓${NC} $name → $code"; pass=$((pass+1))
    elif [ "$code" = "301" ] || [ "$code" = "302" ]; then
        echo -e "  ${YELLOW}⚠${NC} $name → $code"; warn=$((warn+1))
    else
        echo -e "  ${RED}✗${NC} $name → $code (انتظار $expect)"; fail=$((fail+1))
    fi
}

check_no_redirect() {
    local name="$1"; local url="$2"; local expect="${3:-200}"
    local code=$(curl -s -o /dev/null -w "%{http_code}" --max-time 15 "$url" 2>/dev/null)
    if [ "$code" = "$expect" ]; then
        echo -e "  ${GREEN}✓${NC} $name → $code"; pass=$((pass+1))
    else
        echo -e "  ${RED}✗${NC} $name → $code (انتظار $expect بدون ریدایرکت)"; fail=$((fail+1))
    fi
}

section() { echo -e "\n${BLUE}═══ $1 ═══${NC}"; }

# ═══ ۱. سایت اصلی ═══
section "۱. سایت اصلی"
check "صفحه اصلی"       "$BASE/"
check "فرم ورود"        "$BASE/login"
check "ورود کارشناس"    "$BASE/fees-login"
check "محاسبه‌گر"        "$BASE/fees"
check "پیگیری قبض"      "$BASE/fees/tracking"

# ═══ ۲. سایت روستاها ═══
section "۲. سایت روستاها"
check "روستای ابوذر"    "$BASE/village/abuzar"
check "روستای سبزوار"   "$BASE/village/sabzevar"
check "روستای چم دیوان" "$BASE/village/cham-divan"

# ═══ ۳. محاسبه‌گر با روستا ═══
section "۳. محاسبه‌گر با روستا"
check "محاسبه ابوذر"    "$BASE/fees?village=abuzar"
check "محاسبه سبزوار"   "$BASE/fees?village=sabzevar"

# ═══ ۴. سایت بخش ویسیان ═══
section "۴. سایت بخشداری ویسیان"
check "صفحه اصلی بخش"   "$SECTION/"

# ═══ ۵. پنل ادمین (بدون لاگین) — باید 302 به login باشد بدون دنبال‌کردن ═══
section "۵. پنل ادمین بدون لاگین (302 انتظار)"
check_no_redirect "داشبورد ادمین"  "$SECTION/admin"                  "302"
check_no_redirect "پنل عوارض"      "$SECTION/admin/fees/dashboard"   "302"
check_no_redirect "پرداخت‌ها"      "$SECTION/admin/fees/payments"    "302"
check_no_redirect "کارشناسان"      "$SECTION/admin/fees/officers"    "302"
check_no_redirect "گزارش‌ها"       "$SECTION/admin/fees/reports"     "302"

# ═══ ۶. لاگ ═══
section "۶. لاگ لاراول"
if [ -f /var/www/chegeniroosta/storage/logs/laravel.log ]; then
    lines=$(wc -l < /var/www/chegeniroosta/storage/logs/laravel.log)
    errors=$(grep -c "production.ERROR" /var/www/chegeniroosta/storage/logs/laravel.log 2>/dev/null | head -1)
    errors=${errors:-0}
    echo "  خطوط: $lines | خطاها: $errors"
    if [ "$errors" -gt 0 ] 2>/dev/null; then
        echo -e "  ${RED}✗ آخرین ۳ خطا:${NC}"
        grep "production.ERROR" /var/www/chegeniroosta/storage/logs/laravel.log | tail -3 | cut -c1-140 | sed 's/^/    /'
        fail=$((fail+1))
    else
        echo -e "  ${GREEN}✓${NC} لاگ خطا خالی است"; pass=$((pass+1))
    fi
else
    echo -e "  ${GREEN}✓${NC} لاگ خالی است"; pass=$((pass+1))
fi

# ═══ ۷. سرور ═══
section "۷. سرور"
echo "  دیسک:  $(df -h / | tail -1 | awk '{print $5" ("$4" آزاد)"}')"
echo "  حافظه: $(free -h | grep Mem | awk '{print $3"/"$2}')"
echo "  لود:   $(cat /proc/loadavg | awk '{print $1}')"
echo "  MySQL: $(systemctl is-active mysql 2>/dev/null)"
echo "  Nginx: $(systemctl is-active nginx 2>/dev/null)"
echo "  PHP:   $(systemctl is-active php8.5-fpm 2>/dev/null)"

# ═══ ۸. کش ═══
section "۸. کش لاراول"
cd /var/www/chegeniroosta
[ -f bootstrap/cache/config.php ] && echo -e "  ${YELLOW}⚠ config cache فعال${NC}" || echo -e "  ${GREEN}✓ config cache غیرفعال${NC}"
[ -f bootstrap/cache/routes-v7.php ] && echo -e "  ${YELLOW}⚠ route cache فعال${NC}" || echo -e "  ${GREEN}✓ route cache غیرفعال${NC}"

# ═══ ۹. دسترسی ═══
section "۹. دسترسی فایل"
[ -w storage ] && echo -e "  ${GREEN}✓${NC} storage" || echo -e "  ${RED}✗${NC} storage"
[ -w bootstrap/cache ] && echo -e "  ${GREEN}✓${NC} bootstrap/cache" || echo -e "  ${RED}✗${NC} bootstrap/cache"
[ -d public/storage ] && echo -e "  ${GREEN}✓${NC} symlink storage" || echo -e "  ${RED}✗${NC} symlink storage"

# ═══ ۱۰. گیت ═══
section "۱۰. گیت"
echo "  آخرین: $(git log -1 --format='%h - %s')"
echo "  تغییریافته: $(git status --porcelain | wc -l)"
echo "  تگ‌ها: $(git tag -l | tr '\n' ' ')"

# ═══ خلاصه ═══
section "📊 خلاصه"
echo -e "  ${GREEN}موفق: $pass${NC}  |  ${YELLOW}هشدار: $warn${NC}  |  ${RED}ناموفق: $fail${NC}"
[ "$fail" -eq 0 ] && echo -e "\n  ${GREEN}🎉 همه چیز سالم است${NC}" || echo -e "\n  ${RED}⚠ خطاها را بررسی کنید${NC}"
