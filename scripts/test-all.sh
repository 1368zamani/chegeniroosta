#!/bin/bash
# ═══════════════════════════════════════════════════
#  تست جامع chegeniroosta.ir
# ═══════════════════════════════════════════════════

GREEN='\033[0;32m'; RED='\033[0;31m'; YELLOW='\033[1;33m'; BLUE='\033[0;34m'; CYAN='\033[0;36m'; NC='\033[0m'
pass=0; fail=0; warn=0

check() {
    local name="$1"; local url="$2"; local expect="${3:-200}"
    local code=$(curl -sL -o /dev/null -w "%{http_code}" --max-time 15 "$url" 2>/dev/null)
    if [ "$code" = "$expect" ]; then
        echo -e "  ${GREEN}✓${NC} $name → $code"
        pass=$((pass+1))
    elif [ "$code" = "301" ] || [ "$code" = "302" ]; then
        echo -e "  ${YELLOW}⚠${NC} $name → $code"
        warn=$((warn+1))
    else
        echo -e "  ${RED}✗${NC} $name → $code (انتظار $expect)"
        fail=$((fail+1))
    fi
}

check_no_redirect() {
    local name="$1"; local url="$2"; local expect="${3:-200}"
    local code=$(curl -s -o /dev/null -w "%{http_code}" --max-time 15 "$url" 2>/dev/null)
    if [ "$code" = "$expect" ]; then
        echo -e "  ${GREEN}✓${NC} $name → $code"
        pass=$((pass+1))
    else
        echo -e "  ${RED}✗${NC} $name → $code (انتظار $expect)"
        fail=$((fail+1))
    fi
}

check_content() {
    local name="$1"; local url="$2"; local needle="$3"
    local html=$(curl -sL --max-time 15 "$url" 2>/dev/null)
    if echo "$html" | grep -q "$needle"; then
        echo -e "  ${GREEN}✓${NC} $name (حاوی «$needle»)"
        pass=$((pass+1))
    else
        echo -e "  ${RED}✗${NC} $name — «$needle» پیدا نشد"
        fail=$((fail+1))
    fi
}

section() { echo -e "\n${CYAN}═══ $1 ═══${NC}"; }

clear
echo -e "${BLUE}╔══════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║   تست جامع سایت chegeniroosta.ir                ║${NC}"
echo -e "${BLUE}║   تاریخ: $(date '+%Y-%m-%d %H:%M')                     ║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════════════╝${NC}"

BASE="https://chegeniroosta.ir"
SEC="https://visian.chegeniroosta.ir"

# ═══ ۱. سایت اصلی ═══
section "۱. سایت اصلی"
check "صفحه اصلی"          "$BASE/"
check "فرم ورود"           "$BASE/login"
check "ورود کارشناس"       "$BASE/fees-login"

# ═══ ۲. سایت روستاها (نمونه از هر بخش) ═══
section "۲. سایت روستاها"
for v in abuzar sabzevar cham-divan zoorandool mineh tal-moradkhani cham-vazir; do
    check "روستا: $v"      "$BASE/village/$v"
done

# ═══ ۳. محاسبه‌گر عوارض ═══
section "۳. محاسبه‌گر عوارض"
check "صفحه محاسبه"        "$BASE/fees"
check "محاسبه با روستا"    "$BASE/fees?village=abuzar"
check "محاسبه با روستا ۲"  "$BASE/fees?village=sabzevar"
check "پیگیری قبض"         "$BASE/fees/tracking"
check_content "عنوان محاسبه‌گر" "$BASE/fees" "محاسبه‌گر عوارض"

# ═══ ۴. سایت بخش ویسیان ═══
section "۴. سایت بخشداری ویسیان"
check "صفحه اصلی بخش"      "$SEC/"
check "اخبار بخش"          "$SEC/news"
check_content "هدر بخش"    "$SEC/" "پایگاه اطلاع"
check_content "پس‌زمینه هدر" "$SEC/" "F5F9FF"

