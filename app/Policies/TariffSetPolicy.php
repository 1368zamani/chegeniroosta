<?php

namespace App\Policies;

use App\Models\TariffSet;
use App\Models\User;
use App\Models\Village;

/**
 * دسترسی‌ها:
 *  - super_admin: همه چیز
 *  - section_admin: فقط روستاهای بخش خودش
 *  - village_admin: فقط روستای خودش
 *  - fee_officer: فقط مشاهده
 */
class TariffSetPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->isSuperAdmin() || $u->isSectionAdmin() || $u->isVillageAdmin() || $u->isFeeOfficer();
    }

    public function view(User $u, TariffSet $set): bool
    {
        if ($u->isSuperAdmin()) return true;
        if ($u->isFeeOfficer()) return true;

        if ($u->isSectionAdmin()) {
            return $this->villageInSection($set->village_id, $u->section_id);
        }

        if ($u->isVillageAdmin()) {
            return (int) $u->village_id === (int) $set->village_id;
        }

        return false;
    }

    public function create(User $u): bool
    {
        return $u->isSuperAdmin() || $u->isSectionAdmin() || $u->isVillageAdmin();
    }

    /**
     * فقط draft قابل ویرایش است.
     */
    public function update(User $u, TariffSet $set): bool
    {
        if (!$this->view($u, $set)) return false;
        if ($u->isFeeOfficer()) return false;
        return !$set->isLocked();
    }

    /**
     * تغییر وضعیت به وضعیت بعدی.
     */
    public function transition(User $u, TariffSet $set, string $toStatus): bool
    {
        if ($set->isLocked()) return false;
        if (!$this->view($u, $set)) return false;

        // سوپرادمین همیشه
        if ($u->isSuperAdmin()) return true;

        // دهیار: draft → proposed
        if ($u->isVillageAdmin() && $toStatus === 'proposed') {
            return (int) $u->village_id === (int) $set->village_id;
        }

        // بخشدار: proposed → village_council_approved → district_council_approved
        if ($u->isSectionAdmin()) {
            if (!in_array($toStatus, ['village_council_approved', 'district_council_approved'], true)) {
                return false;
            }
            return $this->villageInSection($set->village_id, $u->section_id);
        }

        return false;
    }

    /**
     * حذف: فقط draft توسط سوپرادمین.
     */
    public function delete(User $u, TariffSet $set): bool
    {
        if (!$u->isSuperAdmin()) return false;
        return $set->status === 'draft';
    }

    private function villageInSection(int $villageId, ?int $sectionId): bool
    {
        if (!$sectionId) return false;
        return Village::where('id', $villageId)->where('section_id', $sectionId)->exists();
    }
}
