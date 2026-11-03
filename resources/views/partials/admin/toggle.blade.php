{{--
    دکمه‌ی فعال/غیرفعال کردن محتوا (استفاده در فهرست‌های پنل ادمین)

    نحوه‌ی استفاده:
        @include('partials.admin.toggle', ['type' => 'attraction', 'model' => $a])

    نوع‌های مجاز در app/Http/Controllers/Admin/ToggleController.php تعریف شده‌اند.
    استایل‌ها داخل خود دکمه هستند تا به هیچ فایل CSS وابسته نباشد.
--}}
@php
    $__on = (bool) ($model->is_active ?? $model->is_published ?? $model->is_public ?? false);
@endphp
<form method="POST" action="{{ route('admin.toggle', [$type, $model->getKey()]) }}" style="display:inline;">
    @csrf
    <button type="submit"
            title="{{ $__on ? 'غیرفعال کردن (از سایت برداشته می‌شود)' : 'فعال کردن (در سایت نمایش داده می‌شود)' }}"
            onclick="return confirm('{{ $__on ? 'این مورد غیرفعال شود؟ از سایت برداشته می‌شود ولی حذف نمی‌شود.' : 'این مورد فعال شود و در سایت نمایش داده شود؟' }}')"
            style="display:inline-block;padding:6px 11px;font-size:12px;font-family:inherit;border:none;border-radius:6px;cursor:pointer;color:#fff;background:{{ $__on ? '#e53935' : '#43a047' }};">
        {{ $__on ? '🔒 غیرفعال' : '🔓 فعال' }}
    </button>
</form>
