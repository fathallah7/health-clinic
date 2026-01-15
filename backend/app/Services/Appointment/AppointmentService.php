<?php

namespace App\Services\Appointment;

use App\Mail\AppointmentCanceledAdmin;
use App\Mail\AppointmentConfirmed;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\TimeSlot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;

class AppointmentService
{
    protected $stripe;
    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

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
    public function bookAppointment($patient, $slotId): array
    {
        $exsistingAppointment =
            Appointment::query()->where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($exsistingAppointment) {
            throw new \Exception('You already have an active appointment', 409);
        }

        $appointment = DB::transaction(function () use ($patient, $slotId) {

            $timeSlot = TimeSlot::query()->where('id', $slotId)->lockForUpdate()->first();

            if (!$timeSlot || $timeSlot->status !== 'available') {
                throw new \Exception('Slot not available or not found');
            }


            $newAppointment = Appointment::create([
                'patient_id' => $patient->id,
                'slot_id' => $timeSlot->id,
                'status' => 'pending',
            ]);

            $timeSlot->update(['status' => 'booked']);

            return $newAppointment;
        });

        try {

            $checkout_session = $this->stripe->checkout->sessions->create([
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'Your Time Slot Reservation',
                            ],
                            'unit_amount' => 2500, // $25.00
                        ],
                        'quantity' => 1,
                    ]
                ],
                'metadata' => [
                    'appointment_id' => $appointment->id,
                    'patient_id' => $patient->id,
                ],
                'customer_creation' => 'always',
                'mode' => 'payment',
                'success_url' => 'https://abdullah.top',
                'cancel_url' => 'https://abdullah.top',
            ]);

            $payment = Payment::create([
                'appointment_id' => $appointment->id,
                'stripe_session_id' => $checkout_session->id,
                'amount' => 2500,
                'currency' => 'usd',
                'status' => 'pending',
            ]);

            return [
                'appointment' => $appointment,
                'payment' => $payment,
                'checkout_url' => $checkout_session->url,
            ];
        } catch (\Throwable $th) {
            $appointment->update(['status' => 'canceled']);
            $appointment->timeSlot->update(['status' => 'available']);
            throw new \Exception('Payment gateway error: ' . $th->getMessage());
        }
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
