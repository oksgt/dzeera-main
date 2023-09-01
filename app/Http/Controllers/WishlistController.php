<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    public function addToWishlist(Request $request)
    {
            $productItemId = $request->input('product_item_id');
            $productItemSlug = $request->input('product_item_slug');
            $productName = $request->input('product_name');
            $colorName = $request->input('color_name');

        if (auth()->check()) {

            $cek = Wishlist::where('user_id', auth()->id())->first();

            if($cek){


                $decoded_data = json_decode(json_decode(json_encode($cek->data), true));
                $wishlist = json_decode(request()->cookie('wishlist')) ?? [];

                $item = [
                    'product_item_id'   => $productItemId,
                    'product_item_slug' => $productItemSlug,
                    'product_name'      => $productName,
                    'color_name'        => $colorName,
                ];

                $new_decoded_data = [];
                foreach ($decoded_data as $object) {
                    $new_decoded_data[] = (array) $object;
                }

                if (in_array($item, $new_decoded_data)) {
                    return redirect()->back()->with('wishlist_exists', true);
                }

                $wishlist_arr = array_merge($new_decoded_data, [$item]);

                $wishlist =  Wishlist::find($cek->id);
                $wishlist->user_id = auth()->id();
                $wishlist->data = json_encode($wishlist_arr);
                $wishlist->save();

                $cookie = Cookie::make('wishlist', json_encode($wishlist_arr));
                return redirect()->back()->with('wishlist_added', true)->cookie($cookie);

            } else {
                $wishlist = json_decode($request->cookie('wishlist'), true) ?? [];

                $item = [
                    'product_item_id'   => $productItemId,
                    'product_item_slug' => $productItemSlug,
                    'product_name'      => $productName,
                    'color_name'        => $colorName,
                ];

                if (in_array($item, $wishlist)) {
                    return redirect()->back()->with('wishlist_exists', true);
                }

                $wishlist_arr = array_merge($wishlist, [$item]);

                $wishlist = new Wishlist();
                $wishlist->user_id = auth()->id();
                $wishlist->data = json_encode($wishlist_arr);
                $wishlist->save();

                $cookie = Cookie::make('wishlist', json_encode($wishlist_arr));
                return redirect()->back()->with('wishlist_added', true)->cookie($cookie);
            }
        } else {

            $wishlist = json_decode($request->cookie('wishlist'), true) ?? [];

            $item = [
                'product_item_id'   => $productItemId,
                'product_item_slug' => $productItemSlug,
                'product_name'      => $productName,
                'color_name'        => $colorName,
            ];

            if (in_array($item, $wishlist)) {
                return redirect()->back()->with('wishlist_exists', true);
            }

            $wishlist = array_merge($wishlist, [$item]);

            $cookie = Cookie::make('wishlist', json_encode($wishlist));
            return redirect()->back()->with('wishlist_added', true)->cookie($cookie);
        }
    }

    public function getWishlist()
    {
        $data = [];
        if (auth()->check()) {
            $wishlist = Wishlist::where('user_id', auth()->id())->get();
            $wishlist_arr = json_decode($wishlist[0]->data, true);
            $data = $wishlist_arr;
        } else {
            $wishlist = json_decode(request()->cookie('wishlist'), true) ?? [];
            $data = $wishlist;
        }

        if(empty($data)){
            return route('home');
        }

        $productItemIds = Arr::pluck($data, 'product_item_id');
        $result = "and CONCAT(p.id, '-', pco.id) in ('" . implode("', '", $productItemIds) . "' )";

        $sql = "
        SELECT
            CONCAT(p.slug, '-', LOWER(pco.color_name)) AS item_slug,
            CONCAT(p.id, '-', pco.id) AS item_id,
            pco.id AS color_id,
            pco.color_name,
            p.id AS product_id,
            p.brand_id,
            p.category_id,
            p.product_sku,
            p.product_name,
            p.slug,
            p.product_status,
            p.product_availability,
            p.rating,
            pi2.file_name,
            min_prices.base_price,
            min_prices.disc,
            min_prices.price
        FROM
            product_color_options pco
        JOIN
            products p ON p.id = pco.product_id
        JOIN
            product_tags pt ON pt.product_id = p.id
        LEFT JOIN
            product_images pi2 ON pi2.product_id = p.id AND pi2.is_thumbnail = 1
        JOIN
            (
                SELECT
                    product_id,
                    MIN(price) AS price,
                    disc,
                    base_price
                FROM
                    product_options
                GROUP BY
                    product_id
            ) AS min_prices ON p.id = min_prices.product_id
        WHERE
            pt.tag_id = 1
            AND p.product_availability = 'y'
            $result
        GROUP BY item_id;";

        $data_obj = DB::select($sql);
        foreach ($data_obj as $key => $value) {
            $data_obj[$key]->base_price = (int) $data_obj[$key]->base_price;
            $data_obj[$key]->disc       = (int) $data_obj[$key]->disc;
            $data_obj[$key]->price      = (int) $data_obj[$key]->price;
        }

        $data = $data_obj;
        return view('wishlist', compact( 'data'));
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
