<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

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
        session(['active_tab' => 'create']);

        $val = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required',
            'phone' => 'required',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $user->assignRole($request->role); // asignar rol

            // crear meta dato phone que va guardado en la tabla user_meta
            $user->meta()->create([
                'meta_key' => 'phone',
                'meta_value' => $request->phone,
            ]);

            return redirect()->route('admin.users')->with(
                ['success' => 'User created successfully'],
                ['active_tab' => 'create']
            );

        } catch (\Exception $e) {
            \Log::error('Error creating user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except('password')
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Error al crear el usuario. Por favor, intenta nuevamente.'])
                ->withInput($request->except('password'));
        }

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
            ->addColumn('created_at_formatted', function ($user) {
                return Carbon::parse($user->created_at)->format('d-m-Y');
            })
            ->addColumn('remaining_days', function ($user) {
                $createdDate = Carbon::parse($user->created_at);
                $daysRemaining = $createdDate->diffForHumans($createdDate->copy()->addMonth(), true);

                return $daysRemaining;
            })
            ->addColumn('actions', function ($user) {
                return '
                    <a href="#" data-id="' . $user->id . '" class="user-edit-btn bg-blue-500 rounded hover:bg-blue-700 font-bold text-white py-2 px-5">Editar</a>
                    <a href="#" data-id="' . $user->id . '" class="user-delete-btn bg-red-500 rounded hover:bg-red-700 font-bold text-white py-2 px-5 ml-2">Eliminar</a>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    //metodo edit
    public function edit($id)
    {
        $user = User::with('meta')->find($id);
        $phoneMeta = $user->meta->where('meta_key', 'phone')->first();
        $role = $user->getRoleNames()->first();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $phoneMeta ? $phoneMeta->meta_value : null,
            'role' => $role,
        ]);
    }

    public function update($id)
    {
        session(['active_tab' => 'edit']);

        $val = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required',
            'phone' => 'required',
        ]);

        try {
            $user = User::find($id);
            $user->update([
                'name' => request()->name,
                'email' => request()->email,
            ]);

            $user->syncRoles([request()->role]); // sincronizar rol

            $phoneMeta = $user->meta->where('meta_key', 'phone')->first();
            if ($phoneMeta) {
                $phoneMeta->update(['meta_value' => request()->phone]);
            } else {
                $user->meta()->create([
                    'meta_key' => 'phone',
                    'meta_value' => request()->phone,
                ]);
            }

            return redirect()->route('admin.users')->with(
                ['success' => 'User updated successfully'],
                ['active_tab' => 'edit']
            );

        } catch (\Exception $e) {
            \Log::error('Error updating user', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => request()->except('password')
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Error al actualizar el usuario. Por favor, intenta nuevamente.'])
                ->withInput(request()->except('password'));
        }
    }
}
