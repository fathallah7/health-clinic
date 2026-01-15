<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'appointment_id',
        'stripe_session_id',
        'amount',
        'currency',
        'status',
        'stripe_response'
     ];

    // Relationships
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    // Status Checkers
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isSucceeded()
    {
        return $this->status === 'succeeded';
    }

    public function isFailed()
    {
        return in_array($this->status, ['failed', 'canceled']);
    }
}
