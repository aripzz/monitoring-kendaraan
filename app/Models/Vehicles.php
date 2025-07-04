<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicles extends Model
{
    protected $table = 'vehicles';

    protected $fillable = [
        'plate_number',
        'model',
        'type',
        'owner',
        'bbm',
        'next_service_date',
    ];

    protected $casts = [
        'bbm' => 'decimal:2',
        'next_service_date' => 'date',
    ];
}
