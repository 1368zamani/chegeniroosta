<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TariffChangeLog;
use App\Models\TariffTitle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Morilog\Jalali\Jalalian;

/**
 * مدیریت «عوارض پسماند» و «عوارض ساخت‌وساز» توسط کارشناس عمران.
 *
 * دسترسی‌ها در AppServiceProvider با Gate تعریف شده‌اند:
 *   works-fees.view    → مدیر سامانه، کارشناس فنی، کارشناس مالی، ادمین بخش/روستا
 *   works-fees.manage  → مدیر سامانه + کارشناس فنی (همه) · کارشناس مالی (فقط پسماند)
 */
class WorksFeeController extends Controller
{
    /** دسته‌های نمایشی — ترتیب تب‌ها */
    public const GROUP_KEYS = ['waste', 'new_build', 'renovation'];

    public const GROUP_META = [
        'waste' => [
            'title'  => 'عوارض پسماند',
            'icon'   => '🗑',
            'color'  => '#2e7d32',
            'soft'   => '#e8f5e9',
            'desc'   => 'جمع‌آوری و دفع زباله‌ی خانگی، تجاری و صنعتی',
            'category' => 'waste',
            'subcategory' => null,
        ],
        'new_build' => [
            'title'  => 'ساخت‌وساز جدید',
            'icon'   => '🏗',
            'color'  => '#1565c0',
            'soft'   => '#e3f2fd',
            'desc'   => 'پروانه‌ی ساخت، زیربنا، تفکیک و الحاق',
            'category' => 'construction',
            'subcategory' => 'new_build',
        ],
        'renovation' => [
            'title'  => 'ساخت‌وساز تعمیری',
            'icon'   => '🔨',
            'color'  => '#e65100',
            'soft'   => '#fff3e0',
            'desc'   => 'بازسازی، تمدید پروانه و تغییر کاربری',
            'category' => 'construction',
            'subcategory' => 'renovation',
        ],
    ];

    // ══════════════════════════════════════════════════════
    // فهرست اصلی (تب‌ها)
    // ══════════════════════════════════════════════════════

    public function index(Request $request)
    {
        abort_unless(Gate::allows('works-fees.view'), 403);

        $titles = TariffTitle::categorized()
            ->with(['lastEditor:id,name'])
            ->withCount('items')
            ->ordered()
            ->get();

        $groups = [];
        foreach (self::GROUP_KEYS as $key) {
            $meta = self::GROUP_META[$key];

            $rows = $titles->filter(function (TariffTitle $t) use ($meta) {
                return $t->category === $meta['category']
                    && ($meta['subcategory'] === null
                        ? $t->subcategory === null
                        : $t->subcategory === $meta['subcategory']);
            })->values();

            $groups[$key] = [
                'meta'  => $meta,
                'rows'  => $rows,
                'count' => $rows->count(),
                'active_count' => $rows->where('is_active', true)->count(),
                'can_manage'   => Gate::allows('works-fees.manage', $meta['category']),
            ];
        }

        $stats = [
            'total'          => $titles->count(),
            'active'         => $titles->where('is_active', true)->count(),
            'inactive'       => $titles->where('is_active', false)->count(),
            'with_items'     => $titles->where('items_count', '>', 0)->count(),
            'last_change_at' => TariffChangeLog::max('created_at'),
        ];

        return view('admin.works-fees.index', [
            'groups' => $groups,
            'stats'  => $stats,
            'units'  => TariffTitle::UNITS,
            'canManageAny' => Gate::allows('works-fees.manage'),
        ]);
    }

    // ══════════════════════════════════════════════════════
    // افزودن قلم جدید
    // ══════════════════════════════════════════════════════

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        abort_unless(
            Gate::allows('works-fees.manage', $data['category']),
            403,
            'اجازه‌ی افزودن عوارض در این دسته را ندارید.'
        );

        $title = DB::transaction(function () use ($data) {
            $code = $this->generateCode($data['category']);

            $title = TariffTitle::create([
                'code'           => $code,
                'type'           => $data['category'] === TariffTitle::CATEGORY_WASTE ? 'service_fee' : 'levy',
                'category'       => $data['category'],
                'subcategory'    => $data['subcategory'] ?? null,
                'name_fa'        => $data['name_fa'],
                'unit'           => $data['unit'],
                'description'    => $data['description'] ?? null,
                'base_amount'    => $data['base_amount'],
                'effective_from' => $data['effective_from'],
                'updated_by'     => auth()->id(),
                'is_optional'    => false,
                'sort_order'     => (int) (TariffTitle::max('sort_order') ?? 0) + 1,
                'is_active'      => true,
            ]);

            TariffChangeLog::record($title, TariffChangeLog::ACTION_CREATE, [
                'new_amount' => $title->base_amount,
                'new_values' => $this->snapshot($title),
                'reason'     => $data['reason'] ?? 'ایجاد قلم جدید',
            ]);

            return $title;
        });

