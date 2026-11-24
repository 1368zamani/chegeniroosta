<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@graph": [
    {
      "@@type": "Organization",
      "@@id": "#organization",
      "name": "چگنی روستا",
      "alternateName": [
        "پایگاه اطلاعرسانی روستاهای شهرستان چگنی",
        "پایگاه اطلاعرسانی بخش‌های شهرستان چگنی"
      ],
      "url": "{{ url('/') }}",
      "logo": {
        "@@type": "ImageObject",
        "url": "{{ asset('favicon.png') }}"
      }
    },
    {
      "@@type": "WebSite",
      "@@id": "#website",
      "name": "چگنی روستا",
      "url": "{{ url('/') }}",
      "inLanguage": "fa-IR",
      "publisher": { "@@id": "#organization" },
      "potentialAction": {
        "@@type": "SearchAction",
        "target": "{{ route('search.index') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
  ]
}
</script>
