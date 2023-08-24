<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
// use App\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class LoginController extends Controller
{
    // Redirect to Google for authentication
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle Google callback
    public function handleGoogleCallback()
    {
        // $user = Socialite::driver('google')->user();
        try {
            $user = Socialite::driver('google')->user();
            // dd($user);
            // Check if the user exists in the database
            $existingUser = User::where('email', $user->email)->first();
            // dd($existingUser);
            // If the user doesn't exist, create a new user
            if (!$existingUser) {
                $newUser = new User();
                $newUser->name = $user->name;
                $newUser->email = $user->email;
                $newUser->save();

                Auth::login($newUser);
            } else {
                Auth::login($existingUser);
            }

            return redirect()->route('home'); // Redirect to the desired page after login
        } catch (InvalidStateException $e) {
            // $user = Socialite::driver('google')->stateless()->user();
            // dd($e);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }
}
