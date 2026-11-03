<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CountyOfficial;
use App\Models\SectionCouncilMember;
use App\Models\SectionNews;
use App\Models\VillageAttraction;
use App\Models\VillageCouncilMember;
use App\Models\VillageCouncilResolution;
use App\Models\VillageEvent;
use App\Models\VillageFacility;
use App\Models\VillageNews;
use App\Models\VillageProject;
use Illuminate\Support\Facades\Auth;

/**
 * فعال / غیرفعال کردن هر نوع محتوا با یک مسیر مشترک.
 *
 * چرا یک کنترلر مشترک؟ چون قبلاً فقط بخش‌ها و روستاها دکمه‌ی
 * فعال/غیرفعال داشتند و ۱۰ نوع محتوای دیگر نداشتند. اینجا یک نگاشت
 * تعریف شده و افزودن نوع جدید فقط یک خط است.
 *
 * مسیر:  POST /admin/toggle/{type}/{id}   →   admin.toggle
 */
class ToggleController extends Controller
{
    /**
     * نوع در URL => [کلاس مدل، ستون وضعیت، برچسب فارسی]
     */
    private const TYPES = [
        'attraction'         => [VillageAttraction::class,       'is_active',    'جاذبه‌ی گردشگری'],
        'facility'           => [VillageFacility::class,         'is_active',    'امکانات روستا'],
        'project'            => [VillageProject::class,          'is_active',    'پروژه'],
        'event'              => [VillageEvent::class,            'is_active',    'رویداد'],
        'village-council'    => [VillageCouncilMember::class,    'is_active',    'عضو شورای روستا'],
        'village-news'       => [VillageNews::class,             'is_published', 'خبر روستا'],
        'village-resolution' => [VillageCouncilResolution::class,'is_public',    'مصوبه‌ی شورا'],
        'section-council'    => [SectionCouncilMember::class,    'is_active',    'عضو شورای بخش'],
        'section-news'       => [SectionNews::class,             'is_published', 'خبر بخش'],
        'official'           => [CountyOfficial::class,          'is_active',    'مقام شهرستان'],
    ];

    public function toggle(string $type, int $id)
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        [$modelClass, $column, $label] = self::TYPES[$type];

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = $modelClass::findOrFail($id);

        $this->authorizeToggle($type, $model);

        $model->{$column} = !((bool) $model->{$column});
        $model->save();

        $state = $model->{$column} ? 'فعال' : 'غیرفعال';

        return back()->with('success', "{$label} {$state} شد.");
    }

    /**
     * کنترل دسترسی — بر اساس مالکیت روستا یا بخش.
     * به‌صورت پیش‌فرض: ممنوع.
     */
    private function authorizeToggle(string $type, $model): void
    {
        $user = Auth::user();

        abort_unless($user !== null, 403);

        // مقامات شهرستان در سطح شهرستان‌اند، نه روستا → فقط سوپرادمین
        if ($type === 'official') {
            abort_unless($user->isSuperAdmin(), 403, 'فقط سوپرادمین می‌تواند مقامات را تغییر دهد.');

            return;
        }

        // محتوای روستایی
        $villageId = $model->village_id ?? null;
        if ($villageId !== null) {
            abort_unless($user->canManageVillage($villageId), 403, 'شما به این روستا دسترسی ندارید.');

            return;
        }

        // محتوای بخشی
        $sectionId = $model->section_id ?? null;
        if ($sectionId !== null) {
            abort_unless($user->canManageSection($sectionId), 403, 'شما به این بخش دسترسی ندارید.');

            return;
        }

        abort(403);
    }
}
