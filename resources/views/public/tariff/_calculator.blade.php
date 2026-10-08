@php
    $user = auth()->user();
    $isSuper = $user->role === 'super_admin';
    $isTech = $user->officer_type === 'technical';
    $isFin  = $user->officer_type === 'financial';
@endphp

<div x-data="tariffEditor({
        schemas: @js($schemas),
        slug: @js($village->slug),
        csrf: @js(csrf_token()),
        userRole: @js($user->role),
        officerType: @js($user->officer_type ?? null),
     })"
     x-cloak>

    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold text-emerald-800">🧮 ویرایش تعرفه</h2>
            <p class="text-xs text-gray-600 mt-1">
                شما می‌توانید فقط مقادیر ورودی را تغییر دهید — فرمول‌ها ثابت هستند.
            </p>
        </div>
        @if($user->role === 'fee_officer')
        <span class="text-xs {{ $isTech ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }} px-3 py-1 rounded-full">
            {{ $isTech ? '🏗️ کارشناس فنی عمرانی' : '💰 کارشناس مالی' }}
        </span>
        @endif
    </div>

    {{-- انتخاب آیتم --}}
    <div class="mb-4">
        <label class="block mb-2 font-medium">انتخاب تعرفه</label>
        <select x-model="selectedItem" @change="renderForm()"
                class="w-full border rounded-lg p-3">
            <option value="">— انتخاب کنید —</option>
            <template x-for="(def, id) in filteredSchemas" :key="id">
                <option :value="id" x-text="def.label"></option>
            </template>
        </select>
    </div>

    {{-- فرم داینامیک --}}
    <template x-if="currentSchema && Object.keys(currentSchema).length > 0">
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <h3 class="font-bold mb-3 text-gray-700">📝 مقادیر ورودی</h3>

            <div class="space-y-4">
                <template x-for="(field, key) in currentSchema" :key="key">
                    <div>
                        <label class="block mb-1 text-sm font-medium">
                            <span x-text="field.label || key"></span>
                            <span x-show="field.required" class="text-red-500">*</span>
                            <span x-show="field.unit" class="text-xs text-gray-500" x-text="`(${field.unit})`"></span>
                        </label>

                        {{-- Select --}}
                        <template x-if="field.type === 'select'">
                            <select x-model="inputs[key]" class="w-full border rounded-lg p-2">
                                <template x-for="(text, val) in field.options" :key="val">
                                    <option :value="val" x-text="text"></option>
                                </template>
                            </select>
                        </template>

                        {{-- Checkbox --}}
                        <template x-if="field.type === 'checkbox'">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="inputs[key]" class="h-5 w-5">
                                <span class="text-sm">فعال</span>
                            </label>
                        </template>

                        {{-- Number / Text --}}
                        <template x-if="field.type !== 'select' && field.type !== 'checkbox'">
                            <input :type="field.type === 'number' ? 'number' : 'text'"
                                   x-model="inputs[key]"
                                   :step="field.step || 'any'"
                                   :min="field.min"
                                   :max="field.max"
                                   class="w-full border rounded-lg p-2 font-mono"
                                   dir="ltr">
                        </template>

                        <p x-show="field.help" class="text-xs text-gray-500 mt-1" x-text="field.help"></p>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- دکمه‌ها --}}
    <div x-show="selectedItem" class="flex gap-2 flex-wrap mb-4">
        <button @click="calculate()"
                :disabled="loading"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg disabled:opacity-50 transition">
            🧮 محاسبه
        </button>
        <button @click="save()"
                :disabled="loading"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg disabled:opacity-50 transition">
            💾 محاسبه و ذخیره
        </button>
        <button @click="reset()" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300">
            ↺ پاک‌کردن
        </button>
    </div>

    {{-- نتیجه --}}
    <template x-if="result">
        <div class="bg-emerald-50 border-r-4 border-emerald-500 rounded-lg p-4 mb-4">
            <div class="text-sm text-emerald-800">مبلغ محاسبه‌شده:</div>
            <div class="text-2xl font-bold font-mono text-emerald-900 my-2" x-text="formatMoney(result.amount) + ' ریال'"></div>

            <template x-if="result.old_amount !== undefined && result.old_amount !== result.amount">
                <div class="text-xs text-gray-600 mt-2">
                    تغییر نسبت به قبل:
                    <span :class="result.amount > result.old_amount ? 'text-red-600' : 'text-green-600'"
                          x-text="(result.amount > result.old_amount ? '+' : '') + formatMoney(result.amount - result.old_amount) + ' ریال'"></span>
                </div>
            </template>

            <template x-if="result.breakdown && Object.keys(result.breakdown).length > 0">
                <table class="w-full text-sm mt-3 border">
                    <thead class="bg-gray-100">
                        <tr><th class="p-2 text-right">جزء</th><th class="p-2 text-right">مقدار</th></tr>
                    </thead>
                    <tbody>
                        <template x-for="(v, k) in result.breakdown" :key="k">
                            <tr class="border-t">
                                <td class="p-2" x-text="k"></td>
                                <td class="p-2 font-mono" x-text="v"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </template>
        </div>
    </template>

    {{-- خطا --}}
    <template x-if="error">
        <div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-3 rounded-lg mb-4" x-text="error"></div>
    </template>

    {{-- تاریخچه --}}
    <template x-if="history.length > 0">
        <div class="mt-6 bg-white border rounded-lg p-4">
            <h3 class="font-bold mb-3">📜 تاریخچه تغییرات این تعرفه</h3>
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-right">کاربر</th>
                        <th class="p-2 text-right">مبلغ قبلی</th>
                        <th class="p-2 text-right">مبلغ جدید</th>
                        <th class="p-2 text-right">تاریخ</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="h in history" :key="h.id">
                        <tr class="border-t">
                            <td class="p-2" x-text="h.user_name"></td>
                            <td class="p-2 font-mono" x-text="formatMoney(h.old_amount)"></td>
                            <td class="p-2 font-mono" x-text="formatMoney(h.new_amount)"></td>
                            <td class="p-2 text-xs text-gray-500" x-text="h.date"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </template>

    <style>[x-cloak] { display: none !important; }</style>
