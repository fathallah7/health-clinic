<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AvailabilityRequest;
use App\Http\Requests\Admin\AvailabilityUpdateRequest;
use App\Http\Resources\AvailabilityResource;
use App\Models\DoctorAvailability;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AvailabilityController extends Controller
{
    use ApiResponse;

    // Get All Availabilities
    public function index()
    {
        $query = DoctorAvailability::with('timeSlots')->latest()->get();
        return $this->success(AvailabilityResource::collection($query), 'Availability List', 200);
    }

    // Create Availability
    public function store(AvailabilityRequest $request)
    {
        $validated = $request->validated();

        $availability = DoctorAvailability::create($validated);
        $this->generateTimeSlots($availability);

        return $this->success(new AvailabilityResource($availability), 'Availability Created', 200);
    }

    // Update Availability
    public function update(AvailabilityUpdateRequest $request, DoctorAvailability $availability)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($availability, $validated) {
            $availability->update($validated);
            $availability->timeSlots()->delete();
            $this->generateTimeSlots($availability);
        });

        return $this->success(new AvailabilityResource($availability), 'Availability Updated', 200);
    }

    // Delete Availability
    public function destroy(DoctorAvailability $availability)
    {
        $availability->delete();
        return $this->success(null, 'Availability Deleted', 200);
    }

    // Helper: Time Slots 
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
