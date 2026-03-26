<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureProfileIsSet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // ループ防止
        if ($request->routeIs('profile.edit', 'profile.update', 'verification.*', 'logout')) {
            return $next($request);
        }

        if ($user && empty($user->zip)) {
            return redirect()->route('profile.edit');
        }

        return $next($request);
    }
}

