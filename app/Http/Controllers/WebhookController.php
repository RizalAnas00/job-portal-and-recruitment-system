<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handlePayment(Request $request)
    {
        // TODO: THIS ONE
        // Log webhook received
        Log::info('Webhook received', $request->all());

        // Verify webhook signature
        $signature = $request->header('X-Webhook-Signature');
        $webhookSecret = config('services.payment.webhook_secret');

        // Get payload and calculate expected signature
        $payload = $request->all();
        $expectedSignature = hash_hmac('sha256', json_encode($payload), $webhookSecret);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::warning('Invalid webhook signature', [
                'expected' => $expectedSignature,
                'received' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Process webhook
        $event = $request->input('event');
        $data = $request->input('data');

        if ($event === 'payment.success') {
            $externalId = $data['external_id'];

            $paymentTransaction = PaymentTransaction::where('id', $externalId)->first();

            if (!$paymentTransaction) {
                Log::warning('Order not found', ['external_id' => $externalId]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Check if already processed (idempotency)
            if ($paymentTransaction->isSuccessful()) {
                Log::info('Payment already processed', ['id' => $paymentTransaction->id]);
                return response()->json(['message' => 'Already processed'], 200);
            }

            // Update order status
            $paymentTransaction->update([
                'status' => 'success',
                'payment_date' => now(),
            ]);

            return response()->json(['message' => 'Webhook processed successfully'], 200);
        }

        if ($event === 'payment.failed') {
            $externalId = $data['external_id'];

            $paymentTransaction = PaymentTransaction::where('id', $externalId)->first();

            if ($paymentTransaction) {
                $paymentTransaction->update(['payment_status' => 'failed']);
                Log::info('Payment failed processed', ['id' => $paymentTransaction->id]);
            }

            return response()->json(['message' => 'Webhook processed'], 200);
        }

        if ($event === 'payment.expired') {
            $externalId = $data['external_id'];

            $paymentTransaction = PaymentTransaction::where('id', $externalId)->first();

            if ($paymentTransaction) {
                $paymentTransaction->update(['status' => 'failed']);
                Log::info('Payment expired processed', ['id' => $paymentTransaction->id]);
            }

            return response()->json(['message' => 'Webhook processed'], 200);
        }

        return response()->json(['message' => 'Event not handled'], 200);
    }
}
