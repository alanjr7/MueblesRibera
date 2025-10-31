<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BitacoraLogin;
use App\Models\BitacoraAccion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        // Registrar intento de login en bitácora (siempre)
        BitacoraLogin::create([
            'usuario_id' => $user ? $user->id : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'exito' => false,
            'accion' => 'login_attempt'
        ]);

        // Verificar si el usuario existe
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Verificar contraseña
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Verificar si el usuario está activo
        if (!$user->activo) {
            BitacoraLogin::create([
                'usuario_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'exito' => false,
                'accion' => 'login_failed_inactive'
            ]);

            throw ValidationException::withMessages([
                'email' => ['Su cuenta está desactivada. Contacte al administrador.'],
            ]);
        }

        // Si todo está bien, iniciar sesión
        Auth::login($user, $request->boolean('remember'));

        // Registrar login exitoso en bitácora de logins
        BitacoraLogin::create([
            'usuario_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'exito' => true,
            'accion' => 'login_success'
        ]);

        // Registrar en bitácora de acciones
        BitacoraAccion::create([
            'usuario_id' => $user->id,
            'accion' => 'login',
            'modelo' => User::class,
            'modelo_id' => $user->id,
            'descripcion' => "Inició sesión en el sistema",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'metodo' => $request->method()
        ]);

        $request->session()->regenerate();

        return $this->redirectToDashboard($user);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            // Registrar logout en bitácora de logins
            BitacoraLogin::create([
                'usuario_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'exito' => true,
                'accion' => 'logout'
            ]);

            // Registrar en bitácora de acciones
            BitacoraAccion::create([
                'usuario_id' => $user->id,
                'accion' => 'logout',
                'modelo' => User::class,
                'modelo_id' => $user->id,
                'descripcion' => "Cerró sesión del sistema",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'metodo' => $request->method()
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

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

    // Verificar sesión activa
    public function checkSession(Request $request)
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::check() ? [
                'id' => Auth::id(),
                'name' => Auth::user()->nombre,
                'role' => Auth::user()->rol->nombre
            ] : null
        ]);
    }

    // Extender sesión
    public function extendSession(Request $request)
    {
        $request->session()->regenerate();
        return response()->json(['success' => true]);
    }
}