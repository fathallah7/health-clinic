<?php

namespace App\Services\Appointment;

use App\Mail\AppointmentCanceledAdmin;
use App\Mail\AppointmentConfirmed;
use App\Models\Appointment;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class AppointmentService
{

    // Get all appointments with patient details
    public function getAppointmentsForAdmin()
    {
        return Appointment::with('patient')->get();
    }

    // Cancel an appointment and free up the time slot
    public function cancelAppointmentForAdmin(Appointment $appointment)
    {
        DB::transaction(function () use ($appointment) {
            $appointment->delete();
            if ($appointment->slot) {
                $appointment->slot->update(['status' => 'available']);
            }
        });
        Mail::to($appointment->patient->email)->send(new AppointmentCanceledAdmin($appointment));
    }

    // Book an appointment for a patient
    public function bookAppointment($patient, $slotId)
    {
        $exsistingAppointment =
            Appointment::query()->where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exsistingAppointment) {
            throw new \Exception('You already have an active appointment', 409);
        }

        return DB::transaction(function () use ($patient, $slotId) {

            $timeSlot = TimeSlot::query()->where('id', $slotId)->lockForUpdate()->first();

            if (!$timeSlot || $timeSlot->status !== 'available') {
                throw new \Exception('Slot not available or not found');
            }

            $newAppointment = Appointment::create([
                'patient_id' => $patient->id,
                'slot_id' => $timeSlot->id,
                'status' => 'confirmed',
            ]);

            $timeSlot->update(['status' => 'booked']);
            Mail::to($patient->email)->queue(new AppointmentConfirmed($newAppointment));

            return $newAppointment;
        });
    }

    // Cancel an appointment for a patient
    public function cancelAppointment($appointment)
    {
        Gate::authorize('delete', $appointment);
        DB::transaction(function () use ($appointment) {
            $appointment->delete();
            TimeSlot::where('id', $appointment->slot_id)->update(['status' => 'available']);
        });
    }
}
