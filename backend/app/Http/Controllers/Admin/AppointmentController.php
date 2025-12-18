<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Traits\ApiResponse;
use App\Services\Appointment\AppointmentService;

class AppointmentController extends Controller
{
    use ApiResponse;

    public function __construct(protected AppointmentService $appointmentService){}

    // Appointments list with patient details
    public function index()
    {
        return $this->success(
            AppointmentResource::collection($this->appointmentService->getAppointmentsForAdmin()),
            'Appointments List',
            200);
    }

    // Delete User's Appointment (also free up the time slot)
    public function destroy(Appointment $appointment)
    {
        $this->appointmentService->cancelAppointmentForAdmin($appointment);
        return $this->success(null, 'Appointment deleted successfully', 200);
    }
}
