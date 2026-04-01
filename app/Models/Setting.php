<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use SoftDeletes;

    protected $table = 'settings';

    protected $guarded = [];

    protected $casts = [
        'show_tax' => 'boolean',
        'tax_rate' => 'decimal:2',
        'order_required_fields' => 'array',
    ];
}
