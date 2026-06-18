<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat mengakses halaman Setting.');
        }

        $search = $request->search;
        $role = $request->role;
        $page = 5;

        $query = User::query();

        if (strlen($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name_user', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%")
                    ->orWhere('nik', 'like', "%$search%")
                    ->orWhere('office_branch', 'like', "%$search%")
                    ->orWhere('role', 'like', "%$search%")
                    ->orWhere('position', 'like', "%$search%");
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('id_user', 'desc')->paginate($page)->appends($request->query());

        Session::put('page_url', request()->fullUrl());

        $tariffs = \App\Models\DeliveryTariff::orderBy('min_km')->get();

        return view('setting', compact('users', 'tariffs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    if (auth()->user()->role !== 'superadmin') {
        abort(403, 'Hanya superadmin yang dapat mengelola user.');
    }

    $validatedData = $request->validate([
        'name_user' => 'required',
        'username' => 'required|unique:users,username',
        'nik' => 'nullable|unique:users,nik',
        'email' => 'nullable|email',
        'office_branch' => 'nullable',
        'role' => 'required',
        'position' => 'required',
        'password' => 'required|min:5'
    ], [
        'username.unique' => 'Username sudah digunakan, silakan pilih username lain.',
        'nik.unique' => 'NIK sudah terdaftar di sistem.',
        'name_user.required' => 'Nama wajib diisi.',
        'username.required' => 'Username wajib diisi.',
        'role.required' => 'Role wajib dipilih.',
        'position.required' => 'Posisi wajib dipilih.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 5 karakter.',
        'email.email' => 'Format email tidak valid.',
    ]);

    User::create($validatedData);

    return redirect('/setting')->with('success', 'User berhasil ditambahkan');
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat mengelola user.');
        }

        $validatedData = $request->validate([
            'name_user' => 'required',
            'username' => 'required|unique:users,username,' . $id . ',id_user',
            'nik' => 'required|unique:users,nik,' . $id . ',id_user',
            'email' => 'nullable|email',
            'office_branch' => 'nullable',
            'role' => 'required',
            'position' => 'required',
            'password' => 'nullable|min:5',
        ], [
            'username.unique' => 'Username sudah digunakan oleh user lain.',
            'nik.unique' => 'NIK sudah terdaftar oleh user lain.',
            'password.min' => 'Password minimal 5 karakter.',
        ]);

        // Jika password kosong, jangan update password
        if (empty($validatedData['password'])) {
            unset($validatedData['password']);
        } else {
            // Hash manual karena Query Builder tidak trigger Eloquent cast
            $validatedData['password'] = \Illuminate\Support\Facades\Hash::make($validatedData['password']);
        }

        User::where('id_user', $id)->update($validatedData);

        if (session('page_url')) {
            return redirect(session('page_url'))->with('success', 'User berhasil diperbarui');
        }

        return redirect('/setting')->with('success', 'User berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Hanya superadmin yang dapat mengelola user.');
        }

        User::where('id_user', $id)->delete();
        return redirect('/setting');
    }
}