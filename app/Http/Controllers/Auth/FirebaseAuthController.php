<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Auth as FirebaseAuth;

class FirebaseAuthController extends Controller
{
    protected $auth;

    public function __construct(FirebaseAuth $auth)
    {
        $this->auth = $auth;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showDashboard()
    {
        return view('dashboard');
    }

    public function login(Request $request)
    {
        try {
            $signInResult = $this->auth->signInWithEmailAndPassword(
                $request->email,
                $request->password
            );
            
            session(['firebase_user_id' => $signInResult->data()['localId']]);
            \Log::info('User logged in', ['user_id' => session('firebase_user_id')]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'user' => $signInResult->data(),
                'redirect' => route('welcome')
            ]);
        } catch (\Exception $e) {
            \Log::error('Login error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function logout()
    {
        session()->forget('firebase_user_id');
        return redirect('/login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        try {
            $auth = app('firebase.auth');
            $auth->sendPasswordResetLink($request->email);
            
            return back()->with('status', 'Link reset password telah dikirim!');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Email tidak terdaftar di sistem kami.']);
        }
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        try {
            $auth = app('firebase.auth');
            $auth->confirmPasswordReset($request->token, $request->password);
            
            return redirect()->route('login')->with('status', 'Password berhasil direset!');
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau sudah kadaluarsa.']);
        }
    }
}