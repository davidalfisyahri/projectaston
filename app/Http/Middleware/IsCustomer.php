<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsCustomer
{
    /**
     * Hanya user dengan role 'customer' yang boleh akses.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'customer') {
            abort(403, 'Halaman ini hanya untuk customer.');
        }

        return $next($request);
    }
}
