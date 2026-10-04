@extends('admin.tariffs.layout')
@section('title', 'ساخت مجموعه تعرفه جدید')

@section('content')
<div class="card">
    <h3>➕ ساخت مجموعه تعرفه جدید</h3>

    <div class="alert alert-info">
        💡 اگر برای روستا مجموعه سال قبل وجود دارد، می‌توانید آن را کپی کنید. نرخ افزایش پیش‌فرض = نرخ تورم سال.
    </div>

    <form method="POST" action="{{ route('admin.tariffs.store') }}">
        @csrf

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">

            <div>
                <label>روستا *</label>
                <select name="village_id" id="villageSelect" required onchange="updateCopyList()">
                    <option value="">— انتخاب کنید —</option>
                    @foreach($villages as $v)
                        <option value="{{ $v->id }}" @selected(old('village_id')==$v->id)>{{ $v->name }} ({{ $v->section?->name }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>سال شمسی *</label>
                <input type="number" name="jalali_year" value="{{ old('jalali_year', $suggestedYear) }}" min="1400" max="1450" required>
            </div>

            <div>
                <label>نرخ تورم اعلامی سال (٪)</label>
                <input type="number" step="0.01" name="inflation_rate" value="{{ old('inflation_rate', 40) }}" min="0" max="200">
                <div style="font-size:11.5px; color:#90a4ae; margin-top:4px">TODO(verify): نرخ رسمی بانک مرکزی</div>
            </div>

            <div>
                <label>کپی از مجموعه سال قبل</label>
                <select name="copy_from" id="copyFromSelect">
                    <option value="">— بدون کپی (خالی) —</option>
                </select>
                <div style="font-size:11.5px; color:#90a4ae; margin-top:4px">ابتدا روستا را انتخاب کنید</div>
            </div>

        </div>

        <div style="margin-top:18px">
            <button class="btn btn-green">➕ ساخت مجموعه</button>
            <a href="{{ route('admin.tariffs.index') }}" class="btn btn-gray">انصراف</a>
        </div>
    </form>
</div>

<script>
const previousSets = @json($previousSets->mapWithKeys(fn($sets, $vid) => [
    $vid => $sets->map(fn($s) => ['id' => $s->id, 'year' => $s->jalali_year, 'status' => $s->statusLabel()])->values()
]));

function updateCopyList() {
    const vid = document.getElementById('villageSelect').value;
    const sel = document.getElementById('copyFromSelect');
    sel.innerHTML = '<option value="">— بدون کپی (خالی) —</option>';
    if (!vid || !previousSets[vid]) return;
    previousSets[vid].forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = 'سال ' + s.year + ' (' + s.status + ')';
        sel.appendChild(opt);
    });
}
</script>
@endsection
