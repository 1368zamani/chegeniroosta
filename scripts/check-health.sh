#!/bin/bash
# ═══════════════════════════════════════════════════
#  بررسی وضعیت کلی سایت chegeniroosta.ir
# ═══════════════════════════════════════════════════

GREEN='\033[0;32m'; RED='\033[0;31m'; YELLOW='\033[1;33m'; BLUE='\033[0;34m'; CYAN='\033[0;36m'; NC='\033[0m'
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

section() { echo -e "\n${CYAN}═══ $1 ═══${NC}"; }

clear
echo -e "${BLUE}╔══════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║   وضعیت سایت chegeniroosta.ir                    ║${NC}"
echo -e "${BLUE}║   تاریخ: $(date '+%Y-%m-%d %H:%M')                       ║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════════════╝${NC}"

BASE="https://chegeniroosta.ir"
SEC="https://visian.chegeniroosta.ir"

# ═══ ۱. سایت اصلی ═══
section "۱. سایت اصلی"
check "صفحه اصلی"      "$BASE/"
check "فرم ورود"       "$BASE/login"
check "ورود کارشناس"   "$BASE/fees-login"

# ═══ ۲. سایت‌های بخشداری ═══
section "۲. سایت‌های بخشداری"
check "ویسیان"        "$SEC/"
check "مرکزی"         "https://markazi.chegeniroosta.ir/"
check "شاهیوند"       "https://shahivand.chegeniroosta.ir/"

# ═══ ۳. سایت روستاها ═══
section "۳. سایت روستاها (نمونه)"
check "ابوذر"          "$BASE/village/abuzar"
check "سبزوار"         "$BASE/village/sabzevar"
check "چم دیوان"       "$BASE/village/cham-divan"

# ═══ ۴. ماژول عوارض قدیم ═══
section "۴. ماژول عوارض قدیم"
check "محاسبه‌گر"       "$BASE/fees"
check "محاسبه ابوذر"   "$BASE/fees?village=abuzar"
check "پیگیری قبض"     "$BASE/fees/tracking"

# ═══ ۵. ماژول تعرفه جدید ═══
section "۵. ماژول تعرفه جدید"
check "تعرفه ابوذر"    "$BASE/village/abuzar/tariff"
check "هدایت به تعرفه" "$BASE/fees/view-tariff/1"

# ═══ ۶. پنل ادمین (302 بدون لاگین) ═══
section "۶. پنل ادمین (نیاز به لاگین)"
for path in \
    "admin" \
    "admin/fees/dashboard" \
    "admin/fees/payments" \
    "admin/fees/officers" \
    "admin/fees/reports" \
    "admin/tariffs" \
    "admin/tariffs/1" \
    "admin/passwords" \
    "admin/village/1/settings" \
    "admin/sections/1/dashboard"
