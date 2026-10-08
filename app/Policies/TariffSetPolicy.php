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

    public function delete(User $user, TariffSet $set): bool
    {
        return $user->role === 'super_admin';
    }
}
