<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.add');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => 'required|string',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->password = bcrypt($validated['password']);
        $user->save();

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string',
            'password' => 'nullable|string|confirmed|min:6',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function configureSuperior($id)
    {
        $user = User::findOrFail($id);
        $allUsers = User::where('id', '!=', $id)->get(); // Exclude the current user
        $currentSuperiors = $user->parent()->pluck('users.id')->toArray();

        return view('users.configure-superior', compact('user', 'allUsers', 'currentSuperiors'));
    }

    public function updateSuperior(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'superior_id' => 'nullable|exists:users,id'
        ]);

        // Sync the superior relationship (single superior)
        $superiorId = $validated['superior_id'] ?? null;
        $user->parent()->sync($superiorId ? [$superiorId] : []);

        return redirect()->route('users.index')->with('success', 'Konfigurasi atasan berhasil diperbarui.');
    }
}