@extends('admin.tariffs.layout')
@section('title', 'ویرایش آیتم‌های تعرفه')

@section('content')

<form method="POST" action="{{ route('admin.tariffs.update', $set->id) }}">
    @csrf @method('PUT')

    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px">
            <h3 style="margin:0; border:none; padding:0">✏️ ویرایش تعرفه {{ $set->village?->name }} — سال {{ $set->jalali_year }}</h3>
            <div style="display:flex; gap:8px; align-items:center">
                <label style="margin:0; font-size:13px">نرخ تورم (٪):</label>
                <input type="number" step="0.01" name="inflation_rate" value="{{ old('inflation_rate', $set->inflation_rate) }}" min="0" max="200" style="width:100px">
            </div>
        </div>
    </div>

    <div class="card">
        <h3>💰 عوارض محلی</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>عنوان</th>
                    <th style="width:100px">فعال</th>
                    <th style="width:140px">مبلغ سال قبل (ریال)</th>
                    <th style="width:100px">افزایش (٪)</th>
                    <th style="width:140px">مبلغ نهایی (ریال)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($levyItems as $i => $item)
                    <tr>
                        <td class="amount">{{ $i + 1 }}</td>
                        <td>
                            {{ $item->title?->name_fa }}
                            <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                        </td>
                        <td style="text-align:center">
                            <input type="checkbox" name="items[{{ $i }}][is_active]" value="1" @checked($item->is_active) style="width:20px; height:20px">
                        </td>
                        <td>
                            <input type="number" name="items[{{ $i }}][previous_amount]" value="{{ $item->previous_amount }}" min="0" class="amount">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="items[{{ $i }}][increase_percent]" value="{{ $item->increase_percent }}" min="0" max="500" class="amount">
                        </td>
                        <td>
                            <input type="number" name="items[{{ $i }}][final_amount]" value="{{ $item->final_amount }}" min="0" class="amount">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>🏛 بهای خدمات</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>عنوان</th>
                    <th style="width:100px">فعال</th>
                    <th style="width:140px">مبلغ سال قبل</th>
                    <th style="width:100px">افزایش (٪)</th>
                    <th style="width:140px">مبلغ نهایی</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceItems as $i => $item)
                    <tr>
                        <td class="amount">{{ $i + 1 }}</td>
                        <td>
                            {{ $item->title?->name_fa }}
                            <input type="hidden" name="items[{{ $i + $levyItems->count() }}][id]" value="{{ $item->id }}">
                        </td>
                        <td style="text-align:center">
                            <input type="checkbox" name="items[{{ $i + $levyItems->count() }}][is_active]" value="1" @checked($item->is_active) style="width:20px; height:20px">
                        </td>
                        <td>
                            <input type="number" name="items[{{ $i + $levyItems->count() }}][previous_amount]" value="{{ $item->previous_amount }}" min="0" class="amount">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="items[{{ $i + $levyItems->count() }}][increase_percent]" value="{{ $item->increase_percent }}" min="0" max="500" class="amount">
                        </td>
                        <td>
                            <input type="number" name="items[{{ $i + $levyItems->count() }}][final_amount]" value="{{ $item->final_amount }}" min="0" class="amount">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="display:flex; gap:10px">
        <button class="btn btn-green">💾 ذخیره</button>
        <a href="{{ route('admin.tariffs.show', $set->id) }}" class="btn btn-gray">انصراف</a>
    </div>

</form>
@endsection
