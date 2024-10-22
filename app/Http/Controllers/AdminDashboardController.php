<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'super-admin');
        })->count();

        return view('admin.dashboard', compact('totalUsers'));
    }

    public function users()
    {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'super-admin');
        })->get();

        return view('admin.users', compact('users'));
    }

    // metodo store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // crear meta dato phone que va guardado en la tabla user_meta
        $user->meta()->create([
            'meta_key' => 'phone',
            'meta_value' => $request->phone,
        ]);

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    //metodo data que alimenta un ajax con los usuarios
    public function data()
    {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'super-admin');
        })->get();

        return datatables()->of($users)
            ->addColumn('role', function ($user) {
                return $user->getRoleNames()->implode(', '); // Imprimir los nombres de los roles
            })
            ->addColumn('actions', function ($user) {
                return '<a href="#" class="btn btn-sm btn-primary">Edit</a>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
