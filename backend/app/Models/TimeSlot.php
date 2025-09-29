<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $fillable = ['date', 'start_time', 'end_time', 'status' , 'availability_id'];

    public function availability()
    {
        return $this->belongsTo(DoctorAvailability::class);
    }
}