        return redirect()
            ->route('admin.works-fees.index', ['tab' => $this->groupKeyFor($title)])
            ->with('success', "قلم «{$title->name_fa}» با موفقیت اضافه شد.");
    }

    // ══════════════════════════════════════════════════════
    // تغییر سریع مبلغ
    // ══════════════════════════════════════════════════════

    public function updateAmount(Request $request, TariffTitle $title)
    {
        abort_unless(Gate::allows('works-fees.manage', $title->category), 403);

        $data = $request->validate([
            'base_amount' => ['required', 'integer', 'min:1', 'max:100000000000'],
            'reason'      => ['required', 'string', 'max:500'],
        ], [
            'base_amount.required' => 'مبلغ را وارد کنید.',
            'base_amount.integer'  => 'مبلغ باید عدد صحیح باشد.',
            'base_amount.min'      => 'مبلغ نمی‌تواند صفر یا منفی باشد.',
            'base_amount.max'      => 'مبلغ بیش از حد بزرگ است.',
            'reason.required'      => 'دلیل تغییر مبلغ الزامی است (برای ثبت در تاریخچه).',
        ]);

        $old = (int) $title->base_amount;
        $new = (int) $data['base_amount'];

        if ($old === $new) {
            return back()->with('info', 'مبلغ تغییری نکرده است.');
        }

        DB::transaction(function () use ($title, $old, $new, $data) {
            $title->update([
                'base_amount' => $new,
                'updated_by'  => auth()->id(),
            ]);

            TariffChangeLog::record($title, TariffChangeLog::ACTION_UPDATE_AMOUNT, [
                'old_amount' => $old,
                'new_amount' => $new,
                'old_values' => ['base_amount' => $old],
                'new_values' => ['base_amount' => $new],
                'reason'     => $data['reason'],
            ]);
        });

        return back()->with('success', 'مبلغ با موفقیت به‌روزرسانی و در تاریخچه ثبت شد.');
    }

    // ══════════════════════════════════════════════════════
    // ویرایش کامل
    // ══════════════════════════════════════════════════════

    public function update(Request $request, TariffTitle $title)
    {
        abort_unless(Gate::allows('works-fees.manage', $title->category), 403);

        $data = $this->validatePayload($request, $title);

        abort_unless(Gate::allows('works-fees.manage', $data['category']), 403);

        $old = $this->snapshot($title);
        $oldAmount = (int) $title->base_amount;

        DB::transaction(function () use ($title, $data, $old, $oldAmount) {
            $title->update([
                'category'       => $data['category'],
                'subcategory'    => $data['subcategory'] ?? null,
                'name_fa'        => $data['name_fa'],
                'unit'           => $data['unit'],
                'description'    => $data['description'] ?? null,
                'base_amount'    => $data['base_amount'],
                'effective_from' => $data['effective_from'],
                'is_active'      => (bool) ($data['is_active'] ?? false),
                'updated_by'     => auth()->id(),
            ]);

            $action = $oldAmount !== (int) $data['base_amount']
                ? TariffChangeLog::ACTION_UPDATE_AMOUNT
                : TariffChangeLog::ACTION_UPDATE_INFO;

            TariffChangeLog::record($title, $action, [
                'old_amount' => $oldAmount,
                'new_amount' => (int) $data['base_amount'],
                'old_values' => $old,
                'new_values' => $this->snapshot($title->fresh()),
                'reason'     => $data['reason'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.works-fees.index', ['tab' => $this->groupKeyFor($title->fresh())])
            ->with('success', 'تغییرات با موفقیت ذخیره شد.');
    }

    // ══════════════════════════════════════════════════════
    // فعال / غیرفعال (حذف موقت)
    // ══════════════════════════════════════════════════════

    public function toggle(TariffTitle $title)
    {
        abort_unless(Gate::allows('works-fees.manage', $title->category), 403);

        $new = ! $title->is_active;

        DB::transaction(function () use ($title, $new) {
            $title->update(['is_active' => $new, 'updated_by' => auth()->id()]);

            TariffChangeLog::record($title, $new
                ? TariffChangeLog::ACTION_RESTORE
                : TariffChangeLog::ACTION_DELETE, [
                    'old_values' => ['is_active' => ! $new],
                    'new_values' => ['is_active' => $new],
                    'reason'     => $new ? 'فعال‌سازی مجدد' : 'غیرفعال‌سازی موقت',
                ]);
        });

        return back()->with('success', $new
            ? 'قلم فعال شد و دوباره در صفحه‌ی شهروند نمایش داده می‌شود.'
            : 'قلم غیرفعال شد (حذف نشد — تاریخچه و فاکتورها محفوظ است).');
    }

    // ══════════════════════════════════════════════════════
    // تاریخچه
    // ══════════════════════════════════════════════════════

    public function logs(TariffTitle $title)
    {
        abort_unless(Gate::allows('works-fees.view'), 403);

        $logs = $title->changeLogs()->limit(60)->get()->map(function (TariffChangeLog $l) {
            return [
                'action'      => $l->actionLabel(),
                'color'       => $l->actionColor(),
                'user'        => $l->user_name ?: '—',
                'amount'      => $l->amountChangeLabel(),
                'reason'      => $l->reason,
                'date_jalali' => $l->created_at
                    ? Jalalian::fromCarbon($l->created_at)->format('Y/m/d H:i')
                    : '—',
            ];
        });

        return response()->json([
            'title' => $title->name_fa,
            'unit'  => $title->unitLabel(),
            'logs'  => $logs,
        ]);
    }

    // ══════════════════════════════════════════════════════
    // کمکی‌ها
    // ══════════════════════════════════════════════════════

    /** اعتبارسنجی مشترک افزودن و ویرایش */
    private function validatePayload(Request $request, ?TariffTitle $existing = null): array
    {
        $data = $request->validate([
            'name_fa'        => ['required', 'string', 'max:255'],
            'category'       => ['required', Rule::in([TariffTitle::CATEGORY_WASTE, TariffTitle::CATEGORY_CONSTRUCTION])],
            'subcategory'    => ['nullable', Rule::in([TariffTitle::SUB_NEW_BUILD, TariffTitle::SUB_RENOVATION]), 'required_if:category,construction'],
            'unit'           => ['required', 'string', Rule::in(TariffTitle::UNITS)],
            'base_amount'    => ['required', 'integer', 'min:1', 'max:100000000000'],
            'description'    => ['nullable', 'string', 'max:500'],
            'effective_from' => ['required', 'string', 'max:20'],
            'is_active'      => ['nullable', 'boolean'],
            'reason'         => ['nullable', 'string', 'max:500'],
        ], [
            'name_fa.required'        => 'نام عوارض را وارد کنید.',
            'category.required'       => 'دسته را انتخاب کنید.',
            'subcategory.required_if' => 'برای عوارض ساخت‌وساز، زیرگروه (جدید یا تعمیری) الزامی است.',
            'unit.required'           => 'واحد محاسبه را انتخاب کنید.',
            'unit.in'                 => 'واحد انتخاب‌شده معتبر نیست.',
            'base_amount.required'    => 'مبلغ پایه را وارد کنید.',
            'base_amount.integer'     => 'مبلغ باید عدد صحیح باشد.',
            'base_amount.min'         => 'مبلغ نمی‌تواند صفر یا منفی باشد.',
            'base_amount.max'         => 'مبلغ بیش از حد بزرگ است.',
            'effective_from.required' => 'تاریخ اعمال تعرفه را وارد کنید.',
        ]);

        // اگر دسته پسماند است، زیرگروه باید خالی باشد
        if ($data['category'] === TariffTitle::CATEGORY_WASTE) {
            $data['subcategory'] = null;
        }

        // تبدیل تاریخ شمسی به میلادی
        $carbon = $this->jalaliToCarbon($data['effective_from']);
        if (! $carbon) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'effective_from' => 'تاریخ اعمال معتبر نیست. نمونه‌ی درست: 1405/01/01',
            ]);
        }
        $data['effective_from'] = $carbon->toDateString();

        return $data;
    }

    /** تبدیل «۱۴۰۵/۰۱/۰۱» به Carbon میلادی */
    private function jalaliToCarbon(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        $v = str_replace(
            ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹', '٠','١','٢','٣','٤','٥','٦','٧','٨','٩'],
            ['0','1','2','3','4','5','6','7','8','9', '0','1','2','3','4','5','6','7','8','9'],
            (string) $value
        );
        $v = str_replace(['-', '.'], '/', trim($v));
        $parts = explode('/', $v);

        if (count($parts) !== 3) {
            return null;
        }

        [$y, $m, $d] = [(int) $parts[0], (int) $parts[1], (int) $parts[2]];

        if ($y < 1200 || $y > 1600 || $m < 1 || $m > 12 || $d < 1 || $d > 31) {
            return null;
        }

        try {
            return (new Jalalian($y, $m, $d))->toCarbon()->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** تولید کد یکتا برای قلم جدید */
    private function generateCode(string $category): string
    {
        $prefix = $category === TariffTitle::CATEGORY_WASTE ? 'W' : 'B';

        $last = TariffTitle::where('code', 'like', $prefix.'%')
            ->pluck('code')
            ->map(fn ($c) => (int) substr($c, 1))
            ->max() ?? 0;

        $n = $last + 1;
        while (TariffTitle::where('code', $prefix.str_pad((string) $n, 2, '0', STR_PAD_LEFT))->exists()) {
            $n++;
        }

        return $prefix.str_pad((string) $n, 2, '0', STR_PAD_LEFT);
    }

    /** عکس لحظه‌ای از مقادیر برای لاگ */
    private function snapshot(TariffTitle $t): array
    {
        return [
            'name_fa'        => $t->name_fa,
            'category'       => $t->category,
            'subcategory'    => $t->subcategory,
            'unit'           => $t->unit,
            'base_amount'    => (int) $t->base_amount,
            'description'    => $t->description,
            'is_active'      => (bool) $t->is_active,
            'effective_from' => $t->effective_from?->toDateString(),
        ];
    }

    /** کدام تب مربوط به این قلم است */
    private function groupKeyFor(TariffTitle $t): string
    {
        if ($t->category === TariffTitle::CATEGORY_WASTE) {
            return 'waste';
        }

        return $t->subcategory === TariffTitle::SUB_RENOVATION ? 'renovation' : 'new_build';
    }
}
