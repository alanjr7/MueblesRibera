<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BitacoraLogin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Mostrar formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Procesar login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        // Registrar intento de login en bitácora
        BitacoraLogin::create([
            'usuario_id' => $user ? $user->id : null,
            'ip_address' => $request->ip(),
            'exito' => false
        ]);

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        if (!$user->activo) {
            throw ValidationException::withMessages([
                'email' => ['Su cuenta está desactivada. Contacte al administrador.'],
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        // Actualizar bitácora como exitoso
        if ($user) {
            BitacoraLogin::where('usuario_id', $user->id)
                ->latest()
                ->first()
                ->update(['exito' => true]);
        }

        $request->session()->regenerate();

        // Redireccionar según rol
        return $this->redirectToDashboard($user);
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Redireccionar según rol
    private function redirectToDashboard(User $user)
    {
        switch ($user->rol->nombre) {
            case 'superadmin':
                return redirect()->route('admin.dashboard');
            case 'vendedor':
                return redirect()->route('vendedor.dashboard');
            case 'soldador':
                return redirect()->route('soldador.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }
}