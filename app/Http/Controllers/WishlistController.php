<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class WishlistController extends Controller
{
    public function addToWishlist(Request $request)
    {
        $productOptionId = $request->input('product_item_slug');

        // Check if the user is logged in
        if (auth()->check()) {
            // Add wishlist item for the authenticated user
            $wishlist = Wishlist::create([
                'user_id' => auth()->id(),
                'product_option_id' => $productOptionId,
            ]);

            response()->json($wishlist);
            return redirect()->back();
        } else {
            $productOptionId = $request->input('product_item_slug');

            // Retrieve the current wishlist from the cookie
            $wishlist = json_decode($request->cookie('wishlist'), true) ?? [];

            // Check if the product option ID already exists in the wishlist
            if (in_array($productOptionId, $wishlist)) {
                return redirect()->back()->with('wishlist_exists', true);
            }

            // Add the product option ID to the wishlist
            $wishlist[] = $productOptionId;

            // Set the updated wishlist in the cookie
            $cookie = Cookie::make('wishlist', json_encode($wishlist));

            // Redirect back with success message
            return redirect()->back()->with('wishlist_added', true)->cookie($cookie);
        }
    }

    public function getWishlist()
    {
        if (auth()->check()) {
            // Get the wishlist items for the authenticated user
            $wishlist = Wishlist::where('user_id', auth()->id())->get();

            // Return the wishlist items as a response
            return response()->json($wishlist);
        } else {
            // Retrieve the wishlist from localStorage
            $wishlist = json_decode(request()->cookie('wishlist'), true) ?? [];

            // Return the wishlist items as a response
            return response()->json($wishlist);
        }
    }

    public function syncWishlist(Request $request)
    {
        // Perform synchronization logic for the authenticated user
        if (auth()->check()) {
            // Get the wishlist items for the authenticated user
            $wishlist = Wishlist::where('user_id', auth()->id())->get();

            // Perform synchronization logic here
            // Example pseudocode: Loop through $request->input('wishlist') and sync with $wishlist

            // Return the synchronized wishlist items as a response
            return response()->json($wishlist);
        } else {
            // Retrieve the wishlist from localStorage
            $wishlist = json_decode(request()->cookie('wishlist'), true) ?? [];

            // Perform synchronization logic with the $wishlist array
            // Example pseudocode: Loop through $request->input('wishlist') and sync with $wishlist

            // Store the updated wishlist in localStorage
            return response()->json($wishlist)->withCookie(cookie('wishlist', json_encode($wishlist)));
        }
    }
}
