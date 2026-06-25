<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\PromotionTarget;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::latest()->paginate(15);
        return view('inventory.promotions.index', compact('promotions'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        return view('inventory.promotions.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50|unique:promotions,code',
            'type' => 'required|in:discount_percent,discount_fixed,bogo,bundling',
            'value' => 'required_if:type,discount_percent,discount_fixed|nullable|numeric|min:0',
            'min_requirement_type' => 'required|in:none,min_spend,min_qty',
            'min_requirement_value' => 'required_if:min_requirement_type,min_spend,min_qty|nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'target_type' => 'required|in:all,category,product',
            'target_ids' => 'nullable|array',
        ]);

        $promotion = Promotion::create([
            'name' => $request->name,
            'code' => $request->code ? strtoupper($request->code) : null,
            'type' => $request->type,
            'value' => $request->value,
            'min_requirement_type' => $request->min_requirement_type,
            'min_requirement_value' => $request->min_requirement_value ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'usage_limit' => $request->usage_limit,
            'target_type' => $request->target_type,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->target_type !== 'all' && $request->target_ids) {
            foreach ($request->target_ids as $id) {
                PromotionTarget::create([
                    'promotion_id' => $promotion->id,
                    'target_id' => $id,
                ]);
            }
        }

        return redirect()->route('inventory.promotions.index')
            ->with('success', 'Promosi berhasil ditambahkan.');
    }

    public function edit(Promotion $promotion)
    {
        $promotion->load('targets');
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        $selectedTargets = $promotion->targets->pluck('target_id')->toArray();

        return view('inventory.promotions.edit', compact('promotion', 'products', 'categories', 'selectedTargets'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50|unique:promotions,code,' . $promotion->id,
            'type' => 'required|in:discount_percent,discount_fixed,bogo,bundling',
            'value' => 'required_if:type,discount_percent,discount_fixed|nullable|numeric|min:0',
            'min_requirement_type' => 'required|in:none,min_spend,min_qty',
            'min_requirement_value' => 'required_if:min_requirement_type,min_spend,min_qty|nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'target_type' => 'required|in:all,category,product',
            'target_ids' => 'nullable|array',
        ]);

        $promotion->update([
            'name' => $request->name,
            'code' => $request->code ? strtoupper($request->code) : null,
            'type' => $request->type,
            'value' => $request->value,
            'min_requirement_type' => $request->min_requirement_type,
            'min_requirement_value' => $request->min_requirement_value ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'usage_limit' => $request->usage_limit,
            'target_type' => $request->target_type,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        // Re-sync targets
        $promotion->targets()->delete();
        if ($request->target_type !== 'all' && $request->target_ids) {
            foreach ($request->target_ids as $id) {
                PromotionTarget::create([
                    'promotion_id' => $promotion->id,
                    'target_id' => $id,
                ]);
            }
        }

        return redirect()->route('inventory.promotions.index')
            ->with('success', 'Promosi berhasil diperbarui.');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->route('inventory.promotions.index')
            ->with('success', 'Promosi berhasil dihapus.');
    }

    // POS API Endpoint: Check Coupon Code
    public function checkCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $promotion = Promotion::where('code', strtoupper($request->code))
            ->where('is_active', true)
            ->first();

        if (!$promotion) {
            return response()->json(['success' => false, 'message' => 'Kode voucher tidak valid.'], 404);
        }

        if (!$promotion->isValid()) {
            return response()->json(['success' => false, 'message' => 'Voucher sudah kedaluwarsa atau batas kuota habis.'], 400);
        }

        return response()->json([
            'success' => true,
            'promotion' => [
                'id' => $promotion->id,
                'name' => $promotion->name,
                'type' => $promotion->type,
                'value' => (float)$promotion->value,
                'min_requirement_type' => $promotion->min_requirement_type,
                'min_requirement_value' => (float)$promotion->min_requirement_value,
                'target_type' => $promotion->target_type,
                'targets' => $promotion->targets->pluck('target_id')
            ]
        ]);
    }
}
