<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'updated_at',
        'payload',
    ];

    protected $casts = [
        'id' => 'integer',
        'updated_at' => 'datetime',
        'payload' => 'array',
    ];
}
