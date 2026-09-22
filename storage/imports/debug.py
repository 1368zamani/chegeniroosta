# -*- coding: utf-8 -*-
from openpyxl import load_workbook

wb = load_workbook('/var/www/chegeniroosta/storage/imports/population.xlsx', data_only=True)
print("Sheet names:", wb.sheetnames)
ws = wb[wb.sheetnames[0]]
print(f"Total rows: {ws.max_row}, columns: {ws.max_column}")
print()

# ۱۰ ردیف اول رو نشون بده
print("=== ۱۰ ردیف اول ===")
for i, row in enumerate(ws.iter_rows(min_row=1, max_row=10, values_only=True), start=1):
    print(f"Row {i}: {row}")
    print()

# بررسی که چرا فیلتر می‌کنه - چند ردیف وسط فایل
print("=== بررسی مقادیر یکتا در ستون‌ها ===")
from collections import Counter
shahrestan_vals = Counter()
record_codes = Counter()
for i, row in enumerate(ws.iter_rows(min_row=3, values_only=True), start=3):
    if not row or not row[0]: 
        continue
    shahrestan_vals[row[3]] += 1
    record_codes[row[11]] += 1

print("شهرستان‌ها (ستون D - index 3):")
for k, v in shahrestan_vals.most_common(20):
    print(f"  {repr(k)}: {v}")

print()
print("کد رکورد (ستون L - index 11):")
for k, v in record_codes.most_common(20):
    print(f"  {repr(k)}: {v}")