# ═══ ۵. پنل ادمین — بدون لاگین باید ۳۰۲ بدهد ═══
section "۵. پنل ادمین (بدون لاگین → ۳۰۲)"
check_no_redirect "داشبورد ادمین"   "$SEC/admin"                  "302"
check_no_redirect "پنل عوارض"       "$SEC/admin/fees/dashboard"   "302"
check_no_redirect "پرداخت‌ها"       "$SEC/admin/fees/payments"    "302"
check_no_redirect "کارشناسان"       "$SEC/admin/fees/officers"    "302"
check_no_redirect "گزارش‌ها"        "$SEC/admin/fees/reports"     "302"
check_no_redirect "تعرفه‌ها"        "$SEC/admin/fees/tariffs"     "302"
check_no_redirect "حساب‌ها"         "$SEC/admin/fees/accounts"    "302"

# ═══ ۶. فایل‌های استاتیک ═══
section "۶. فایل‌های استاتیک"
check "CSS فونت‌های فارسی"   "$SEC/css/persian-fonts.css"
check "فونت Lalezar"        "$SEC/fonts/Lalezar.ttf"
check "فونت Noto Nastaliq"  "$SEC/fonts/NotoNastaliq.ttf"
check "فایل CSS اصلی"       "$BASE/css/persian-fonts.css"

