<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (! User::query()->exists()) {
            return redirect()->route('register');
        }

        return view('auth.login');
    }

    public function register(): View|RedirectResponse
    {
        return User::query()->exists()
            ? redirect()->route('login')
            : view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $credentials['is_active'] = true;

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function storeFirstAdmin(Request $request, ActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($validated, $activityLogger): User {
            if (User::query()->lockForUpdate()->exists()) {
                abort(409);
            }

            app(AccessControlSeeder::class)->run();
            $user = User::query()->create($validated + ['is_active' => true, 'locale' => app()->getLocale()]);
            $user->roles()->attach(\App\Models\Role::query()->where('slug', 'super-admin')->value('id'));
            $activityLogger->log('user.bootstrap', $user);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
