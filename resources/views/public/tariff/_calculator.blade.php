@if(!empty($schemas))
<section class="mb-8 no-print" id="calculator-section">
    <h2 class="text-xl font-bold mb-3 text-emerald-800">🧮 ماشین‌حساب تعرفه</h2>
    <div class="bg-white rounded-lg shadow p-4 md:p-6">

        <div class="mb-4">
            <label class="block mb-1 font-medium">انتخاب عنوان</label>
            <select id="calcItem" class="w-full border rounded p-2">
                <option value="">— یک عنوان انتخاب کنید —</option>
                @foreach($schemas as $itemId => $def)
                    <option value="{{ $itemId }}">{{ $def['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div id="calcFields" class="space-y-3 mb-4"></div>

        <button id="calcSubmit" type="button"
                class="hidden bg-emerald-600 text-white px-6 py-2 rounded hover:bg-emerald-700">
            محاسبه
        </button>

        <div id="calcResult" class="hidden mt-4 border-t pt-4"></div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="application/json" id="calcSchemas">@json($schemas)</script>
    <script>
    (function () {
        const schemas = JSON.parse(document.getElementById('calcSchemas').textContent);
        const select  = document.getElementById('calcItem');
        const fields  = document.getElementById('calcFields');
        const submit  = document.getElementById('calcSubmit');
        const result  = document.getElementById('calcResult');
        const csrf    = document.querySelector('meta[name="csrf-token"]').content;
        const slug    = @json($slug);

        function faNum(v) {
            return String(v).replace(/\d/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]);
        }
        function money(v) { return faNum(Number(v).toLocaleString('en-US')); }

        function renderForm(itemId) {
            fields.innerHTML = '';
            result.classList.add('hidden');
            if (!itemId || !schemas[itemId]) {
                submit.classList.add('hidden');
                return;
            }
            const schema = schemas[itemId].schema;
            for (const [key, f] of Object.entries(schema)) {
                const wrap = document.createElement('div');
                const label = document.createElement('label');
                label.className = 'block mb-1 text-sm font-medium';
                label.textContent = f.label || key;
                wrap.appendChild(label);

                let el;
                if (f.type === 'select') {
                    el = document.createElement('select');
                    for (const [val, text] of Object.entries(f.options || {})) {
                        const opt = document.createElement('option');
                        opt.value = val; opt.textContent = text;
                        if (val === (f.default ?? '')) opt.selected = true;
                        el.appendChild(opt);
                    }
                } else if (f.type === 'checkbox') {
                    el = document.createElement('input');
                    el.type = 'checkbox';
                    el.checked = !!f.default;
                    el.className = 'h-5 w-5';
                } else {
                    el = document.createElement('input');
                    el.type = 'number';
                    el.step = 'any';
                    if (f.min !== undefined) el.min = f.min;
                    if (f.max !== undefined) el.max = f.max;
                    if (f.default !== undefined) el.value = f.default;
                    if (f.required) el.required = true;
                }
                el.dataset.key = key;
                el.className = (el.className || '') + ' w-full border rounded p-2';
                wrap.appendChild(el);
                fields.appendChild(wrap);
            }
            submit.classList.remove('hidden');
        }

        select.addEventListener('change', () => renderForm(select.value));

        submit.addEventListener('click', async () => {
            const itemId = select.value;
            if (!itemId) return;
            const inputs = {};
            fields.querySelectorAll('[data-key]').forEach(el => {
                inputs[el.dataset.key] = el.type === 'checkbox' ? el.checked : el.value;
            });

            submit.disabled = true;
            submit.textContent = 'در حال محاسبه...';
            try {
                const res = await fetch(`/village/${slug}/tariff/calculate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ item_id: parseInt(itemId), inputs }),
                });
                const data = await res.json();
                result.innerHTML = '';
                result.classList.remove('hidden');

                if (!data.ok) {
                    result.innerHTML = `<div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-3 rounded">${data.error || 'خطا'}</div>`;
                } else {
                    let html = `<div class="bg-emerald-50 border-r-4 border-emerald-500 p-4 rounded mb-3">
                        <div class="text-sm text-emerald-800">مبلغ محاسبه‌شده:</div>
                        <div class="text-2xl font-bold font-mono text-emerald-900 mt-1">${money(data.amount)} ریال</div>
                        <div class="text-sm text-gray-600 mt-1">معادل ${money(data.amount / 10)} تومان</div>
                    </div>`;
                    if (data.breakdown && Object.keys(data.breakdown).length) {
                        html += '<table class="w-full text-right text-sm border">';
                        html += '<thead class="bg-gray-100"><tr><th class="p-2">جزء</th><th class="p-2">مقدار</th></tr></thead><tbody>';
                        for (const [k, v] of Object.entries(data.breakdown)) {
                            const val = typeof v === 'number' ? money(v) : faNum(v);
                            html += `<tr class="border-t"><td class="p-2">${k}</td><td class="p-2 font-mono">${val}</td></tr>`;
                        }
                        html += '</tbody></table>';
                    }
                    if (data.warnings && data.warnings.length) {
                        html += '<div class="mt-3 bg-amber-50 border-r-4 border-amber-500 text-amber-900 p-2 rounded text-xs">';
                        data.warnings.forEach(w => html += `<div>⚠️ ${w}</div>`);
                        html += '</div>';
                    }
                    result.innerHTML = html;
                }
            } catch (err) {
                result.innerHTML = `<div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-3 rounded">${err.message}</div>`;
                result.classList.remove('hidden');
            } finally {
                submit.disabled = false;
                submit.textContent = 'محاسبه';
            }
        });
    })();
    </script>
</section>
@endif
