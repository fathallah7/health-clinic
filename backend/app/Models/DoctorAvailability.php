<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorAvailability extends Model
{
    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'slot_duration',
    ];

    public function timeslots()
    {
        return $this->hasMany(TimeSlot::class, foreignKey: 'availability_id');
    }
}
