<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->paginate(20);
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|unique:customers',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_member' => 'boolean',
        ]);

        Customer::create($validated);

        return back()->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_member' => 'boolean',
        ]);

        $customer->update($validated);

        return back()->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', 'Data pelanggan berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $q = $request->q;
        $customers = Customer::where('name', 'like', "%$q%")
            ->orWhere('phone', 'like', "%$q%")
            ->limit(10)
            ->get();
            
        return response()->json($customers);
    }
}