# ═══ ۷. سلامت دیتابیس ═══
section "۷. سلامت دیتابیس"
cd /var/www/chegeniroosta
DB_RESULT=$(php artisan tinker --execute='
try {
    $users = \App\Models\User::count();
    $villages = \App\Models\Village::count();
    $sections = \App\Models\Section::count();
    $dehyaris = \App\Models\Dehyari::count();
    $tariffs = \App\Models\FeeTariff::count();
    $bills = \App\Models\Bill::count();
    $payments = \App\Models\Payment::count();
    echo "OK: users=$users, villages=$villages, sections=$sections, dehyaris=$dehyaris, tariffs=$tariffs, bills=$bills, payments=$payments";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage();
}' 2>&1 | tail -1)

if echo "$DB_RESULT" | grep -q "OK:"; then
    echo -e "  ${GREEN}✓${NC} دیتابیس سالم"
    echo "    $DB_RESULT"
    pass=$((pass+1))
else
    echo -e "  ${RED}✗${NC} دیتابیس مشکل دارد"
    echo "    $DB_RESULT"
    fail=$((fail+1))
fi

# ═══ ۸. لاگ خطا ═══
section "۸. لاگ خطا"
if [ -f storage/logs/laravel.log ]; then
    lines=$(wc -l < storage/logs/laravel.log)
    errors=$(grep -c "production.ERROR" storage/logs/laravel.log 2>/dev/null | head -1)
    errors=${errors:-0}
    if [ "$errors" -gt 0 ] 2>/dev/null; then
        echo -e "  ${RED}✗${NC} $errors خطا در لاگ"
        grep "production.ERROR" storage/logs/laravel.log | tail -2 | cut -c1-140 | sed 's/^/    /'
        fail=$((fail+1))
    else
        echo -e "  ${GREEN}✓${NC} لاگ خطا خالی ($lines خط)"
        pass=$((pass+1))
    fi
else
    echo -e "  ${GREEN}✓${NC} فایل لاگ وجود ندارد"
    pass=$((pass+1))
fi

# ═══ ۹. دسترسی فایل‌ها ═══
section "۹. دسترسی فایل‌ها"
[ -w storage ] && { echo -e "  ${GREEN}✓${NC} storage"; pass=$((pass+1)); } || { echo -e "  ${RED}✗${NC} storage"; fail=$((fail+1)); }
[ -w bootstrap/cache ] && { echo -e "  ${GREEN}✓${NC} bootstrap/cache"; pass=$((pass+1)); } || { echo -e "  ${RED}✗${NC} bootstrap/cache"; fail=$((fail+1)); }
[ -d public/storage ] && { echo -e "  ${GREEN}✓${NC} symlink storage"; pass=$((pass+1)); } || { echo -e "  ${RED}✗${NC} symlink storage"; fail=$((fail+1)); }
[ -f public/fonts/Lalezar.ttf ] && { echo -e "  ${GREEN}✓${NC} فونت Lalezar"; pass=$((pass+1)); } || { echo -e "  ${RED}✗${NC} فونت Lalezar"; fail=$((fail+1)); }

# ═══ ۱۰. سرور ═══
section "۱۰. سرور"
echo "  دیسک:     $(df -h / | tail -1 | awk '{print $5" ("$4" آزاد)"}')"
echo "  حافظه:    $(free -h | grep Mem | awk '{print $3"/"$2}')"
echo "  لود:      $(cat /proc/loadavg | awk '{print $1}')"
echo "  MySQL:    $(systemctl is-active mysql 2>/dev/null)"
echo "  Nginx:    $(systemctl is-active nginx 2>/dev/null)"
echo "  PHP-FPM:  $(systemctl is-active php8.5-fpm 2>/dev/null)"

# ═══ ۱۱. کش لاراول ═══
section "۱۱. کش لاراول"
[ -f bootstrap/cache/config.php ] && echo -e "  ${YELLOW}⚠${NC} config cache فعال" || echo -e "  ${GREEN}✓${NC} config cache غیرفعال"
[ -f bootstrap/cache/routes-v7.php ] && echo -e "  ${YELLOW}⚠${NC} route cache فعال" || echo -e "  ${GREEN}✓${NC} route cache غیرفعال"

# ═══ ۱۲. گیت ═══
section "۱۲. گیت"
echo "  آخرین کامیت: $(git log -1 --format='%h - %s' 2>/dev/null)"
echo "  تغییریافته: $(git status --porcelain | wc -l) فایل"
echo "  تگ‌های اخیر:"
git tag -l | tail -5 | sed 's/^/    /'

# ═══ ۱۳. فونت و رنگ‌بندی هدر ═══
section "۱۳. بررسی هدر جدید"
HEADER_HTML=$(curl -sL "$SEC/" 2>/dev/null)
echo "$HEADER_HTML" | grep -q "F5F9FF" && { echo -e "  ${GREEN}✓${NC} پس‌زمینه هدر (#F5F9FF)"; pass=$((pass+1)); } || { echo -e "  ${RED}✗${NC} پس‌زمینه هدر"; fail=$((fail+1)); }
echo "$HEADER_HTML" | grep -q "0D47A1" && { echo -e "  ${GREEN}✓${NC} رنگ عنوان (#0D47A1)"; pass=$((pass+1)); } || { echo -e "  ${YELLOW}⚠${NC} رنگ عنوان"; warn=$((warn+1)); }
echo "$HEADER_HTML" | grep -q "D32F2F" && { echo -e "  ${GREEN}✓${NC} رنگ ویسیان (#D32F2F)"; pass=$((pass+1)); } || { echo -e "  ${YELLOW}⚠${NC} رنگ ویسیان"; warn=$((warn+1)); }
echo "$HEADER_HTML" | grep -q "Lalezar" && { echo -e "  ${GREEN}✓${NC} فونت Lalezar"; pass=$((pass+1)); } || { echo -e "  ${YELLOW}⚠${NC} فونت Lalezar"; warn=$((warn+1)); }

# ═══ خلاصه ═══
section "📊 خلاصه"
echo -e "  ${GREEN}موفق: $pass${NC}  |  ${YELLOW}هشدار: $warn${NC}  |  ${RED}ناموفق: $fail${NC}"

if [ "$fail" -eq 0 ]; then
    echo -e "\n  ${GREEN}🎉 همه چیز سالم است!${NC}"
    exit 0
else
    echo -e "\n  ${RED}⚠ $fail مورد ناموفق — بررسی کنید${NC}"
    exit 1
fi
