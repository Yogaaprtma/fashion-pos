<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\StoreSetting;

class QrisController extends Controller
{
    private function getHeaders()
    {
        $serverKey = env('MIDTRANS_SERVER_KEY', StoreSetting::get('midtrans_server_key'));
        if (!$serverKey) {
            // Throw exception or return fallback handled by env
            $serverKey = 'PLACEHOLDER_KEY_PLEASE_SET_IN_ENV';
        }
        $auth = base64_encode($serverKey . ':');
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . $auth,
        ];
    }

    public function generate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $orderId = 'TRX-' . time() . '-' . rand(1000, 9999);
        $amount = (int) $request->amount;

        $payload = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'qris' => [
                'acquirer' => 'gopay'
            ]
        ];

        $isProduction = env('MIDTRANS_IS_PRODUCTION', StoreSetting::get('midtrans_is_production', '0')) === '1';
        $baseUrl = $isProduction ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';

        try {
            $response = Http::withHeaders($this->getHeaders())->post($baseUrl . '/v2/charge', $payload);
            
            if ($response->successful()) {
                $data = $response->json();
                $qrAction = collect($data['actions'] ?? [])->firstWhere('name', 'generate-qr-code');
                $qrUrl = $qrAction ? $qrAction['url'] : null;

                if ($qrUrl) {
                    return response()->json([
                        'success' => true,
                        'order_id' => $orderId,
                        'qr_url' => $qrUrl,
                        'qr_image_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrUrl)
                    ]);
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi server Midtrans. ' . ($response->json()['status_message'] ?? '')
            ], 400);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function check($orderId)
    {
        $isProduction = env('MIDTRANS_IS_PRODUCTION', StoreSetting::get('midtrans_is_production', '0')) === '1';
        $baseUrl = $isProduction ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';

        try {
            $response = Http::withHeaders($this->getHeaders())->get($baseUrl . '/v2/' . $orderId . '/status');
            
            if ($response->successful()) {
                $data = $response->json();
                $status = $data['transaction_status'] ?? '';
                
                if ($status === 'settlement' || $status === 'capture') {
                    return response()->json([
                        'success' => true,
                        'settled' => true,
                        'message' => 'Pembayaran lunas!'
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'settled' => false,
                    'status' => $status,
                    'message' => 'Menunggu pembayaran... Status saat ini: ' . $status
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek status ke Midtrans.'
            ], 400);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
