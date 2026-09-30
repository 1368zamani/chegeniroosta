<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>محاسبه‌گر عوارض | روستا نما</title>
<style>
    *{box-sizing:border-box}
    body{font-family:Tahoma,sans-serif;background:#f4f7fb;margin:0;padding:16px;color:#1e3a5f;line-height:1.7}
    .card{max-width:720px;margin:0 auto;background:#fff;border-radius:14px;padding:22px;box-shadow:0 6px 24px rgba(30,58,95,.08)}
    h1{font-size:20px;color:#1565c0;margin:0 0 16px;text-align:center}
    label{display:block;margin:14px 0 6px;font-size:14px;font-weight:bold}
    input,select,textarea{width:100%;padding:11px 13px;border:1px solid #cfd8dc;border-radius:9px;font-family:inherit;font-size:14px;background:#fbfdff}
    input:focus,select:focus,textarea:focus{outline:none;border-color:#1565c0;background:#fff}
    .btn{display:block;width:100%;margin-top:18px;padding:13px;background:#1565c0;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:bold;cursor:pointer}
    .btn:hover{background:#0d47a1}
    .err{background:#ffebee;color:#c62828;border:1px solid #ef9a9a;padding:10px 13px;border-radius:9px;margin:12px 0;font-size:13px}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    @media(max-width:520px){.row{grid-template-columns:1fr}}
    .hint{font-size:12px;color:#607d8b;margin-top:4px}
</style>
</head>
<body>
<div class="card">
    <h1>🧮 محاسبه‌گر عوارض</h1>

    @if($errors->any())
        <div class="err">
            @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('fees.calculate') }}">
        @csrf

        @if(!empty($lockedDehyariId))
            <label>دهیاری</label>
            <div style="padding:11px 13px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:9px;font-weight:bold;color:#2e7d32">
                🏘 {{ $lockedVillage->name ?? $dehyaris->first()->village->name ?? $dehyaris->first()->name }}
            </div>
            <input type="hidden" name="dehyari_id" value="{{ $lockedDehyariId }}">
            <div class="hint" style="color:#2e7d32">
                ℹ️ عوارض به حساب دهیاری {{ $lockedVillage->name ?? '' }} ثبت می‌شود.
            </div>
        @else
            <label>دهیاری</label>
            <select name="dehyari_id" required>
                <option value="">— انتخاب کنید —</option>
                @foreach($dehyaris as $d)
                    <option value="{{ $d->id }}" @selected(old('dehyari_id')==$d->id)>{{ $d->village->name ?? preg_replace('/^دهیاری\s+/u', '', $d->name) }}</option>
                @endforeach
            </select>
        @endif

        <label>نوع عوارض</label>
        <select name="fee_type_id" id="feeTypeSelect" required>
            <option value="">— ابتدا دسته را انتخاب کنید —</option>
            @foreach($categories as $cat)
                <optgroup label="{{ $cat->name }}">
                    @foreach($cat->feeTypes as $t)
                        <option value="{{ $t->id }}"
                                data-params="{{ $t->parameters->toJson() }}"
                                @selected(old('fee_type_id')==$t->id)>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>

        <div id="paramsArea"></div>

        <hr style="margin:22px 0;border:none;border-top:1px dashed #cfd8dc">

        <label>نام و نام خانوادگی مؤدی</label>
        <input type="text" name="taxpayer_name" value="{{ old('taxpayer_name') }}" required>

        <div class="row">
            <div>
                <label>کد ملی</label>
                <input type="text" name="national_code" value="{{ old('national_code') }}" maxlength="10">
            </div>
            <div>
                <label>شماره موبایل</label>
                <input type="text" name="mobile" value="{{ old('mobile') }}" required maxlength="11">
                <div class="hint">برای پیگیری قبض استفاده می‌شود</div>
            </div>
        </div>

        <label>آدرس</label>
        <textarea name="address" rows="2">{{ old('address') }}</textarea>

        <button class="btn" type="submit">محاسبه و صدور قبض</button>
    </form>
</div>

<script>
const sel = document.getElementById('feeTypeSelect');
const area = document.getElementById('paramsArea');

function renderParams() {
    area.innerHTML = '';
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.dataset.params) return;
    let params;
    try { params = JSON.parse(opt.dataset.params); } catch(e){ return; }
    if (!params.length) return;

    const title = document.createElement('div');
    title.style = 'margin-top:18px;font-weight:bold;font-size:14px;color:#1565c0';
    title.textContent = 'اطلاعات مورد نیاز:';
    area.appendChild(title);

    params.forEach(p => {
        const wrap = document.createElement('div');
        const lbl = document.createElement('label');
        lbl.textContent = p.label + (p.unit_label ? ' (' + p.unit_label + ')' : '');
        wrap.appendChild(lbl);

        let el;
        if (p.input_type === 'select' && p.options) {
            el = document.createElement('select');
            (Array.isArray(p.options) ? p.options : Object.values(p.options)).forEach(o => {
                const op = document.createElement('option');
                op.value = (o.value !== undefined ? o.value : o);
                op.textContent = (o.label !== undefined ? o.label : o);
                el.appendChild(op);
            });
        } else if (p.input_type === 'boolean') {
            el = document.createElement('select');
            el.innerHTML = '<option value="1">بله</option><option value="0">خیر</option>';
        } else {
            el = document.createElement('input');
            el.type = p.input_type === 'number' ? 'number' : 'text';
            if (p.default_value) el.value = p.default_value;
            if (p.input_type === 'number') el.step = 'any';
        }
        el.name = 'inputs[' + p.key + ']';
        if (p.is_required) el.required = true;
        wrap.appendChild(el);
        area.appendChild(wrap);
    });
}
sel.addEventListener('change', renderParams);
renderParams();
</script>
</body>
</html>
