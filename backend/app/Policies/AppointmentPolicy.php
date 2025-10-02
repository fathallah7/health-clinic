<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->id === $appointment->patient_id;
    }
}
