<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
// use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
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

            // sync cart
            $data = [];
            // if (auth()->check()) {
                // $cart = Cart::where('user_id', auth()->id())->get();
                // $cart_arr = json_decode($cart[0]->data, true);
            //     $data = $cart_arr;
            // } else {
            //     $cart = json_decode(request()->cookie('cart'), true) ?? [];
            //     $data = $cart;
            // }

            // for
            $cart = json_decode(request()->cookie('cart'), true) ?? [];
            $dataArray = $cart;
            $user_id = auth()->id();

            foreach ($dataArray as $data) {
                $existingRecord = DB::table('carts')
                    ->where('user_id', $user_id)
                    ->where('product_id', $data['product_id'])
                    ->where('color_opt_id', $data['color_opt_id'])
                    ->where('size_opt_id', $data['size_opt_id'])
                    ->first();

                if ($existingRecord) {
                    $newQty = $existingRecord->qty + $data['qty'];

                    DB::table('carts')
                        ->where('user_id', $user_id)
                        ->where('product_id', $data['product_id'])
                        ->where('color_opt_id', $data['color_opt_id'])
                        ->where('size_opt_id', $data['size_opt_id'])
                        ->update(['qty' => $newQty]);
                } else {
                    $data['user_id'] = $user_id;
                    DB::table('carts')->insert($data);
                }
            }

            $cart = Cart::where('user_id', auth()->id())
            ->select('product_id', 'color_opt_id', 'size_opt_id', 'qty', 'price')
            ->get();
            $cart_arr = json_decode($cart, true);
            $cookie = Cookie::make('cart', json_encode($cart_arr));

            return redirect()->route('home')->cookie($cookie);; // Redirect to the previous page after login
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
