<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionCouncilMember extends Model
{
    protected $table = 'section_council_members';

    protected $fillable = [
        'section_id', 'name', 'position', 'phone',
        'photo', 'bio', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}
