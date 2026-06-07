<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent', 'children'])
            ->whereNull('parent_id')
            ->withCount('children')
            ->get();
        return view('inventory.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:10',
        ]);

        Category::create([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(4),
            'icon' => $request->icon,
            'is_active' => true,
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $category->update($request->only(['name', 'icon']) + ['is_active' => $request->boolean('is_active')]);
        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus kategori yang memiliki sub-kategori.']);
        }
        $category->delete();
        return back()->with('success', 'Kategori dihapus.');
    }
}
