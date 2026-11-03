{{--
    کارت یک «عنوان عوارض» در صفحه‌ی عمومی شهروند.

    ورودی‌ها:
      $card  → (object) title, item, item_count, amount, selectable
      $meta  → رنگ و برچسب دسته
--}}
@php
    $t          = $card->title;
    $selectable = $card->selectable;
    $amount     = (int) $card->amount;
    $itemId     = $card->item?->id;
    $unit       = $t->unit ?: 'مورد';
@endphp

<label class="item-card block bg-white rounded-xl p-4 transition-all shadow-sm"
       id="card-{{ $t->id }}"
       style="border:2px solid #e0e0e0; {{ $selectable ? 'cursor:pointer;' : 'opacity:.72;cursor:not-allowed;' }}">

    <div class="flex items-start gap-3">
        <input type="checkbox"
               class="item-check mt-1 w-5 h-5 shrink-0"
               data-id="{{ $itemId ?? '' }}"
               data-amount="{{ $amount }}"
               data-title="{{ str_replace('"', '&quot;', $t->name_fa) }}"
               @disabled(!$selectable)>

        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2 flex-wrap">
                <div class="font-bold text-gray-800 text-[15px]">{{ $t->name_fa }}</div>
                <span class="text-[10.5px] px-2 py-0.5 rounded-full shrink-0 whitespace-nowrap"
                      style="background:{{ $meta['soft'] }};color:{{ $meta['color'] }};">
                    {{ $unit }}
                </span>
            </div>

            @if($t->description)
                <div class="text-[12.5px] text-gray-500 mt-1 leading-relaxed">
                    {{ $t->description }}
                </div>
            @endif

            <div class="mt-2.5 flex items-baseline gap-1.5 flex-wrap">
                <span class="text-lg font-extrabold font-mono" style="color:{{ $meta['color'] }};">
                    {{ \App\Support\Number::fa(number_format($amount)) }}
                </span>
                <span class="text-xs text-gray-500">ریال</span>
                <span class="text-[11px] text-gray-400">به‌ازای هر {{ $unit }}</span>
            </div>

            @if(!$selectable)
                <div class="mt-2 text-[11px] text-amber-700">
                    ⚠️ تعرفه‌ی این قلم برای این روستا هنوز در سیستم ثبت نشده است — برای پرداخت با دهیاری تماس بگیرید.
                </div>
            @elseif($card->item_count > 1)
                <div class="mt-2 text-[11px] text-gray-400">
                    مبلغ نهایی بر اساس نوع بنا و متراژ توسط دهیاری محاسبه می‌شود.
                </div>
            @endif

            @if($t->effective_from)
                <div class="mt-1 text-[10.5px] text-gray-400">
                    تاریخ اعمال:
                    {{ \App\Support\Number::fa(\Morilog\Jalali\Jalalian::fromCarbon($t->effective_from)->format('Y/m/d')) }}
                </div>
            @endif
        </div>
    </div>
</label>
