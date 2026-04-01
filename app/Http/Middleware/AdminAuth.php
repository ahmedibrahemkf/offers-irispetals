<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = (int) $request->session()->get('auth_user_id');
        if ($id <= 0) {
            return redirect()->route('admin.login');
        }

        $user = User::query()
            ->where('id', $id)
            ->where('is_active', true)
            ->first();

        if (! $user) {
            $request->session()->forget('auth_user_id');

            return redirect()->route('admin.login');
        }

        $request->attributes->set('authUser', $user);
        app()->instance('authUser', $user);
        view()->share('authUser', $user);

        return $next($request);
    }
}
