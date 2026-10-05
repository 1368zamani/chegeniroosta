<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TariffParameter extends Model
{
    protected $table = 'tariff_parameters';
    protected $fillable = ['tariff_set_id', 'key', 'value', 'unit', 'description'];

    public function set(): BelongsTo { return $this->belongsTo(TariffSet::class, 'tariff_set_id'); }

    public function typedValue(): int|float|string|null
    {
        $v = $this->value;
        if ($v === null) return null;
        if (is_numeric($v)) {
            return str_contains((string) $v, '.') ? (float) $v : (int) $v;
        }
        return $v;
    }

    public static function getValue(TariffSet $set, string $key, $default = null)
    {
        $p = self::where('tariff_set_id', $set->id)->where('key', $key)->first();
        return $p ? $p->typedValue() : $default;
    }

    public static function put(TariffSet $set, string $key, $value, ?string $unit = null): self
    {
        return self::updateOrCreate(
            ['tariff_set_id' => $set->id, 'key' => $key],
            ['value' => $value, 'unit' => $unit]
        );
    }
}
