<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CrudPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $action): Response|RedirectResponse
    {
        $user = $request->attributes->get('authUser');
        if (! $user) {
            abort(401);
        }

        $allowed = match ($action) {
            'create' => $user->canCreateRecords(),
            'update' => $user->canUpdateRecords(),
            'delete' => $user->canDeleteRecords(),
            default => true,
        };

        if ($allowed) {
            return $next($request);
        }

        $message = match ($action) {
            'create' => 'ليس لديك صلاحية الإضافة.',
            'update' => 'ليس لديك صلاحية التعديل.',
            'delete' => 'ليس لديك صلاحية الحذف.',
            default => 'ليس لديك الصلاحية المطلوبة.',
        };

        if ($request->expectsJson()) {
            abort(403, $message);
        }

        if (! $request->isMethod('GET')) {
            return redirect()->back()->withErrors(['permission' => $message]);
        }

        abort(403, $message);
    }
}

