<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            Log::info('LOGIN_ATTEMPT: User attempting to login', [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);

            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            if (Auth::attempt($validated)) {
                $request->session()->regenerate();

                Log::info('LOGIN_SUCCESS: User logged in successfully', [
                    'user_id' => Auth::id(),
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->role,
                    'name' => Auth::user()->name,
                    'ip_address' => $request->ip(),
                    'session_id' => $request->session()->getId(),
                    'timestamp' => now()
                ]);

                return redirect()->route('dashboard');
            }

            Log::warning('LOGIN_FAILED: Invalid credentials provided', [
                'email' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
                'reason' => 'Invalid email or password'
            ]);

            throw ValidationException::withMessages([
                'credentials' => 'sorry, Invalid email or password'
            ]);
        } catch (ValidationException $e) {
            Log::error('LOGIN_VALIDATION_ERROR: Validation failed during login', [
                'email' => $request->email,
                'errors' => $e->errors(),
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('LOGIN_SYSTEM_ERROR: Unexpected error during login process', [
                'email' => $request->email,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);
            throw $e;
        }
    }

    public function loginView()
    {
        try {
            if (Auth::check()) {
                Log::info('LOGIN_PAGE_REDIRECT: Authenticated user redirected from login page', [
                    'user_id' => Auth::id(),
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->role,
                    'redirected_to' => 'dashboard',
                    'timestamp' => now()
                ]);
                return redirect()->route('dashboard');
            }

            Log::info('LOGIN_PAGE_ACCESS: Guest user accessing login page', [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()
            ]);

            return view('auth.login');
        } catch (\Exception $e) {
            Log::error('LOGIN_VIEW_ERROR: Error occurred while accessing login page', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'timestamp' => now()
            ]);
            throw $e;
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();

            Log::info('LOGOUT_INITIATED: User logout process started', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'session_id' => $request->session()->getId(),
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);

            auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('LOGOUT_SUCCESS: User logged out successfully', [
                'user_email' => $user->email,
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);

            return redirect()->route('login-view');
        } catch (\Exception $e) {
            Log::error('LOGOUT_ERROR: Error occurred during logout process', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'ip_address' => $request->ip(),
                'timestamp' => now()
            ]);
            throw $e;
        }
    }
}