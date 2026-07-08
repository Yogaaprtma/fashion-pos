<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->paginate(15);
        return view('settings.branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:branches,code',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Branch::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'address' => $request->address,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('settings.branches.index')
            ->with('success', 'Cabang baru berhasil didaftarkan.');
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:branches,code,' . $branch->id,
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $branch->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'address' => $request->address,
            'phone' => $request->phone,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('settings.branches.index')
            ->with('success', 'Informasi cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('settings.branches.index')
            ->with('success', 'Cabang berhasil dihapus.');
    }
}
