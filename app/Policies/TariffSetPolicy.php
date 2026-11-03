<?php

namespace App\Policies;

use App\Models\TariffSet;
use App\Models\User;

class TariffSetPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->role === 'super_admin' ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'super_admin', 'section_admin', 'village_admin', 'fee_officer',
        ]);
    }

    public function view(User $user, TariffSet $set): bool
    {
        return match ($user->role) {
            'section_admin' => $set->village?->section_id === $user->section_id,
            'village_admin' => $set->village_id === $user->village_id,
            'fee_officer'   => true,
            default         => false,
        };
    }

    /**
     * آیا کاربر می‌تواند آیتم‌های این مجموعه را ویرایش کند؟
     */
    public function updateItems(User $user, TariffSet $set): bool
    {
        if (!$this->view($user, $set)) return false;

        if (in_array($user->role, ['super_admin', 'section_admin', 'village_admin'])) {
            return true;
        }

        if ($user->role === 'fee_officer') {
            return in_array($user->officer_type, ['technical', 'financial']);
        }

        return false;
    }

    /**
     * ⭐ چک نهایی — آیا این کارشناس می‌تواند این نوع تعرفه را تغییر دهد؟
     */
    public function canEditType(User $user, string $type): bool
    {
        if (in_array($user->role, ['super_admin', 'section_admin', 'village_admin'])) {
            return true;
        }

        if ($user->role === 'fee_officer') {
            return match ($user->officer_type) {
                'technical' => $type === 'levy',
                'financial' => $type === 'service_fee',
                default     => false,
            };
        }

        return false;
    }

    public function update(User $user, TariffSet $set): bool
    {
        return in_array($user->role, ['super_admin', 'section_admin'])
            && $this->view($user, $set);
    }

    /**
     * ساخت مجموعه تعرفه جدید.
     * قبلاً این متد وجود نداشت و Gate::authorize('create', TariffSet::class)
     * برای همه (به‌جز سوپرادمین که از before() رد می‌شد) خطای 403 می‌داد.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, [
            'super_admin', 'section_admin', 'village_admin',
        ], true);
    }

    /**
     * تغییر وضعیت در گردش کار تعرفه.
     * قبلاً وجود نداشت و همه‌ی مراحل (proposed → ... → published)
     * برای همه‌ی نقش‌ها 403 می‌شدند.
     */
    public function transition(User $user, TariffSet $set, string $to): bool
    {
        static $allowedTargets = [
            'proposed',
            'village_council_approved',
            'district_council_approved',
            'finalized',
            'published',
        ];

        if (!in_array($to, $allowedTargets, true)) {
            return false;
        }

        if (!in_array($user->role, [
            'super_admin', 'section_admin', 'village_admin',
        ], true)) {
            return false;
        }

        return $this->view($user, $set);
    }

    public function delete(User $user, TariffSet $set): bool
    {
        return $user->role === 'super_admin';
    }
}
