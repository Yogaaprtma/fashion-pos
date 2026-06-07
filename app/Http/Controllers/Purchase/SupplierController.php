<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::withCount('purchaseOrders')
            ->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->paginate(20)->withQueryString();
        return view('purchase.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('purchase.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
        ]);
        Supplier::create($request->except('_token'));
        return redirect()->route('purchase.suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('purchase.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($request->except('_token', '_method'));
        return redirect()->route('purchase.suppliers.index')->with('success', 'Supplier diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('purchase.suppliers.index')->with('success', 'Supplier dihapus.');
    }
}
