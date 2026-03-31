<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expenses';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'created_at',
        'payload',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'payload' => 'array',
    ];
}
