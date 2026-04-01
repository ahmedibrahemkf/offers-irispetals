<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\SystemLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (session()->has('auth_user_id')) {
            $user = User::query()->find((int) session('auth_user_id'));
            if ($user && $user->is_active) {
                return redirect()->route($this->routeForRole($user->role));
            }
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identity' => 'required|string|max:120',
            'password' => 'required|string|min:6|max:100',
            'remember' => 'nullable|boolean',
        ], [
            'identity.required' => 'هذا الحقل مطلوب',
            'password.required' => 'هذا الحقل مطلوب',
        ]);

        $identity = trim($validated['identity']);
        $user = User::query()
            ->where('username', $identity)
            ->orWhere('phone', $identity)
            ->orWhere('email', $identity)
            ->first();

        if (! $user || ! $user->is_active || ! Hash::check($validated['password'], $user->password)) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors(['identity' => 'اسم المستخدم أو كلمة المرور غير صحيحة']);
        }

        $request->session()->put('auth_user_id', $user->id);
        if (! empty($validated['remember'])) {
            $request->session()->put('remember_30_days', true);
        }

        SystemLogger::log((int) $user->id, 'login', 'Successful login', 'User', (int) $user->id, $request);

        return redirect()->route($this->routeForRole($user->role));
    }

    public function logout(Request $request): RedirectResponse
    {
        $userId = (int) $request->session()->get('auth_user_id');
        if ($userId > 0) {
            SystemLogger::log($userId, 'logout', 'Logout', 'User', $userId, $request);
        }

        $request->session()->flush();

        return redirect()->route('admin.login');
    }

    public function showForgotPassword(): View
    {
        return view('admin.auth.forgot-password');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identity' => 'required|string|max:120',
        ]);

        $user = User::query()
            ->where('username', $validated['identity'])
            ->orWhere('phone', $validated['identity'])
            ->orWhere('email', $validated['identity'])
            ->first();

        if (! $user) {
            return back()->withErrors(['identity' => 'المستخدم غير موجود']);
        }

        $otp = (string) random_int(100000, 999999);
        $token = Str::random(40);
        $request->session()->put('reset_user_id', $user->id);
        $request->session()->put('reset_otp', $otp);
        $request->session()->put('reset_token', $token);
        $request->session()->put('reset_expires_at', now()->addMinutes(10)->timestamp);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email ?: ('user_'.$user->id.'@local.reset')],
            ['token' => Hash::make($otp), 'created_at' => now()]
        );

        return redirect()
            ->route('admin.password.otp')
            ->with('otp_hint', 'رمز التحقق الحالي (وضع تجريبي): '.$otp);
    }

    public function showOtp(): View|RedirectResponse
    {
        if (! session()->has('reset_user_id')) {
            return redirect()->route('admin.password.forgot');
        }

        return view('admin.auth.otp');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|string|min:6|max:6',
        ]);

        $expected = (string) session('reset_otp', '');
        $expiresAt = (int) session('reset_expires_at', 0);
        if ($expected === '' || time() > $expiresAt) {
            return back()->withErrors(['otp' => 'انتهت صلاحية الرمز']);
        }

        if ($request->input('otp') !== $expected) {
            return back()->withErrors(['otp' => 'رمز التحقق غير صحيح']);
        }

        return redirect()->route('admin.password.reset');
    }

    public function showResetPassword(): View|RedirectResponse
    {
        if (! session()->has('reset_user_id')) {
            return redirect()->route('admin.password.forgot');
        }

        return view('admin.auth.reset-password');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|string|min:8|max:100|confirmed',
        ]);

        $userId = (int) session('reset_user_id');
        $user = User::query()->find($userId);
        if (! $user) {
            return redirect()->route('admin.password.forgot');
        }

        $user->password = Hash::make((string) $request->input('password'));
        $user->save();

        $request->session()->forget([
            'reset_user_id',
            'reset_otp',
            'reset_token',
            'reset_expires_at',
        ]);

        return redirect()
            ->route('admin.login')
            ->with('status', 'تم تغيير كلمة المرور بنجاح');
    }

    private function routeForRole(string $role): string
    {
        return match ($role) {
            'staff' => 'staff.orders',
            'craftsman' => 'craftsman.tasks',
            'viewer' => 'admin.reports.index',
            default => 'admin.dashboard',
        };
    }
}

