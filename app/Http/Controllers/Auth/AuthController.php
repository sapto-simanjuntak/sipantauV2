<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Email or password is incorrect.'])
                ->withInput();
        }

        $request->session()->regenerate();
        $user = auth()->user();

        // ✅ Route berdasarkan role
        return match (true) {
            $user->hasRole(['superadmin', 'admin']) => redirect()->route('dashboard'),
            $user->hasRole(['teknisi']) => redirect()->route('technician.tickets.index'),
            $user->hasRole(['user']) => redirect()->route('ticket.index'),
            default => tap(null, function () {
                auth()->logout();
                abort(403, 'Role tidak valid.');
            })
        };
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }
}
