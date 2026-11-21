<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class LevyFile extends Model
{
    use SoftDeletes;

    protected $table = 'levy_files';

    protected $fillable = [
        'village_id', 'jalali_year',
        'applicant_name', 'dehyari_name', 'dehyari_account_no',
        'iban', 'id_number', 'village_group', 'class_status',
        'construction_area', 'property_area', 'notes',
        'status', 'completion_percent', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'construction_area'  => 'decimal:2',
        'property_area'      => 'decimal:2',
        'completion_percent' => 'integer',
    ];

    /** روستای موجود در سامانه */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    /** ضرایب مرجع (M, O, P) مخصوص همان روستا */
    public function params(): HasOne
    {
        return $this->hasOne(LevyVillageParam::class, 'village_id', 'village_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(LevyFileLine::class)->orderBy('sort_order');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(LevyAuditLog::class)->orderByDesc('created_at');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // ─────────────────────────────────────────────────────────────
    // محاسبات خودکار — دقیقاً مطابق فرمول اکسل (منطق تغییر نکرده)
    // N = M * 0.14
    // S = Q * N * O
    // T = N * (Q یا R طبق استثناء) * P
    // U = MAX(S, T)
    // H = SUM(G4:G6)
    // ─────────────────────────────────────────────────────────────

    public function getFinalCoefficientAttribute(): int
    {
        return $this->params?->finalCoefficient() ?? 0;
    }

    public function getResidentialLevyAttribute(): int
    {
        $q = (float) ($this->construction_area ?? 0);
        return (int) round($q * $this->final_coefficient * (int) ($this->params?->construction_coefficient ?? 0));
    }

    public function getDensityAreaAttribute(): float
    {
        // استثناء اکسل (سلول T64): از مساحت ملک R بجای مساحت ساخت Q استفاده میشود
        if ($this->params?->density_area_source === 'R') {
            return (float) ($this->property_area ?? 0);
        }
        return (float) ($this->construction_area ?? 0);
    }

    public function getDensityLevyAttribute(): int
    {
        return (int) round($this->final_coefficient * $this->density_area * (int) ($this->params?->density_coefficient ?? 0));
    }

    public function getFinalLevyAttribute(): int
    {
        return (int) max($this->residential_levy, $this->density_levy);
    }

    public function getLinesTotalAttribute(): int
    {
        return (int) $this->lines->sum('amount');
    }

    public function requiredFields(): array
    {
        return [
            'applicant_name'     => 'نام متقاضی',
            'dehyari_name'       => 'نام دهیاری',
            'dehyari_account_no' => 'شماره حساب دهیاری',
            'iban'               => 'شماره شبا',
            'id_number'          => 'شماره شناسه',
            'village_group'      => 'گروه روستا',
            'class_status'       => 'وضعیت طبقه',
            'line_1'             => 'عوارض حصار کشی',
            'line_2'             => 'عوارض احداث غیرمسکونی',
            'line_3'             => 'عوارض تراکم مجاز',
            'construction_area'  => 'مساحت ساخت',
        ];
    }

    public function missingFields(): array
    {
        $missing = [];
        foreach ($this->requiredFields() as $key => $label) {
            if (str_starts_with($key, 'line_')) {
                $line = $this->lines->firstWhere('sort_order', (int) str_replace('line_', '', $key));
                if (!$line || (float) $line->amount <= 0) {
                    $missing[] = $label;
                }
                continue;
            }
            $value = $this->{$key};
            if ($value === null || $value === '') {
                $missing[] = $label;
            }
        }
        return $missing;
    }

    public function refreshCompletion(): void
    {
        $total = count($this->requiredFields());
        $missing = count($this->missingFields());
        $percent = $total > 0 ? (int) round((($total - $missing) / $total) * 100) : 0;

        $this->completion_percent = $percent;
        $this->status = match (true) {
            $missing === 0 => 'complete',
            $percent > 0   => 'incomplete',
            default        => 'draft',
        };
    }
}
