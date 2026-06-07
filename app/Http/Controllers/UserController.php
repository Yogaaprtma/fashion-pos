<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('role')
            ->when($request->search, fn($q) => $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%'))
            ->when($request->role_id, fn($q) => $q->where('role_id', $request->role_id))
            ->latest()
            ->paginate(15)->withQueryString();

        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'pin' => 'nullable|string|min:4|max:6',
        ]);

        $user = User::create([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'pin' => $request->pin ? Hash::make($request->pin) : null,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        AuditLog::record('create_user', $user, [], ['name' => $user->name, 'email' => $user->email]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
            'pin' => 'nullable|string|min:4|max:6',
        ]);

        $data = $request->only(['name', 'email', 'role_id', 'phone']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        if ($request->filled('pin')) {
            $data['pin'] = Hash::make($request->pin);
        }

        $user->update($data);
        AuditLog::record('update_user', $user, [], $data);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus akun sendiri.']);
        }

        AuditLog::record('delete_user', $user, ['name' => $user->name, 'email' => $user->email], []);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
