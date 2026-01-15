<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'slot_id',
        'patient_id',
        'status',
        'notes',
    ];

    public function slot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function patient()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
