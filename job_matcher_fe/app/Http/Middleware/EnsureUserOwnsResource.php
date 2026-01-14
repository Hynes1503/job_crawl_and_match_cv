<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserOwnsResource
{
    public function handle(Request $request, Closure $next, string $modelClass, string $routeKey = 'id'): Response
    {
        $model = $request->route($routeKey);

        if (!$model instanceof $modelClass) {
            $model = $modelClass::findOrFail($request->route($routeKey));
        }

        if ($model->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền truy cập tài nguyên này.');
        }

        return $next($request);
    }
}