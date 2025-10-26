<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TimeSlotRequest;
use App\Http\Requests\Admin\TimeSlotUpdateRequest;
use App\Models\TimeSlot;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    use ApiResponse;

    // Time Slots list
    public function index()
    {
        $timeslots = TimeSlot::all();
        return $this->success($timeslots, 'Time Slots List', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TimeSlotRequest $request)
    {
        $validated = $request->validated();
        $timeslot = TimeSlot::create($validated);
        return $this->success($timeslot, 'Time Slot Created', 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TimeSlotUpdateRequest $request, TimeSlot $timeSlot)
    {
        $validated = $request->validated();
        $timeSlot->update($validated);
        return $this->success($timeSlot, 'Time Slot Updated', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeSlot $timeSlot)
    {
        $timeSlot->delete();
        return $this->success(null, 'Time Slot Deleted', 204);
    }
}