do
    code=$(curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$SEC/$path" 2>/dev/null)
    if [ "$code" = "302" ]; then
        echo -e "  ${GREEN}✓${NC} /$path → 302"; pass=$((pass+1))
    else
        echo -e "  ${RED}✗${NC} /$path → $code"; fail=$((fail+1))
    fi
done

# ═══ ۷. لاگ خطا ═══
section "۷. لاگ لاراول"
cd /var/www/chegeniroosta
if [ -f storage/logs/laravel.log ]; then
    lines=$(wc -l < storage/logs/laravel.log)
    errors=$(grep -c "production.ERROR" storage/logs/laravel.log 2>/dev/null | head -1)
    errors=${errors:-0}
    echo "  خطوط: $lines | خطاها: $errors"
    if [ "$errors" -gt 0 ] 2>/dev/null; then
        echo -e "  ${RED}✗ $errors خطا${NC}"; fail=$((fail+1))
        grep "production.ERROR" storage/logs/laravel.log | tail -2 | cut -c1-150 | sed 's/^/    /'
    else
        echo -e "  ${GREEN}✓ لاگ خالی${NC}"; pass=$((pass+1))
    fi
else
    echo -e "  ${GREEN}✓ لاگ ندارد${NC}"; pass=$((pass+1))
fi

# ═══ ۸. دیتابیس ═══
section "۸. دیتابیس"
DB_RESULT=$(php artisan tinker --execute='
try {
    echo "OK|users=" . \App\Models\User::count()
        . "|villages=" . \App\Models\Village::count()
        . "|sections=" . \App\Models\Section::count()
        . "|dehyaris=" . \App\Models\Dehyari::count()
        . "|tariff_sets=" . \App\Models\TariffSet::count()
        . "|tariff_titles=" . \App\Models\TariffTitle::count()
        . "|bills=" . \App\Models\Bill::count()
        . "|payments=" . \App\Models\Payment::count();
} catch (\Throwable $e) { echo "ERROR|" . $e->getMessage(); }' 2>&1 | tail -1)

if echo "$DB_RESULT" | grep -q "^OK"; then
    echo -e "  ${GREEN}✓ دیتابیس سالم${NC}"
    echo "    $DB_RESULT" | sed 's/|/\n    /g'
    pass=$((pass+1))
else
    echo -e "  ${RED}✗ دیتابیس مشکل دارد${NC}"
    echo "    $DB_RESULT"
    fail=$((fail+1))
fi

# ═══ ۹. ماژول تعرفه ═══
section "۹. ماژول تعرفه"
TARIFF_RESULT=$(php artisan tinker --execute='
try {
    $active = \App\Models\TariffSet::where("status","published")->count();
    $draft  = \App\Models\TariffSet::where("status","draft")->count();
    echo "OK|منتشرشده=" . $active . "|پیش‌نویس=" . $draft;
} catch (\Throwable $e) { echo "ERROR|" . $e->getMessage(); }' 2>&1 | tail -1)
echo "    $TARIFF_RESULT" | sed 's/|/\n    /g'

echo -n "    Calculator های ثبت‌شده: "
php artisan tinker --execute='
use App\Services\Tariff\Calculators\CalculatorRegistry;
echo count((new CalculatorRegistry())->all());' 2>/dev/null | tail -1

# ═══ ۱۰. سرور ═══
section "۱۰. سرور"
echo "  دیسک:     $(df -h / | tail -1 | awk '{print $5" ("$4" آزاد)"}')"
echo "  حافظه:    $(free -h | grep Mem | awk '{print $3"/"$2}')"
echo "  لود:      $(cat /proc/loadavg | awk '{print $1}')"
echo "  کرنل:     $(uname -r)"
echo "  Nginx:    $(systemctl is-active nginx 2>/dev/null)"
echo "  PHP-FPM:  $(systemctl is-active php8.5-fpm 2>/dev/null)"
echo "  MySQL:    $(systemctl is-active mysql 2>/dev/null)"

# ═══ ۱۱. کش لاراول ═══
section "۱۱. کش لاراول"
[ -f bootstrap/cache/config.php ] && echo -e "  ${YELLOW}⚠ config cache فعال${NC}" || echo -e "  ${GREEN}✓ config cache غیرفعال${NC}"
[ -f bootstrap/cache/routes-v7.php ] && echo -e "  ${YELLOW}⚠ route cache فعال${NC}" || echo -e "  ${GREEN}✓ route cache غیرفعال${NC}"

# ═══ ۱۲. فایل‌های مهم ═══
section "۱۲. فایل‌های مهم"
[ -d storage/app/public/dehyar-photos ] && echo -e "  ${GREEN}✓ پوشه عکس دهیار${NC}" || echo -e "  ${YELLOW}⚠ پوشه عکس دهیار نیست${NC}"
[ -d storage/app/public/tariff-approvals ] && echo -e "  ${GREEN}✓ پوشه مصوبه تعرفه${NC}" || echo -e "  ${YELLOW}⚠ پوشه مصوبه تعرفه نیست${NC}"
[ -L public/storage ] && echo -e "  ${GREEN}✓ symlink storage${NC}" || echo -e "  ${RED}✗ symlink storage نیست${NC}"
[ -f public/css/admin-master.css ] && echo -e "  ${GREEN}✓ CSS ادمین${NC}" || echo -e "  ${YELLOW}⚠ CSS ادمین نیست${NC}"

# ═══ ۱۳. گیت ═══
section "۱۳. گیت"
echo "  آخرین: $(cd /var/www/chegeniroosta && git log -1 --format='%h - %s' 2>/dev/null)"
echo "  تغییریافته: $(cd /var/www/chegeniroosta && git status --porcelain 2>/dev/null | wc -l) فایل"
echo "  تگ‌های اخیر:"
cd /var/www/chegeniroosta && git tag -l | tail -5 | sed 's/^/    /'

# ═══ ۱۴. بکاپ ═══
section "۱۴. آخرین بکاپ"
ls -lht /root/backups/*.tar.gz 2>/dev/null | head -3 | awk '{print "  " $9 " (" $5 ")"}' || echo "  بکاپی نیست"

# ═══ خلاصه ═══
section "📊 خلاصه"
echo -e "  ${GREEN}موفق: $pass${NC}  |  ${YELLOW}هشدار: $warn${NC}  |  ${RED}ناموفق: $fail${NC}"
if [ "$fail" -eq 0 ]; then
    echo -e "\n  ${GREEN}🎉 همه چیز سالم است${NC}"
else
    echo -e "\n  ${RED}⚠ $fail مورد ناموفق — بررسی کنید${NC}"
fi
