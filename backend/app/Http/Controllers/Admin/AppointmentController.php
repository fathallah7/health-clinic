<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Traits\ApiResponse;

class AppointmentController extends Controller
{
    use ApiResponse;

    // Appointments list with patient details
    public function index()
    {
        $appointments = Appointment::with('patient')->get();
        return $this->success($appointments, 'Appointments List', 200);
    }

    // Delete User's Appointment (also free up the time slot)
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        $appointment->slot()->update(['status' => 'available']);
        return $this->success(null, 'Appointment deleted successfully', 200);
    }
}
