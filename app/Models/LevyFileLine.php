<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevyFileLine extends Model
{
    protected $table = 'levy_file_lines';

    protected $fillable = ['levy_file_id', 'title', 'amount', 'sort_order'];

    protected $casts = ['amount' => 'integer', 'sort_order' => 'integer'];

    public function file(): BelongsTo
    {
        return $this->belongsTo(LevyFile::class, 'levy_file_id');
    }
}
