<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/*
 * ⚠️ توجه: ستون‌های `officer_type`، `phone` و `is_active` قبلاً در این
 * فهرست نبودند. نتیجه‌اش این بود که:
 *   • `User::create([... 'officer_type' => 'financial'])` در
 *     AdminUserController — با اینکه اعتبارسنجی می‌شد — بی‌صدا دور
 *     ریخته می‌شد و کارشناس مالی بدون نوع ساخته می‌شد؛ در نتیجه
 *     `isFinancialOfficer()` همیشه false بود و آن کاربر هرگز نمی‌توانست
 *     رسید پرداخت را تأیید کند.
 *   • `User::create([... 'phone' => ...])` در TariffOfficerController
 *     همین‌طور دور ریخته می‌شد.
 */
#[Fillable([
    'name', 'username', 'email', 'password', 'role',
    'section_id', 'village_id',
    'officer_type', 'phone', 'position', 'is_active',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function section()
    {
        return $this->belongsTo(\App\Models\Section::class);
    }

    public function village()
    {
        return $this->belongsTo(\App\Models\Village::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isSectionAdmin(): bool
    {
        return $this->role === 'section_admin';
    }

    public function isVillageAdmin(): bool
    {
        return $this->role === 'village_admin';
    }

    public function canManageSection($sectionId): bool
    {
        if ($this->isSuperAdmin()) return true;

        if ($this->isSectionAdmin()) {
            return (int) $this->section_id === (int) $sectionId;
        }

        return false;
    }

    public function canManageVillage($village): bool
    {
        if ($this->isSuperAdmin()) return true;

        $villageModel = is_object($village) ? $village : \App\Models\Village::find($village);
        if (!$villageModel) return false;

        if ($this->isSectionAdmin()) {
            return (int) $this->section_id === (int) $villageModel->section_id;
        }

        if ($this->isVillageAdmin()) {
            return (int) $this->village_id === (int) $villageModel->id;
        }

        return false;
    }

    public function isFeeOfficer(): bool
    {
        return $this->role === 'fee_officer';
    }

    public function isSectionStaff(): bool
    {
        return in_array($this->role, ['section_admin', 'fee_officer'], true);
    }


    /** کارشناس مالی */
    public function isFinancialOfficer(): bool
    {
        return $this->role === 'fee_officer' && $this->officer_type === 'financial';
    }


    /** کارشناس فنی/عمرانی */
    public function isTechnicalOfficer(): bool
    {
        return $this->role === 'fee_officer' && $this->officer_type === 'technical';
    }


    /** برچسب فارسی نوع کارشناس */
    public function officerTypeLabel(): string
    {
        return match ($this->officer_type) {
            'financial' => 'کارشناس مالی',
            'technical' => 'کارشناس فنی و عمرانی',
            default     => 'کارشناس',
        };
    }

}
