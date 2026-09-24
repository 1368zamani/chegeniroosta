<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>درخواست {{ $request->tracking_code }}</title>
<style>
* { box-sizing: border-box; }
body { margin:0; font-family: Tahoma, Arial, sans-serif; background:#f5f7fa; color:#263238; }
.header { background: linear-gradient(135deg, #1e5b9c, #2470b8); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
.header h1 { margin: 0; font-size: 20px; }
.header .nav a { color: white; text-decoration: none; padding: 8px 16px; background: rgba(255,255,255,0.15); border-radius: 8px; font-size: 13px; margin-left: 8px; }
.header .nav a:hover { background: rgba(255,255,255,0.25); }
.container { max-width: 900px; margin: auto; padding: 30px 20px; }
.card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 18px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
.card h3 { color: #1e5b9c; margin: 0 0 18px; padding-bottom: 12px; border-bottom: 2px solid #e3f2fd; font-size: 17px; }
.info-row { display: grid; grid-template-columns: 140px 1fr; gap: 8px 16px; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
.info-row:last-child { border-bottom: none; }
.info-row strong { color: #64748b; font-size: 13px; }
.desc { background: #f8fafc; padding: 14px; border-radius: 8px; line-height: 2; font-size: 14px; white-space: pre-line; margin-top: 10px; }
.field { margin-bottom: 16px; }
.field label { display: block; font-size: 13px; margin-bottom: 6px; color: #546e7a; font-weight: 600; }
.field select, .field textarea { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 14px; }
.field textarea { min-height: 140px; resize: vertical; }
.success { background: #d1fae5; color: #065f46; padding: 12px 18px; border-radius: 10px; margin-bottom: 16px; }
.btn-save { padding: 12px 24px; background: #16a34a; color: white; border: none; border-radius: 8px; cursor: pointer; font-family: inherit; font-weight: 600; }
.btn-cancel { padding: 12px 20px; background: #f1f5f9; color: #1e5b9c; text-decoration: none; border-radius: 8px; display: inline-block; }
@media (max-width: 600px) { .info-row { grid-template-columns: 1fr; gap: 2px; } }
</style>
</head>
<body>

<div class="header">
    <h1>📩 جزئیات درخواست — بخشداری {{ $section->name }}</h1>
    <div class="nav">
        <a href="{{ route('admin.sections.requests.index', $section->id) }}">← بازگشت به لیست</a>
    </div>
</div>

<div class="container">

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <h3>📋 اطلاعات درخواست</h3>

        <div class="info-row">
            <strong>کد پیگیری:</strong>
            <span style="font-family:monospace;direction:ltr;color:#1e5b9c;font-weight:700;font-size:15px;">{{ $request->tracking_code }}</span>
        </div>
        <div class="info-row">
            <strong>نام درخواست‌کننده:</strong>
            <span>{{ $request->full_name }}</span>
        </div>
        <div class="info-row">
            <strong>شماره تماس:</strong>
            <span style="direction:ltr;"><a href="tel:{{ $request->phone }}" style="color:#1e5b9c;">{{ $request->phone }}</a></span>
        </div>
        <div class="info-row">
            <strong>موضوع:</strong>
            <span>{{ $request->subject }}</span>
        </div>
        <div class="info-row">
            <strong>تاریخ ثبت:</strong>
            <span>{{ $request->created_at->format('Y/m/d H:i') }}</span>
        </div>
        <div class="info-row">
            <strong>IP:</strong>
            <span style="font-family:monospace;font-size:12px;color:#94a3b8;">{{ $request->ip_address ?? '—' }}</span>
        </div>

        <div style="margin-top:16px;">
            <strong style="color:#64748b;font-size:13px;">متن درخواست:</strong>
            <div class="desc">{{ $request->description }}</div>
        </div>

        @if($request->attachment)
            <div style="margin-top:16px;">
                <strong style="color:#64748b;font-size:13px;">فایل پیوست:</strong><br><br>
                <a href="{{ asset('storage/' . $request->attachment) }}" target="_blank" style="display:inline-block;padding:8px 16px;background:#f0f9ff;color:#1e5b9c;text-decoration:none;border-radius:8px;border:1px solid #bae6fd;">📎 دانلود فایل</a>
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.sections.requests.update', [$section->id, $request->id]) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <h3>✍️ پاسخ و تغییر وضعیت</h3>

            <div class="field">
                <label>وضعیت درخواست</label>
                <select name="status" required>
                    <option value="submitted"  @selected($request->status=='submitted')>ثبت شده</option>
                    <option value="reviewing"  @selected($request->status=='reviewing')>در حال بررسی</option>
                    <option value="assigned"   @selected($request->status=='assigned')>ارجاع داده شده</option>
                    <option value="processing" @selected($request->status=='processing')>در حال پیگیری</option>
                    <option value="resolved"   @selected($request->status=='resolved')>پاسخ داده شده</option>
                    <option value="rejected"   @selected($request->status=='rejected')>رد شده</option>
                </select>
            </div>

            <div class="field">
                <label>پاسخ (برای کاربر نمایش داده می‌شود)</label>
                <textarea name="admin_response" placeholder="پاسخ خود را بنویسید...">{{ old('admin_response', $request->admin_response) }}</textarea>
            </div>

            <div style="display:flex;gap:10px;margin-top:16px;">
                <button type="submit" class="btn-save">💾 ذخیره پاسخ</button>
                <a href="{{ route('admin.sections.requests.index', $section->id) }}" class="btn-cancel">انصراف</a>
            </div>
        </div>
    </form>

</div>

</body>
</html>
