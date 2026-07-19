<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Branch;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'branch', 'user']);

        // Filters
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('expense_category_id')) {
            $query->where('expense_category_id', $request->expense_category_id);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }

        $expenses = $query->latest('expense_date')->paginate(15);
        $categories = ExpenseCategory::all();
        $branches = Branch::where('is_active', true)->get();

        return view('expenses.index', compact('expenses', 'categories', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'branch_id' => 'nullable|exists:branches,id',
            'notes' => 'nullable|string|max:255',
        ]);

        Expense::create([
            'expense_category_id' => $request->expense_category_id,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'branch_id' => $request->branch_id,
            'notes' => $request->notes,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran operasional berhasil dicatat.');
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'branch_id' => 'nullable|exists:branches,id',
            'notes' => 'nullable|string|max:255',
        ]);

        $expense->update([
            'expense_category_id' => $request->expense_category_id,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'branch_id' => $request->branch_id,
            'notes' => $request->notes,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran operasional berhasil diperbarui.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')
            ->with('success', 'Catatan pengeluaran operasional berhasil dihapus.');
    }
}
