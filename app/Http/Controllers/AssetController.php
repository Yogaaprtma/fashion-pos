<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetHistory;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $assets = Asset::with('assetCategory')
            ->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->condition, fn($q) => $q->where('condition', $request->condition))
            ->paginate(20)->withQueryString();

        $totalValue = Asset::where('condition', '!=', 'disposed')->sum('current_value');
        $categories = AssetCategory::withCount('assets')->get();

        return view('assets.index', compact('assets', 'totalValue', 'categories'));
    }

    public function create()
    {
        $categories = AssetCategory::all();
        return view('assets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'asset_category_id' => 'nullable|exists:asset_categories,id',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
            'condition' => 'required|in:good,fair,poor',
            'location' => 'nullable|string|max:200',
        ]);

        $asset = Asset::create([
            'asset_category_id' => $request->asset_category_id,
            'name' => $request->name,
            'asset_code' => 'AST-' . date('y') . '-' . str_pad(Asset::count() + 1, 4, '0', STR_PAD_LEFT),
            'purchase_date' => $request->purchase_date,
            'purchase_price' => $request->purchase_price,
            'current_value' => $request->current_value ?? $request->purchase_price,
            'depreciation_rate' => $request->depreciation_rate ?? 0,
            'condition' => $request->condition,
            'location' => $request->location,
            'notes' => $request->notes,
        ]);

        AssetHistory::create(['asset_id' => $asset->id, 'action' => 'Aset ditambahkan', 'user_id' => auth()->id()]);

        return redirect()->route('assets.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['assetCategory', 'histories.user']);
        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $categories = AssetCategory::all();
        return view('assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, Asset $asset)
    {
        $oldCondition = $asset->condition;
        $asset->update($request->only(['name', 'asset_category_id', 'current_value', 'condition', 'location', 'notes']));

        if ($oldCondition !== $request->condition) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => "Kondisi diubah dari {$oldCondition} ke {$request->condition}",
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('assets.index')->with('success', 'Aset diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Aset dihapus.');
    }
}
