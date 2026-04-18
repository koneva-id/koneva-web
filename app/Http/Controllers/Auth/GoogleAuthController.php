<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AltchaCaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! $this->hasGoogleConfig()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google login is not configured yet. Please contact admin.',
            ]);
        }

        return $this->googleDriver()->redirect();
    }

    public function callback(): RedirectResponse
    {
        if (! $this->hasGoogleConfig()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google login is not configured yet. Please contact admin.',
            ]);
        }

        /** @var SocialiteUser $googleUser */
        $googleUser = $this->googleDriver()->user();
        $googleId = (string) $googleUser->getId();
        $googleEmail = (string) $googleUser->getEmail();
        $googleName = (string) ($googleUser->getName() ?: 'Google User');

        if ($googleId === '' || $googleEmail === '') {
            return redirect()->route('login')->withErrors([
                'email' => 'Google account did not provide a valid email. Please use another account.',
            ]);
        }

        $user = User::query()
            ->where('google_id', $googleId)
            ->orWhere('email', $googleEmail)
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleName,
                'email' => $googleEmail,
                'password' => Hash::make(Str::random(40)),
                'role' => 'client',
                'is_active' => true,
                'google_id' => $googleId,
                'email_verified_at' => now(),
            ]);
        } elseif (! $user->google_id) {
            $user->forceFill([
                'google_id' => $googleId,
                'email_verified_at' => $user->email_verified_at ?: now(),
            ])->save();
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        if (! $user->google_profile_completed_at) {
            return redirect()->route('google.complete-profile');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function showCompleteProfile(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->google_profile_completed_at) {
            return redirect()->route('dashboard');
        }

        return view('auth.google-complete-profile', [
            'user' => $user,
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function storeCompleteProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'organization' => ['nullable', 'string', 'max:255'],
            'altcha' => ['nullable', 'string'],
        ]);

        if (! AltchaCaptcha::verify($validated['altcha'] ?? null)) {
            throw ValidationException::withMessages([
                'altcha' => 'Captcha verification failed. Please try again.',
            ]);
        }

        $user->forceFill([
            'name' => $validated['name'],
            'organization' => $validated['organization'] ?? null,
            'google_profile_completed_at' => now(),
        ])->save();

        return redirect()->route('dashboard')->with('status', 'Google profile completed.');
    }

    private function hasGoogleConfig(): bool
    {
        return (string) config('services.google.client_id') !== ''
            && (string) config('services.google.client_secret') !== '';
    }

    private function googleDriver()
    {
        // Always pass redirect URL explicitly to avoid missing redirect_uri in OAuth request.
        config(['services.google.redirect' => route('google.callback')]);

        return Socialite::driver('google');
    }
}
