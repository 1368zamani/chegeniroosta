@extends('admin.users.layout')

@section('title', 'ویرایش کاربر')
@section('header', 'ویرایش کاربر')

@section('content')

<form method="POST" action="{{ route('admin.users.update', $user->id) }}">
    @csrf
    @method('PUT')

    <div class="card">

        <div class="row">
            <div class="field">
                <label>نام و نام خانوادگی</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="field">
                <label>ایمیل</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required style="direction:ltr;">
            </div>
        </div>

        <div class="row">
            <div class="field">
                <label>رمز عبور (اختیاری)</label>
                <input type="text" name="password">
                <div class="hint">اگر خالی بگذاری، رمز قبلی حفظ می‌شود.</div>
            </div>
            <div class="field">
                <label>نقش</label>
                <select name="role" id="roleSelect" required>
                    <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>سوپر ادمین</option>
                    <option value="section_admin" {{ old('role', $user->role) == 'section_admin' ? 'selected' : '' }}>ادمین بخش</option>
                    <option value="village_admin" {{ old('role', $user->role) == 'village_admin' ? 'selected' : '' }}>ادمین روستا</option>
                </select>
            </div>
        </div>

        <div class="field" id="sectionField">
            <label>بخش مربوطه</label>
            <select name="section_id">
                <option value="">— انتخاب بخش —</option>
                @foreach($sections as $s)
                    <option value="{{ $s->id }}" {{ old('section_id', $user->section_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="field" id="villageField">
            <label>روستای مربوطه</label>
            <select name="village_id">
                <option value="">— انتخاب روستا —</option>
                @foreach($villages as $v)
                    <option value="{{ $v->id }}" {{ old('village_id', $user->village_id) == $v->id ? 'selected' : '' }}>{{ $v->name }} ({{ $v->section }})</option>
                @endforeach
            </select>
        </div>

        <div style="display:flex; gap:12px;">
            <button type="submit" class="btn btn-primary">💾 ذخیره</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">انصراف</a>
        </div>

    </div>

</form>

<script>
(function() {
    const roleSelect = document.getElementById('roleSelect');
    const sectionField = document.getElementById('sectionField');
    const villageField = document.getElementById('villageField');

    function updateFields() {
        const v = roleSelect.value;
        sectionField.style.display = (v === 'section_admin') ? 'block' : 'none';
        villageField.style.display = (v === 'village_admin') ? 'block' : 'none';
    }

    roleSelect.addEventListener('change', updateFields);
    updateFields();
})();
</script>

@endsection
