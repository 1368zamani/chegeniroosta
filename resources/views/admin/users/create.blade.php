@extends('admin.users.layout')

@section('title', 'افزودن کاربر جدید')
@section('header', 'افزودن کاربر جدید')

@section('content')

<form method="POST" action="{{ route('admin.users.store') }}">
    @csrf

    <div class="card">

        <div class="row">
            <div class="field">
                <label>نام و نام خانوادگی</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="field">
                <label>ایمیل</label>
                <input type="email" name="email" value="{{ old('email') }}" required style="direction:ltr;">
            </div>
        </div>

        <div class="row">
            <div class="field">
                <label>رمز عبور</label>
                <input type="text" name="password" value="{{ old('password') }}" required>
                <div class="hint">حداقل ۶ کاراکتر</div>
            </div>
            <div class="field">
                <label>نقش</label>
                <select name="role" id="roleSelect" required>
                    <option value="">— انتخاب کنید —</option>
                    <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>سوپر ادمین</option>
                    <option value="section_admin" {{ old('role') == 'section_admin' ? 'selected' : '' }}>ادمین بخش</option>
                    <option value="village_admin" {{ old('role') == 'village_admin' ? 'selected' : '' }}>ادمین روستا</option>
                    <option value="fee_officer" {{ old('role') == 'fee_officer' ? 'selected' : '' }}>کارشناس مالی و فنی</option>
                </select>
            </div>
        </div>

        <div class="field" id="sectionField" style="display:none;">
            <label>بخش مربوطه</label>
            <select name="section_id">
                <option value="">— انتخاب بخش —</option>
                @foreach($sections as $s)
                    <option value="{{ $s->id }}" {{ old('section_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="field" id="villageField" style="display:none;">
            <label>روستای مربوطه</label>
            <select name="village_id">
                <option value="">— انتخاب روستا —</option>
                @foreach($villages as $v)
                    <option value="{{ $v->id }}" {{ old('village_id') == $v->id ? 'selected' : '' }}>{{ $v->name }} ({{ $v->section }})</option>
                @endforeach
            </select>
        </div>

        <div style="display:flex; gap:12px;">
            <button type="submit" class="btn btn-primary">💾 ذخیره</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">انصراف</a>
        </div>

    </div>


<div id="officerTypeBox" style="display:none;margin-top:15px;">
    <label>نوع کارشناس</label>
    <select name="officer_type" class="form-control">
        <option value="">انتخاب کنید</option>
        <option value="financial" {{ old('officer_type') == 'financial' ? 'selected' : '' }}>
            کارشناس مالی
        </option>
        <option value="technical" {{ old('officer_type') == 'technical' ? 'selected' : '' }}>
            کارشناس فنی و عمرانی
        </option>
    </select>
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
