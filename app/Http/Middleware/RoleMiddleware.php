<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->attributes->get('authUser');
        if (! $user) {
            abort(401);
        }

        $allowed = array_filter(array_map('trim', explode(',', $roles)));
        if (! in_array($user->role, $allowed, true)) {
            abort(403, 'ليس لديك صلاحية للوصول لهذه الصفحة.');
        }

        return $next($request);
    }
}
