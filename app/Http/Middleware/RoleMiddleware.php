<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        // Ambil peran-peran yang dipisahkan dengan "|" (pipe)
        $rolesArray = explode('|', $roles);

        // Cek apakah role pengguna ada dalam array peran yang diberikan
        if (!auth()->user() || !in_array(auth()->user()->role, $rolesArray)) {
            // Jika role tidak cocok, redirect atau tampilkan unauthorized
            return redirect('/'); // Bisa disesuaikan dengan halaman yang tepat
        }

        // Lanjutkan request jika role cocok
        return $next($request);
    }
}
