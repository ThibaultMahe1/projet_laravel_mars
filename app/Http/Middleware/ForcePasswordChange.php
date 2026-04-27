<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->needs_password_change) {
            // Empêcher la boucle infinie si on est déjà sur les routes autorisées
            if (!$request->is('password/change') && !$request->routeIs('logout')) {
                return redirect()->route('password.change.notice');
            }
        }

        return $next($request);
    }
}
