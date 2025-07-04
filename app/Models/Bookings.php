<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bookings extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'driver_id',
        'purpose',
        'start_time',
        'end_time',
        'status',
        'rejection_reason'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    /**
     * Get the user that made the booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the vehicle that was booked
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicles::class, 'vehicle_id');
    }

    /**
     * Get the driver assigned to this booking
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get the approvals for this booking
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(Approvals::class, 'booking_id');
    }
}
