<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirectGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle()
    {
        $socialUser = Socialite::driver('google')->user();
        return $this->loginOrCreate($socialUser, 'google');
    }

    public function redirectGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function callbackGithub()
    {
        $socialUser = Socialite::driver('github')->user();
        return $this->loginOrCreate($socialUser, 'github');
    }

    private function loginOrCreate($socialUser, $provider)
    {
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name'     => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                'email'    => $socialUser->getEmail(),
                'provider' => $provider,
                'password' => bcrypt(Str::random(32)),
            ]);
        }

        Auth::login($user);

        return redirect('/dashboard');
    }
}
