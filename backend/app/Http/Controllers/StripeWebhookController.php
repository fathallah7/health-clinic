<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Appointment;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type == 'checkout.session.completed') {

            $session = $event->data->object;

            $appointmentId = $session->metadata->appointment_id ?? null;
            $stripeSessionId = $session->id;

            if ($appointmentId) {

                $appointment = Appointment::find($appointmentId);
                if ($appointment) {
                    $appointment->update(['status' => 'confirmed']);

                    $payment = Payment::where('appointment_id', $appointmentId)->first();
                    if ($payment) {
                        $payment->update([
                            'status' => 'paid',
                            'stripe_session_id' => $stripeSessionId
                        ]);
                    }

                    Log::info("Appointment {$appointmentId} confirmed via Webhook.");
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}
