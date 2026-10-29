<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::orderBy('role')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')],
            'role' => 'required|in:'.implode(',', array_keys(User::ROLES)),
            'password' => 'required|string|min:6',
        ]);

        User::create($data);

        return back()->with('success', 'Akun pengguna baru berhasil ditambahkan.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Tidak dapat menghapus akun yang sedang Anda gunakan.']);
        }
        $user->delete();

        return back()->with('success', 'Akun pengguna dihapus.');
    }
}

