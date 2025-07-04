<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (Auth::check() && Auth::user()->role === 'admin') {
                Log::info('ADMIN_ACCESS_GRANTED: Admin user accessing protected route', [
                    'user_id' => Auth::id(),
                    'user_email' => Auth::user()->email,
                    'route' => $request->route()->getName(),
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'ip_address' => $request->ip(),
                    'timestamp' => now()
                ]);

                return $next($request);
            }

            Log::warning('ADMIN_ACCESS_DENIED: Non-admin user attempted to access protected route', [
                'user_id' => Auth::id(),
                'user_email' => Auth::check() ? Auth::user()->email : 'guest',
                'user_role' => Auth::check() ? Auth::user()->role : 'guest',
                'route' => $request->route()->getName(),
                'url' => $request->url(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);

            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        } catch (\Exception $e) {
            Log::error('ADMIN_MIDDLEWARE_ERROR: Error in admin middleware', [
                'user_id' => Auth::id(),
                'route' => $request->route() ? $request->route()->getName() : 'unknown',
                'url' => $request->url(),
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