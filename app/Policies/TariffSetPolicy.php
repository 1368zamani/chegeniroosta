<?php

namespace App\Policies;

use App\Models\TariffSet;
use App\Models\User;
use App\Models\Village;

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

    public function update(User $u, TariffSet $set): bool
    {
        if (!$this->view($u, $set)) return false;
        if ($u->isFeeOfficer()) return false;
        return !$set->isLocked();
    }

    public function transition(User $u, TariffSet $set, string $to): bool
    {
        if ($set->isLocked()) return false;
        if (!$this->view($u, $set)) return false;
        if ($u->isSuperAdmin()) return true;

        if ($u->isVillageAdmin() && $to === 'proposed') {
            return (int) $u->village_id === (int) $set->village_id;
        }
        if ($u->isSectionAdmin()) {
            if (!in_array($to, ['village_council_approved', 'district_council_approved'], true)) {
                return false;
            }
            return $this->villageInSection($set->village_id, $u->section_id);
        }
        return false;
    }

    public function delete(User $u, TariffSet $set): bool
    {
        return $u->isSuperAdmin() && $set->status === 'draft';
    }

    private function villageInSection(int $villageId, ?int $sectionId): bool
    {
        if (!$sectionId) return false;
        return Village::where('id', $villageId)->where('section_id', $sectionId)->exists();
    }
}
