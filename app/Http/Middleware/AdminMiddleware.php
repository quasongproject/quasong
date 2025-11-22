<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('player');
        }

        if ($user->role !== 'admin') {
            // abort(403, 'Anda tidak memiliki akses ke halaman ini.');
            return redirect()->route('player')->with('error', 'Anda bukan admin.');
        }

        return $next($request);
    }
}
