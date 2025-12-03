<?php

namespace App\Services\Availability;

use App\Models\DoctorAvailability;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AvailabilityService
{
    // Get all availabilities with time slots
    public function getAllAvailabilities()
    {
        return DoctorAvailability::with('timeSlots')->latest()->get();
    }

    // Create availability with time slots
    public function createAvailability(array $data)
    {
        return DB::transaction(function () use ($data) {
            $availability = DoctorAvailability::create($data);
            $this->generateTimeSlots($availability);

            return $availability->load('timeSlots');
        });
    }

    // Update availability and regenerate time slots
    public function updateAvailability(DoctorAvailability $availability, array $data)
    {
        return DB::transaction(function () use ($availability, $data) {
            $availability->update($data);
            $availability->timeSlots()->delete();
            $this->generateTimeSlots($availability);

            return $availability->fresh('timeSlots');
        });
    }

    // Delete availability
    public function deleteAvailability(DoctorAvailability $availability)
    {
        DB::transaction(function () use ($availability) {
            $availability->delete();
        });
    }

    // Generate time slots based on availability
    private function generateTimeSlots(DoctorAvailability $availability)
    {
        $start = Carbon::parse($availability->start_time);
        $end = Carbon::parse($availability->end_time);
        $duration = $availability->slot_duration;

        while ($start->copy()->addMinutes($duration) <= $end) {
            TimeSlot::create([
                'availability_id' => $availability->id,
                'date' => $availability->date,
                'start_time' => $start->copy()->format('H:i'),
                'end_time' => $start->copy()->addMinutes($duration)->format('H:i'),
                'status' => 'available'
            ]);
            $start->addMinutes($duration);
        }
    }
}
