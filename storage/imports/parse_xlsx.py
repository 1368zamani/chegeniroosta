# -*- coding: utf-8 -*-
import json, re
from openpyxl import load_workbook

TRANS = {
    'آ':'a','ا':'a','ب':'b','پ':'p','ت':'t','ث':'s','ج':'j','چ':'ch',
    'ح':'h','خ':'kh','د':'d','ذ':'z','ر':'r','ز':'z','ژ':'zh','س':'s',
    'ش':'sh','ص':'s','ض':'z','ط':'t','ظ':'z','ع':'a','غ':'gh','ف':'f',
    'ق':'gh','ک':'k','ك':'k','گ':'g','ل':'l','م':'m','ن':'n','و':'v',
    'ه':'h','ی':'y','ي':'y','ء':'','ئ':'y','ة':'h',
    '۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9',
    ' ':'-','‌':'-','_':'-','/':'-','.':'-','،':'-',',':'-','(':'-',')':'-',
}

def make_slug(name):
    name = re.sub(r'\s+',' ', str(name).strip())
    out = []
    for ch in name:
        if ch in TRANS: out.append(TRANS[ch])
        elif ch.isascii() and ch.isalnum(): out.append(ch.lower())
        else: out.append('-')
    slug = re.sub(r'-+','-',''.join(out)).strip('-')
    return slug or 'village'

SECTION_MAP = {
    'چگني': ('markazi','بخش مرکزی'),
    'چگنی': ('markazi','بخش مرکزی'),
    'شاهيوند': ('shahivand','بخش شاهیوند'),
    'شاهیوند': ('shahivand','بخش شاهیوند'),
    'ويسيان': ('visian','بخش ویسیان'),
    'ویسیان': ('visian','بخش ویسیان'),
}

wb = load_workbook('/var/www/chegeniroosta/storage/imports/population.xlsx', data_only=True)
ws = wb['CN95_HouseholdPopulationVillage']

villages = []
skipped_shahrestan = 0
skipped_record = 0
skipped_number = 0
skipped_population = 0
skipped_section = 0

for row in ws.iter_rows(min_row=3, values_only=True):
    if not row or not row[0]: continue

    shahrestan_name = str(row[3] or '').strip()
    bakhsh_name = str(row[5] or '').strip()
    dehestan_name = str(row[7] or '').strip()
    v_code = row[9]
    v_name = str(row[10] or '').strip()
    record_code = str(row[11] or '').strip()
    households = row[14]
    population = row[15]

    # فیلتر شهرستان
    if shahrestan_name != 'دوره':
        skipped_shahrestan += 1
        continue

    # فیلتر کد رکورد (6=آبادی، 8=آبادی)
    if record_code not in ('6', '8'):
        skipped_record += 1
        continue

    # فیلتر عددی بودن
    try:
        h = int(str(households).strip())
        p = int(str(population).strip())
    except (ValueError, TypeError):
        skipped_number += 1
        continue

    # فیلتر جمعیت و خانوار
    if h < 30 or p < 100:
        skipped_population += 1
        continue

    # فیلتر بخش
    if bakhsh_name not in SECTION_MAP:
        skipped_section += 1
        continue
    section_slug, section_name = SECTION_MAP[bakhsh_name]

    villages.append({
        'name': v_name,
        'slug': make_slug(v_name),
        'section_slug': section_slug,
        'section_name': section_name,
        'dehestan': dehestan_name,
        'county': 'چگنی',
        'province': 'لرستان',
        'population': p,
        'households': h,
        'village_code': str(v_code or '').strip(),
    })

# حذف slug های تکراری
seen = {}
for v in villages:
    base = v['slug']
    if base in seen:
        seen[base] += 1
        v['slug'] = f"{base}-{seen[base]}"
    else:
        seen[base] = 1

out = {'villages': villages, 'total': len(villages)}
with open('/var/www/chegeniroosta/storage/imports/villages.json','w',encoding='utf-8') as f:
    json.dump(out, f, ensure_ascii=False, indent=2)

print(f"✅ روستاهای نهایی: {len(villages)}")
print()
print("📊 آمار فیلتر:")
print(f"  - رد شده به خاطر شهرستان (نه دوره): {skipped_shahrestan}")
print(f"  - رد شده به خاطر کد رکورد: {skipped_record}")
print(f"  - رد شده به خاطر عدد نبودن: {skipped_number}")
print(f"  - رد شده به خاطر خانوار<30 یا جمعیت<100: {skipped_population}")
print(f"  - رد شده به خاطر بخش نامشخص: {skipped_section}")
print()
print("📌 خلاصه هر بخش:")
by_section = {}
for v in villages:
    by_section.setdefault(v['section_slug'], []).append(v)
for slug, vs in sorted(by_section.items()):
    print(f"  بخش {slug}: {len(vs)} روستا")

print()
print("📋 ۱۰ روستای اول:")
for v in villages[:10]:
    print(f"  - {v['name']} → {v['slug']} | {v['dehestan']} | {v['households']} خانوار | {v['population']} نفر")
