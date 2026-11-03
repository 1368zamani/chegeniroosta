<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'فرم تعرفه')</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        * { box-sizing: border-box; }
        body { font-family: Tahoma, 'B Nazanin', serif; font-size: 11pt; color: #000; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 12px; }
        .header h1 { font-size: 15pt; margin: 4px 0; }
        .header h2 { font-size: 13pt; margin: 4px 0; font-weight: normal; }
        .meta { display: flex; justify-content: space-between; font-size: 10pt; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: right; vertical-align: top; }
        th { background: #eee; font-weight: bold; }
        .center { text-align: center; }
        .ltr { direction: ltr; text-align: left; }
        .footer { margin-top: 18px; font-size: 10pt; }
        .signatures { display: flex; justify-content: space-around; margin-top: 32px; font-size: 10pt; }
        .sig-box { text-align: center; min-width: 120px; border-top: 1px solid #000; padding-top: 4px; }
        .warning { background: #fef3c7; border: 1px solid #d97706; padding: 6px; margin: 8px 0; font-size: 9.5pt; }
        @media print { .no-print { display: none; } }
    </style>
<link rel="stylesheet" href="/css/admin-master.css?v=2">
<link rel="stylesheet" href="{{ asset('css/admin-final-fix.css') }}?v=10">
<link rel="stylesheet" href="/css/admin-header-fix.css?v=1"></head>
<body>

@if(request()->has('print') === false)
<div class="no-print" style="background:#e2e8f0;padding:8px;margin-bottom:12px;text-align:center">
    <button onclick="window.print()" style="padding:6px 16px;cursor:pointer">چاپ این فرم</button>
    <a href="{{ url()->previous() }}" style="margin-right:16px">بازگشت</a>
</div>
@endif

@yield('content')

<script>
    @if(request()->query('auto') === '1')
    window.addEventListener('load', () => window.print());
    @endif
</script>
</body>
</html>
