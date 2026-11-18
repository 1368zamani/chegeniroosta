{{--
    نان‌ریزه (breadcrumb) سرصفحهٔ صفحات داخلی نسخهٔ ۳
    ورودی: $crumbs = [ ['label' => '...', 'url' => '...'|null], ... ]
    ورودی اختیاری: $maxWidth (پیش‌فرض ۱۱۲۰)
--}}
@php
    $__crumbs = $crumbs ?? [];
    $__maxW = $maxWidth ?? 1120;
@endphp
@if(count($__crumbs))
    <div class="v3i-crumbs" style="width: min({{ $__maxW }}px, 94%); margin: 0 auto;">
        <div class="crumbs">
            @foreach($__crumbs as $__i => $__c)
                @if($__i > 0)<span>/</span>@endif
                @if(!empty($__c['url']))
                    <a href="{{ $__c['url'] }}">{{ $__c['label'] }}</a>
                @else
                    <b>{{ $__c['label'] }}</b>
                @endif
            @endforeach
        </div>
    </div>
@endif
