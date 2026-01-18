<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\TimeSlotResource;
use App\Models\Appointment;
use App\Models\TimeSlot;
use App\Services\Appointment\AppointmentService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use ApiResponse;

    public function __construct(protected AppointmentService $appointmentService) {}

    // Get All Time Slots
    public function index()
    {
        $timeslots = TimeSlot::all();
        return $this->success(TimeSlotResource::collection($timeslots), 'Time Slots', 200);
    }

    // Book Appointment
    public function store(Request $request)
    {
        try {
            $result = $this->appointmentService->bookAppointment($request->user(), $request->input('slot_id'));
            return $this->success([
                'appointment' => new AppointmentResource($result['appointment']),
                'checkout_url' => $result['checkout_url'],
            ], 'Appointment Created', 201);
        } catch (\Exception $e) {
            $code = $e->getCode() ?: 422;
            return $this->error($e->getMessage(), [], $code);
        }
    }

    // Cancel Appointment
    public function destroy(Appointment $appointment)
    {
        $this->appointmentService->cancelAppointment($appointment);
        return $this->success(null, 'Appointment Cancelled', 200);
    }
}
