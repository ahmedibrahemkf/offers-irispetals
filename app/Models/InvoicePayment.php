<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoicePayment extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'datetime',
    ];
}
