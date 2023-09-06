<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
            $productId  = $request->input('product_id');
            $color      = $request->input('color_opt_id');
            $size       = $request->input('size_opt_id');
            $price      = $request->input('price');
            $qty        = $request->input('qty');

        if (auth()->check()) {

            $cek = Cart::where([
                'product_id'   => $productId,
                'color_opt_id' => $color,
                'size_opt_id'  => $size,
                'user_id'      => auth()->id()
            ])->first();


            if($cek){
                // dd($cek);
                // echo json_decode(json_encode($cek->data), true); die;
                $decoded_data = json_decode(json_decode(json_encode($cek->data), true));
                $cart = json_decode(request()->cookie('cart')) ?? [];

                $new_item = [
                    'product_id'   => $productId,
                    'color_opt_id' => $color,
                    'size_opt_id'  => $size,
                    'qty'          => $qty,
                    'price'        => $price
                ];

                // Iterate over the array
                foreach ($decoded_data as &$item) {
                    // Check if the combination matches
                    if ($item->product_id === $new_item['product_id']
                        && $item->color_opt_id === $new_item['color_opt_id']
                        && $item->size_opt_id === $new_item['size_opt_id']
                    ) {
                        // Update the quantity value
                        $item->qty = strval(intval($item->qty) + intval($new_item['qty']));
                        break; // Stop iterating after finding the match
                    }
                }

                // Encode the updated array back into JSON
                $updatedData = json_encode($decoded_data);

                // Output the updated JSON
                // echo $updatedData;
                // die;

                $new_decoded_data = [];
                foreach ($decoded_data as $object) {
                    $new_decoded_data[] = (array) $object;
                }


                if (in_array($item, $new_decoded_data)) {
                    return redirect()->back()->with('cart_exists', true);
                }

                $cart_arr = array_merge($new_decoded_data, [$item]);

                $cart =  Cart::find($cek->id);

                $cart->user_id = auth()->id();
                $cart->data = json_encode($cart_arr);
                $cart->qty     = 22;
                $cart->save();

                $cookie = Cookie::make('cart', json_encode($cart_arr));
                return redirect()->back()->with('cart_added', true)->cookie($cookie);

            } else {
                $cart = json_decode($request->cookie('cart'), true) ?? [];

                $item = [
                    'product_id'   => $productId,
                    'color_opt_id' => $color,
                    'size_opt_id'  => $size,
                    'qty'          => $qty,
                    'price'        => $price
                ];

                if (in_array($item, $cart)) {
                    return redirect()->back()->with('cart_exists', true);
                }

                $cart_arr = array_merge($cart, [$item]);

                $cart = new Cart();
                $cart->user_id = auth()->id();
                $cart->data = json_encode($cart_arr);
                $cart->product_id       = $productId;
                $cart->color_opt_id     = $color;
                $cart->size_opt_id      = $size;
                $cart->qty              = $qty;
                $cart->save();

                $cookie = Cookie::make('cart', json_encode($cart_arr));
                return redirect()->back()->with('cart_added', true)->cookie($cookie);
            }
        } else {

            $cart = json_decode($request->cookie('cart'), true) ?? [];

            $item = [
                'product_id'   => $productId,
                'color_opt_id' => $color,
                'size_opt_id'  => $size,
                'qty'          => $qty,
                'price'        => $price
            ];

            if (in_array($item, $cart)) {
                return redirect()->back()->with('wishlist_exists', true);
            }

            $cart = array_merge($cart, [$item]);

            $cookie = Cookie::make('cart', json_encode($cart));
            return redirect()->back()->with('cart_added', true)->cookie($cookie);
        }
    }

    // public function getWishlist()
    // {
    //     $data = [];
    //     if (auth()->check()) {
    //         $wishlist = Wishlist::where('user_id', auth()->id())->get();
    //         $wishlist_arr = json_decode($wishlist[0]->data, true);
    //         $data = $wishlist_arr;
    //     } else {
    //         $wishlist = json_decode(request()->cookie('wishlist'), true) ?? [];
    //         $data = $wishlist;
    //     }
    //     dd($data);
    //     if(empty($data)){
    //         return route('home');
    //     }

    //     $productItemIds = Arr::pluck($data, 'product_item_id');
    //     $result = "and CONCAT(p.id, '-', pco.id) in ('" . implode("', '", $productItemIds) . "' )";

    //     $sql = "
    //     SELECT
    //         CONCAT(p.slug, '-', LOWER(pco.color_name)) AS item_slug,
    //         CONCAT(p.id, '-', pco.id) AS item_id,
    //         pco.id AS color_id,
    //         pco.color_name,
    //         p.id AS product_id,
    //         p.brand_id,
    //         p.category_id,
    //         p.product_sku,
    //         p.product_name,
    //         p.slug,
    //         p.product_status,
    //         p.product_availability,
    //         p.rating,
    //         pi2.file_name,
    //         min_prices.base_price,
    //         min_prices.disc,
    //         min_prices.price
    //     FROM
    //         product_color_options pco
    //     JOIN
    //         products p ON p.id = pco.product_id
    //     JOIN
    //         product_tags pt ON pt.product_id = p.id
    //     LEFT JOIN
    //         product_images pi2 ON pi2.product_id = p.id AND pi2.is_thumbnail = 1
    //     JOIN
    //         (
    //             SELECT
    //                 product_id,
    //                 MIN(price) AS price,
    //                 disc,
    //                 base_price
    //             FROM
    //                 product_options
    //             GROUP BY
    //                 product_id
    //         ) AS min_prices ON p.id = min_prices.product_id
    //     WHERE
    //         pt.tag_id = 1
    //         AND p.product_availability = 'y'
    //         $result
    //     GROUP BY item_id;";

    //     $data_obj = DB::select($sql);
    //     foreach ($data_obj as $key => $value) {
    //         $data_obj[$key]->base_price = (int) $data_obj[$key]->base_price;
    //         $data_obj[$key]->disc       = (int) $data_obj[$key]->disc;
    //         $data_obj[$key]->price      = (int) $data_obj[$key]->price;
    //     }

    //     $data = $data_obj;
    //     return view('wishlist', compact( 'data'));
    // }

    // public function syncWishlist(Request $request)
    // {
    //     // Perform synchronization logic for the authenticated user
    //     if (auth()->check()) {
    //         // Get the wishlist items for the authenticated user
    //         $wishlist = Wishlist::where('user_id', auth()->id())->get();

    //         // Perform synchronization logic here
    //         // Example pseudocode: Loop through $request->input('wishlist') and sync with $wishlist

    //         // Return the synchronized wishlist items as a response
    //         return response()->json($wishlist);
    //     } else {
    //         // Retrieve the wishlist from localStorage
    //         $wishlist = json_decode(request()->cookie('wishlist'), true) ?? [];

    //         // Perform synchronization logic with the $wishlist array
    //         // Example pseudocode: Loop through $request->input('wishlist') and sync with $wishlist

    //         // Store the updated wishlist in localStorage
    //         return response()->json($wishlist)->withCookie(cookie('wishlist', json_encode($wishlist)));
    //     }
    // }
}
