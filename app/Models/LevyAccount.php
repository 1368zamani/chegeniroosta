<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevyAccount extends Model
{
    protected $table = 'levy_accounts';

    protected $fillable = ['account_no', 'label', 'sort_order'];

    protected $casts = ['sort_order' => 'integer'];
}
