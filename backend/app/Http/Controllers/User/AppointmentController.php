<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\TimeSlotResource;
use App\Mail\AppointmentConfirmed;
use App\Models\Appointment;
use App\Models\TimeSlot;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    use ApiResponse;

    // Get All Time Slots
    public function index()
    {
        $timeslots = TimeSlot::all();
        return $this->success(TimeSlotResource::collection($timeslots), 'Time Slots', 200);
    }

    // Book Appointment
    public function store(Request $request)
    {
        $validated = $request->validate([
            'slot_id' => 'required|exists:time_slots,id',
            'notes' => 'nullable|string',
        ]);

        $patient = $request->user();
        $slotId = $validated['slot_id'];

        $exsistingAppointment =
            Appointment::query()->where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exsistingAppointment) {
            return $this->error('You already have an active appointment', [], 409);
        }

        try {
            $appointment = DB::transaction(function () use ($patient, $slotId, $validated) {

                $timeSlot = TimeSlot::query()->where('id', $slotId)->lockForUpdate()->first();

                if (!$timeSlot || $timeSlot->status !== 'available') {
                    throw new \Exception('Slot not available or not found');
                }

                $newAppointment = Appointment::create([
                    'patient_id' => $patient->id,
                    'slot_id' => $timeSlot->id,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'confirmed',
                ]);

                $timeSlot->update(['status' => 'booked']);

                return $newAppointment;
            });
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), [], 422);
        }

        Mail::to($patient->email)->queue(new AppointmentConfirmed($appointment));

        return $this->success(new AppointmentResource($appointment), 'Appointment Created', 201);
    }

    // Cancel Appointment
    public function destroy(Appointment $appointment)
    {
        Gate::authorize('delete', $appointment);

        try {
            DB::transaction(function () use ($appointment) {
                $appointment->delete();
                TimeSlot::where('id', $appointment->slot_id)->update(['status' => 'available']);
            });
            return $this->success(null, 'Appointment Cancelled', 200);
        } catch (\Throwable $th) {
            return $this->error('An error occurred while cancelling the appointment.', [], 500);
        }
    }
}
