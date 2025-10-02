<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UserTimeSlotController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $usertimeslots = Appointment::where('patient_id', $request->user()->id)
            ->with('slot')->get();

        return $this->success($usertimeslots, 'User Time Slots', 200);
    }
}