</div>

<script>
function tariffEditor(config) {
    return {
        schemas: config.schemas,
        slug: config.slug,
        csrf: config.csrf,
        userRole: config.userRole,
        officerType: config.officerType,

        selectedItem: '',
        inputs: {},
        currentSchema: {},
        loading: false,
        result: null,
        error: '',
        history: [],

        get filteredSchemas() {
            // فیلتر بر اساس نقش
            const result = {};
            for (const [id, def] of Object.entries(this.schemas)) {
                const type = def.type || 'levy'; // از schema بخون یا پیش‌فرض

                if (this.userRole === 'fee_officer') {
                    if (this.officerType === 'technical' && type !== 'levy') continue;
                    if (this.officerType === 'financial' && type !== 'service_fee') continue;
                }

                result[id] = def;
            }
            return result;
        },

        renderForm() {
            this.result = null;
            this.error = '';
            this.inputs = {};
            this.history = [];

            if (!this.selectedItem || !this.schemas[this.selectedItem]) {
                this.currentSchema = {};
                return;
            }

            this.currentSchema = this.schemas[this.selectedItem].schema || {};

            // مقادیر پیش‌فرض
            for (const [key, field] of Object.entries(this.currentSchema)) {
                if (field.default !== undefined) {
                    this.inputs[key] = field.default;
                } else if (field.type === 'checkbox') {
                    this.inputs[key] = false;
                } else {
                    this.inputs[key] = '';
                }
            }

            this.loadHistory();
        },

        async calculate() {
            this.loading = true;
            this.error = '';
            try {
                const res = await fetch(`/village/${this.slug}/tariff/calculate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                    body: JSON.stringify({
                        item_id: parseInt(this.selectedItem),
                        inputs: this.inputs,
                    }),
                });
                const data = await res.json();
                if (data.ok) {
                    this.result = data;
                } else {
                    this.error = data.error || 'خطا در محاسبه';
                }
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },

        async save() {
            if (!confirm('آیا مطمئن هستید که می‌خواهید این تغییر را ذخیره کنید؟')) return;

            this.loading = true;
            this.error = '';
            try {
                const res = await fetch(`/village/${this.slug}/tariff/save`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                    body: JSON.stringify({
                        item_id: parseInt(this.selectedItem),
                        inputs: this.inputs,
                    }),
                });
                const data = await res.json();
                if (data.ok) {
                    this.result = data;
                    this.loadHistory();
                    alert('✅ ' + (data.message || 'ذخیره شد'));
                } else {
                    this.error = data.error || 'خطا در ذخیره';
                }
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },

        async loadHistory() {
            try {
                const res = await fetch(`/village/${this.slug}/tariff/history/${this.selectedItem}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                this.history = data.history || [];
            } catch (e) {
                this.history = [];
            }
        },

        reset() {
            this.selectedItem = '';
            this.inputs = {};
            this.currentSchema = {};
            this.result = null;
            this.error = '';
            this.history = [];
        },

        formatMoney(v) {
            const n = Number(v) || 0;
            return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
                .replace(/\d/g, d => '۰۱۲۳۴۵۶۷۸۹'[d]);
        },
    }
}
</script>
