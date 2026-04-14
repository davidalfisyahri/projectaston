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
        $search = $request->search;
        $page = 5;

        if (strlen($search)) {
            $users = User::where('name_user', 'like', "%$search%")
                ->orWhere('username', 'like', "%$search%")
                ->orWhere('nik', 'like', "%$search%")
                ->orWhere('office_branch', 'like', "%$search%")
                ->orWhere('role', 'like', "%$search%")
                ->orWhere('position', 'like', "%$search%")
                ->paginate($page);

            Session::put('page_url', request()->fullUrl());
        } else {
            $users = User::orderBy('id_user', 'desc')->paginate($page);

            Session::put('page_url', request()->fullUrl());
        }

        return view('setting', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'name_user' => 'required',
        'username' => 'required|unique:users,username',
        'nik' => 'nullable|unique:users,nik',
        'email' => 'nullable|email',
        'office_branch' => 'nullable',
        'role' => 'required',
        'position' => 'required',
        'password' => 'required|min:5'
    ]);

    User::create($validatedData);

    return redirect('/setting')->with('success', 'User berhasil ditambahkan');
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name_user' => 'required',
            'username' => 'required',
            'nik' => 'required',
            'email' => 'nullable|email',
            'office_branch' => 'nullable',
            'role' => 'required',
            'position' => 'required',
        ]);

        User::where('id_user', $id)->update($validatedData);

        if (session('page_url')) {
            return redirect(session('page_url'));
        }

        return redirect('/setting');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        User::where('id_user', $id)->delete();
        return redirect('/setting');
    }
}