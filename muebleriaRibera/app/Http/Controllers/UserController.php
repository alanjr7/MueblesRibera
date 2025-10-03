<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:superadmin');
    }

    // Listar usuarios
    public function index()
    {
        $users = User::with('rol')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    // Almacenar nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|string|email|max:150|unique:usuarios',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol_id' => 'required|exists:roles,id',
            'activo' => 'boolean'
        ]);

        User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
            'activo' => $request->activo ?? true
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    // Mostrar formulario de edición
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    // Actualizar usuario
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|string|email|max:150|unique:usuarios,email,' . $user->id,
            'password' => 'nullable|confirmed|min:8',
            'rol_id' => 'required|exists:roles,id',
            'activo' => 'boolean'
        ]);

        $data = [
            'nombre' => $request->nombre,
            'email' => $request->email,
            'rol_id' => $request->rol_id,
            'activo' => $request->activo ?? $user->activo
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    // Desactivar usuario
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update(['activo' => false]);

        return back()->with('success', 'Usuario desactivado exitosamente.');
    }

    // Activar usuario
    public function activar(User $user)
    {
        $user->update(['activo' => true]);

        return back()->with('success', 'Usuario activado exitosamente.');
    }
}