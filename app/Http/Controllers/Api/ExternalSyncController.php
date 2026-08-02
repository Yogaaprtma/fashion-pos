<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiIntegration;
use App\Models\ApiSyncLog;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExternalSyncController extends Controller
{
    /**
     * External API: Get live stock levels by SKU array or all products
     */
    public function getStock(Request $request)
    {
        $integration = $this->authenticateApiKey($request);
        if (!$integration) {
            return response()->json(['success' => false, 'message' => 'Unauthorized API Key'], 401);
        }

        $skus = $request->input('skus', []);
        $variants = ProductVariant::with('product')
            ->where('is_active', true)
            ->when(!empty($skus), fn($q) => $q->whereIn('sku', $skus))
            ->get();

        $data = $variants->map(fn($v) => [
            'sku'           => $v->sku,
            'product_name'  => $v->product->name ?? '',
            'variant_label' => $v->variant_label,
            'stock_qty'     => (int)$v->stock_qty,
            'selling_price' => (float)$v->selling_price,
        ]);

        $integration->update(['last_synced_at' => now()]);

        return response()->json([
            'success'   => true,
            'channel'   => $integration->channel_name,
            'timestamp' => now()->toIso8601String(),
            'data'      => $data,
        ]);
    }

    /**
     * External API Webhook: Push online order from Shopee/Tokopedia -> deduct POS stock
     */
    public function handleOnlineOrder(Request $request)
    {
        $integration = $this->authenticateApiKey($request);
        if (!$integration) {
            return response()->json(['success' => false, 'message' => 'Unauthorized API Key'], 401);
        }

        $request->validate([
            'order_ref' => 'required|string',
            'items'     => 'required|array|min:1',
            'items.*.sku' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request, $integration) {
                foreach ($request->items as $item) {
                    $variant = ProductVariant::where('sku', $item['sku'])->first();
                    if ($variant && $integration->auto_deduct_stock) {
                        $oldStock = $variant->stock_qty;
                        $variant->decrement('stock_qty', $item['qty']);

                        StockMovement::create([
                            'product_variant_id' => $variant->id,
                            'user_id'            => 1, // System automated
                            'type'               => 'out',
                            'quantity'           => $item['qty'],
                            'reference'          => "ONLINE-ORDER-{$integration->channel_name}-{$request->order_ref}",
                            'notes'              => "Potong stok otomatis dari pesanan e-commerce {$integration->channel_name}",
                        ]);
                    }
                }

                ApiSyncLog::create([
                    'integration_id'   => $integration->id,
                    'event_type'       => 'order_created',
                    'payload'          => json_encode($request->all()),
                    'status'           => 'success',
                    'response_message' => "Order {$request->order_ref} diproses. Stok terpotong.",
                ]);

                $integration->update(['last_synced_at' => now()]);
            });

            return response()->json([
                'success' => true,
                'message' => "Order {$request->order_ref} diproses. Stok toko fisik otomatis terpotong.",
            ]);
        } catch (\Exception $e) {
            ApiSyncLog::create([
                'integration_id'   => $integration->id,
                'event_type'       => 'order_created',
                'payload'          => json_encode($request->all()),
                'status'           => 'failed',
                'response_message' => $e->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function authenticateApiKey(Request $request): ?ApiIntegration
    {
        $apiKey = $request->header('X-API-KEY') ?? $request->query('api_key');
        if (!$apiKey) return null;

        return ApiIntegration::where('api_key', $apiKey)->where('is_active', true)->first();
    }
}
