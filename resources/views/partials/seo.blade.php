{{-- SEO Meta Tags --}}
<meta name="description" content="{{ $seo_description ?? 'سامانه اطلاع‌رسانی روستاهای شهرستان چگنی' }}">
<meta name="keywords" content="{{ $seo_keywords ?? 'چگنی, لرستان, روستا, ویسیان, شاهیوند, بخشداری' }}">
<meta name="robots" content="{{ $seo_robots ?? 'index, follow' }}">
<meta name="author" content="سامانه اطلاع‌رسانی چگنی">

{{-- Open Graph --}}
<meta property="og:site_name" content="روستا نما چگنی">
<meta property="og:type" content="{{ $og_type ?? 'website' }}">
<meta property="og:title" content="{{ $og_title ?? ($seo_title ?? 'روستا نما') }}">
<meta property="og:description" content="{{ $og_description ?? ($seo_description ?? '') }}">
<meta property="og:url" content="{{ url()->current() }}">
@if(isset($og_image))
<meta property="og:image" content="{{ $og_image }}">
@endif
<meta property="og:locale" content="fa_IR">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $og_title ?? ($seo_title ?? 'روستا نما') }}">
<meta name="twitter:description" content="{{ $og_description ?? ($seo_description ?? '') }}">

{{-- Theme Color --}}
<meta name="theme-color" content="#2e7d32">

{{-- Favicon --}}
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect fill='%231565c0' width='100' height='100' rx='20'/><text x='50' y='68' font-size='60' text-anchor='middle' fill='white' font-family='Tahoma'>چ</text></svg>">
