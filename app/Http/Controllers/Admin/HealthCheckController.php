<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class HealthCheckController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index()
    {
        $status = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'disk' => $this->checkDisk(),
            'payment_gateway' => $this->checkPaymentGateway(),
        ];

        return view('admin.monitoring.health', compact('status'));
    }

    private function checkDatabase()
    {
        try {
            $startTime = microtime(true);
            DB::connection()->getPdo();
            $endTime = microtime(true);
            return [
                'status' => 'ok',
                'message' => 'Connected',
                'latency' => round(($endTime - $startTime) * 1000, 2) . 'ms'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'latency' => 0
            ];
        }
    }

    private function checkCache()
    {
        try {
            $startTime = microtime(true);
            Cache::put('health_check', 'ok', 10);
            $value = Cache::get('health_check');
            $endTime = microtime(true);
            
            if ($value === 'ok') {
                return [
                    'status' => 'ok',
                    'message' => 'Operational',
                    'latency' => round(($endTime - $startTime) * 1000, 2) . 'ms'
                ];
            }
            throw new \Exception('Cache read/write failed');
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'latency' => 0
            ];
        }
    }

    private function checkDisk()
    {
        try {
            $free = disk_free_space(base_path());
            $total = disk_total_space(base_path());
            $used = $total - $free;
            $percentage = round(($used / $total) * 100, 2);

            return [
                'status' => $percentage > 90 ? 'warning' : 'ok',
                'free_gb' => round($free / 1024 / 1024 / 1024, 2) . ' GB',
                'total_gb' => round($total / 1024 / 1024 / 1024, 2) . ' GB',
                'percentage' => $percentage . '%'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    private function checkPaymentGateway()
    {
        $url = config('services.payment.base_url', env('PAYMENT_BASE_URL'));
        
        if (empty($url)) {
            return [
                'status' => 'warning',
                'message' => 'URL not configured',
                'latency' => 0
            ];
        }

        try {
            $startTime = microtime(true);
            // Just checking if we can reach the host, simplified check
            // Timeout set to 3 seconds to avoid hanging the page
            // withoutVerifying() added to bypass SSL local issues (cURL error 60)
            $response = Http::withoutVerifying()->timeout(3)->get($url);
            $endTime = microtime(true);

            return [
                'status' => $response->successful() || $response->status() === 401 || $response->status() === 403 ? 'ok' : 'error',
                'message' => 'Status: ' . $response->status(),
                'latency' => round(($endTime - $startTime) * 1000, 2) . 'ms'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Connection failed: ' . $e->getMessage(),
                'latency' => 0
            ];
        }
    }
}
