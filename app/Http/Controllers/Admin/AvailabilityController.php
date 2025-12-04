<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AvailabilityRequest;
use App\Http\Requests\Admin\AvailabilityUpdateRequest;
use App\Http\Resources\AvailabilityResource;
use App\Models\DoctorAvailability;
use App\Services\Availability\AvailabilityService;
use App\Traits\ApiResponse;

class AvailabilityController extends Controller
{
    use ApiResponse;

    public function __construct(protected AvailabilityService $availabilityService) {}

    // Get All Availabilities
    public function index()
    {
        $availabilities = $this->availabilityService->getAllAvailabilities();
        return $this->success(AvailabilityResource::collection($availabilities), 'Availability List', 200);
    }

    // Create Availability
    public function store(AvailabilityRequest $request)
    {
        $availability = $this->availabilityService->createAvailability($request->validated());
        return $this->success(new AvailabilityResource($availability), 'Availability Created', 200);
    }

    // Update Availability
    public function update(AvailabilityUpdateRequest $request, DoctorAvailability $availability)
    {
        $availability = $this->availabilityService->updateAvailability($availability, $request->validated());
        return $this->success(new AvailabilityResource($availability), 'Availability Updated', 200);
    }

    // Delete Availability
    public function destroy(DoctorAvailability $availability)
    {
        $this->availabilityService->deleteAvailability($availability);
        return $this->success(null, 'Availability Deleted', 200);
    }
}
