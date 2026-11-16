<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionStaff extends Model
{
    protected $table = "section_staffs";

    protected $fillable = ['section_id', 'name', 'position', 'phone', 'sort_order'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
