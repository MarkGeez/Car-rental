<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:customer,vendor'],
        ]);

        User::create($data);

        return redirect()
            ->route('login')
            ->with('success', 'Your account has been created. Please log in.');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'email' => $data['email'],
            'password' => $data['password'],
            'status' => 'active',
        ], $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'These credentials do not match an active account.']);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->dashboardPath($request->user()));
    }

    public function me(Request $request): View
    {
        return view('user.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $request->user()->update($data);

        return redirect()->route('user.profile')->with('success', 'Your profile has been updated.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }

    private function dashboardPath(User $user): string
    {
        if ($user->role === 'vendor' && ! $user->vendor()->exists()) {
            return route('vendor.me');
        }

        return match ($user->role) {
            'vendor' => '/vendor/dashboard',
            'admin' => '/admin/dashboard',
            default => '/user/dashboard',
        };
    }
}
