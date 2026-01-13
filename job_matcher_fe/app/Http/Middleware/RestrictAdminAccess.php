<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if ($user->role !== 'admin') {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Bạn không có quyền truy cập khu vực quản trị.');
        }


        return $next($request);
    }
}