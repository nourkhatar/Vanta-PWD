<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Cryptographically secure salt
        $encSalt = bin2hex(random_bytes(32));

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'enc_salt' => $encSalt,
        ]);

        // Auto login
        Auth::login($user);

        // Derive and store encryption key
        $this->deriveAndStoreKey($request->password, $encSalt);

        return redirect()->route('dashboard');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $this->deriveAndStoreKey($request->password, $user->enc_salt);

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        // Destroy key
        Session::forget('encryption_key');
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function deriveAndStoreKey(string $password, string $salt)
    {
        // PBKDF2 Key Derivation
        // Algo: sha256, Password, Salt, Iterations: 1000, Length: 32 bytes (256 bits)
        $key = hash_pbkdf2('sha256', $password, $salt, 1000, 32, true);
        
        // Store base64 encoded key in session
        Session::put('encryption_key', base64_encode($key));
    }
}
