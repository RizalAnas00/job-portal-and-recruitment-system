<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\CompanySubscription;
use App\Actions\CheckActiveSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    private $checkActiveSubscription;

    public function __construct(CheckActiveSubscription $checkActiveSubscription)
    {
        $this->checkActiveSubscription = $checkActiveSubscription;
    }

    public function handlePayment(Request $request)
    {
        Log::info('Webhook received', $request->all());

        // Verify webhook signature
        $signature = $request->header('X-Webhook-Signature');
        $webhookSecret = config('services.payment.webhook_secret');
        $payload = $request->all();
        $expectedSignature = hash_hmac('sha256', json_encode($payload), $webhookSecret);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('Invalid webhook signature', [
                'expected' => $expectedSignature,
                'received' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        if ($event === 'payment.success') {
            $externalId = $data['external_id'];
            $paymentTransaction = PaymentTransaction::find($externalId);

            if (!$paymentTransaction) {
                Log::warning('Payment transaction not found', ['external_id' => $externalId]);
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            if ($paymentTransaction->isSuccessful()) {
                Log::info('Payment already processed', ['id' => $paymentTransaction->id]);
                return response()->json(['message' => 'Already processed'], 200);
            }

            $paymentTransaction->update([
                'status' => 'success',
                'payment_date' => now(),
            ]);

            $companySubscription = $paymentTransaction->companySubscription ?? null;

            if ($companySubscription) {
                $company = $companySubscription->company;
                
                $activeSubscription = $this->checkActiveSubscription->__invoke($company);

                if ($activeSubscription && $activeSubscription->id !== $companySubscription->id) {
                    $activeSubscription->update(['status' => 'canceled']);
                    Log::info('Previous subscription canceled', ['id' => $activeSubscription->id]);
                }

                $companySubscription->update(['status' => 'active']);

                if ($companySubscription->plan->allow_verified_badge == true) {
                    $company->update(['is_verified' => true]);
                }
                
                $company->update(['is_verified' => true]);
                Log::info('New subscription activated', ['id' => $companySubscription->id]);
            }

            return response()->json(['message' => 'Webhook processed successfully'], 200);
        }

        if ($event === 'payment.failed') {
            $externalId = $data['external_id'];
            $paymentTransaction = PaymentTransaction::find($externalId);

            if ($paymentTransaction) {
                $paymentTransaction->update(['status' => 'failed']);
                Log::info('Payment failed processed', ['id' => $paymentTransaction->id]);
            }

            return response()->json(['message' => 'Webhook processed'], 200);
        }

        if ($event === 'payment.expired') {
            $externalId = $data['external_id'];
            $paymentTransaction = PaymentTransaction::find($externalId);

            if ($paymentTransaction) {
                $paymentTransaction->update(['status' => 'failed']);
                Log::info('Payment expired processed', ['id' => $paymentTransaction->id]);
            }

            return response()->json(['message' => 'Webhook processed'], 200);
        }

        return response()->json(['message' => 'Event not handled'], 200);
    }
}
