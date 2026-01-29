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

        if (auth()->attempt($credentials)) {

            $request->session()->regenerate();

            $user = auth()->user();

            if ($user->hasRole(['superadmin', 'admin'])) {
                return redirect()->route('dashboard');
            }

            if ($user->hasRole('user')) {
                return redirect()->route('ticket.index');
            }

            auth()->logout();
            abort(403, 'Role tidak valid.');
        }

        return back()
            ->withErrors(['email' => 'Email or password is incorrect.'])
            ->withInput();
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }
}
