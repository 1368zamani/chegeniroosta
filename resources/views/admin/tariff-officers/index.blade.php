@extends(View::exists('layouts.admin') ? 'layouts.admin' : 'layouts.tariff-fallback')
@section('title', 'مدیریت کارشناسان عوارض')

@section('content')
<div class="p-4 md:p-6" dir="rtl">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold">👨‍💼 کارشناسان فنی و مالی تعرفه</h1>
            <p class="text-sm text-gray-600 mt-1">این کارشناسان به پنل تعرفه دسترسی دارند — از طریق <code dir="ltr">/tariff-officer/login</code></p>
        </div>
        <a href="{{ route('admin.tariffs.index') }}" class="px-4 py-2 border rounded">← لیست تعرفه‌ها</a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border-r-4 border-emerald-500 text-emerald-800 p-3 mb-4 rounded">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border-r-4 border-red-500 text-red-800 p-3 mb-4 rounded">
            <ul class="list-disc pr-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- فرم ایجاد --}}
    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <h2 class="font-bold mb-4 text-lg border-b pb-2">➕ افزودن کارشناس جدید</h2>
        <form method="POST" action="{{ route('admin.tariff-officers.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm mb-1 font-medium">نام و نام خانوادگی <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="{{ old('name') }}"
                           class="w-full border rounded p-2" placeholder="مثلاً: علی رضایی">
                </div>
                <div>
                    <label class="block text-sm mb-1 font-medium">نام کاربری <span class="text-red-500">*</span></label>
                    <input type="text" name="username" required value="{{ old('username') }}"
                           dir="ltr" class="w-full border rounded p-2 font-mono"
                           pattern="[a-zA-Z0-9_.\-]+"
                           placeholder="ali.rezaei">
                    <p class="text-xs text-gray-500 mt-1">فقط حروف انگلیسی، اعداد، نقطه، خط تیره</p>
                </div>
                <div>
                    <label class="block text-sm mb-1 font-medium">رمز عبور <span class="text-red-500">*</span></label>
                    <input type="text" name="password" required minlength="6" value="{{ old('password') }}"
                           dir="ltr" class="w-full border rounded p-2 font-mono"
                           placeholder="حداقل ۶ کاراکتر">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                <div>
                    <label class="block text-sm mb-1 font-medium">بخشداری <span class="text-red-500">*</span></label>
                    <select name="section_id" required class="w-full border rounded p-2">
                        <option value="">— انتخاب کنید —</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" @selected(old('section_id') == $s->id)>
                                {{ $s->name }} ({{ $s->county ?? '' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1 font-medium">نقش <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full border rounded p-2">
                        <option value="section_admin" @selected(old('role') == 'section_admin')>مدیر بخشداری (کامل)</option>
                        <option value="fee_officer" @selected(old('role') == 'fee_officer')>کارشناس مالی (کامل)</option>
                        <option value="tariff_officer" @selected(old('role') == 'tariff_officer')>کارشناس تعرفه (محدود)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1 font-medium">ایمیل (اختیاری)</label>
                    <input type="email" name="email" value="{{ old('email') }}" dir="ltr"
                           class="w-full border rounded p-2 font-mono">
                </div>
            </div>
            <button class="mt-4 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded font-medium">
                ساخت کارشناس
            </button>
        </form>
    </div>

    {{-- جدول --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <div class="p-4 border-b bg-gray-50 font-bold">
            📋 لیست کارشناسان ({{ $officers->total() }} نفر)
        </div>
        <table class="w-full text-right text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2">#</th>
                    <th class="p-2">نام</th>
                    <th class="p-2">نام کاربری</th>
                    <th class="p-2">بخش</th>
                    <th class="p-2">نقش</th>
                    <th class="p-2">تماس</th>
                    <th class="p-2">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($officers as $i => $o)
                <tr class="border-t">
                    <td class="p-2 text-gray-500">{{ $officers->firstItem() + $i }}</td>
                    <td class="p-2 font-medium">{{ $o->name }}</td>
                    <td class="p-2 font-mono text-xs" dir="ltr">{{ $o->username ?: '—' }}</td>
                    <td class="p-2 text-xs">
                        @php $sec = $sections->firstWhere('id', $o->section_id); @endphp
                        {{ $sec?->name ?: '—' }}
                    </td>
                    <td class="p-2 text-xs">
                        <span class="px-2 py-1 rounded text-xs
                            @if($o->role === 'section_admin') bg-blue-100 text-blue-800
                            @elseif($o->role === 'fee_officer') bg-purple-100 text-purple-800
                            @elseif($o->role === 'tariff_officer') bg-amber-100 text-amber-800
                            @else bg-gray-100 @endif">
                            {{ match($o->role) {
                                'section_admin' => 'مدیر بخش',
                                'fee_officer'   => 'کارشناس مالی',
                                'tariff_officer'=> 'کارشناس تعرفه',
                                default         => $o->role
                            } }}
                        </span>
                    </td>
                    <td class="p-2 text-xs" dir="ltr">{{ $o->email ?: ($o->phone ?: '—') }}</td>
                    <td class="p-2 whitespace-nowrap">
                        <button type="button" class="text-blue-600 hover:underline text-xs ml-2"
                                onclick="document.getElementById('edit-{{ $o->id }}').classList.toggle('hidden')">
                            ویرایش
                        </button>
                        <button type="button" class="text-amber-600 hover:underline text-xs ml-2"
                                onclick="document.getElementById('reset-{{ $o->id }}').classList.toggle('hidden')">
                            تغییر رمز
                        </button>
                        <form method="POST" action="{{ route('admin.tariff-officers.destroy', $o) }}"
                              class="inline" onsubmit="return confirm('حذف شود؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline text-xs">حذف</button>
                        </form>
                    </td>
                </tr>
                <tr id="edit-{{ $o->id }}" class="hidden bg-gray-50">
                    <td colspan="7" class="p-4">
                        <form method="POST" action="{{ route('admin.tariff-officers.update', $o) }}" class="space-y-3">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                <div><label class="block text-xs mb-1">نام</label>
                                    <input type="text" name="name" value="{{ $o->name }}" required class="w-full border rounded p-1.5 text-sm"></div>
                                <div><label class="block text-xs mb-1">نام کاربری</label>
                                    <input type="text" name="username" value="{{ $o->username }}" dir="ltr" required class="w-full border rounded p-1.5 text-sm font-mono"></div>
                                <div><label class="block text-xs mb-1">بخش</label>
                                    <select name="section_id" required class="w-full border rounded p-1.5 text-sm">
                                        @foreach($sections as $s)
                                            <option value="{{ $s->id }}" @selected($o->section_id == $s->id)>{{ $s->name }}</option>
                                        @endforeach
                                    </select></div>
                                <div><label class="block text-xs mb-1">نقش</label>
                                    <select name="role" required class="w-full border rounded p-1.5 text-sm">
                                        <option value="section_admin" @selected($o->role==='section_admin')>مدیر بخشداری</option>
                                        <option value="fee_officer" @selected($o->role==='fee_officer')>کارشناس مالی</option>
                                        <option value="tariff_officer" @selected($o->role==='tariff_officer')>کارشناس تعرفه</option>
                                    </select></div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div><label class="block text-xs mb-1">ایمیل</label>
                                    <input type="email" name="email" value="{{ $o->email }}" dir="ltr" class="w-full border rounded p-1.5 text-sm font-mono"></div>
                                <div><label class="block text-xs mb-1">رمز جدید (اختیاری)</label>
                                    <input type="text" name="password" dir="ltr" minlength="6" class="w-full border rounded p-1.5 text-sm font-mono" placeholder="خالی = بی‌تغییر"></div>
                                <div><label class="block text-xs mb-1">تلفن</label>
                                    <input type="text" name="phone" value="{{ $o->phone }}" dir="ltr" class="w-full border rounded p-1.5 text-sm font-mono"></div>
                            </div>
                            <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm">ذخیره</button>
                        </form>
                    </td>
                </tr>
                <tr id="reset-{{ $o->id }}" class="hidden bg-amber-50">
                    <td colspan="7" class="p-4">
                        <form method="POST" action="{{ route('admin.tariff-officers.reset-password', $o) }}" class="flex gap-2 items-end">
                            @csrf
                            <div class="flex-1">
                                <label class="block text-xs mb-1">رمز عبور جدید برای «{{ $o->name }}»</label>
                                <input type="text" name="new_password" dir="ltr" required minlength="6"
                                       class="w-full border rounded p-1.5 text-sm font-mono"
                                       placeholder="حداقل ۶ کاراکتر">
                            </div>
                            <button class="bg-amber-600 text-white px-4 py-1.5 rounded text-sm">تغییر رمز</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-6 text-center text-gray-500">هیچ کارشناسی ثبت نشده است.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $officers->links() }}</div>

    <div class="mt-6 bg-blue-50 border-r-4 border-blue-500 p-4 rounded text-sm text-blue-900">
        <strong>ℹ️ راهنما:</strong>
        <ul class="list-disc pr-5 mt-2 space-y-1">
            <li>کارشناسان از طریق <code dir="ltr">{{ url('/tariff-officer/login') }}</code> وارد می‌شوند.</li>
            <li>لینک ورود در سایت بخشداری هر بخش در پایین صفحه موجود است.</li>
            <li>هر کارشناس فقط به تعرفه‌ی روستاهای <strong>بخش خودش</strong> دسترسی دارد.</li>
        </ul>
    </div>

</div>
@endsection
