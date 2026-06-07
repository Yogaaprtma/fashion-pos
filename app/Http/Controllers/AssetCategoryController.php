<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::withCount('assets')->paginate(20);
        return view('assets.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100', 'description' => 'nullable|string']);
        AssetCategory::create($request->only(['name', 'description']));
        return back()->with('success', 'Kategori aset ditambahkan.');
    }

    public function update(Request $request, AssetCategory $category)
    {
        $category->update($request->only(['name', 'description']));
        return back()->with('success', 'Kategori aset diperbarui.');
    }

    public function destroy(AssetCategory $category)
    {
        if ($category->assets()->exists()) {
            return back()->withErrors(['error' => 'Tidak bisa hapus kategori yang masih memiliki aset.']);
        }
        $category->delete();
        return back()->with('success', 'Kategori aset dihapus.');
    }
}
