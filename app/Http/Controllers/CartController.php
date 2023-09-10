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

    private function removeCookie($cookieName){
        Cookie::queue(Cookie::forget($cookieName));
        return ['ok' => true];
    }

    public function addToCart(Request $request)
    {
            $productId  = $request->input('product_id');
            $color      = $request->input('color_opt_id');
            $size       = $request->input('size_opt_id');
            $price      = $request->input('price');
            $qty        = $request->input('qty');

        if (auth()->check()) {
            echo "here";
            $cek = Cart::where([
                'product_id'   => $productId,
                'color_opt_id' => $color,
                'size_opt_id'  => $size,
                'user_id'      => auth()->id()
            ])->first();

            if($cek){
                //if exist, update qty in table
                $cart = Cart::find($cek->id);

                $cart->user_id = auth()->id();
                $cart->qty     = $cart->qty + $qty;
                $cart->save();

                //remove cookie
                $deleteCookie = $this->removeCookie('cart');

                $cart = Cart::where('user_id', auth()->id())
                ->select('product_id', 'color_opt_id', 'size_opt_id', 'qty', 'price')
                ->get();
                $cart_arr = json_decode($cart, true);
                // echo json_encode($cart_arr); die;;
                $cookie = Cookie::make('cart', json_encode($cart_arr));
                // dd($cookie);
                return redirect()->back()->with('cart_added', true)->withCookie($cookie);

                // Check if the item already exists in the cart based on product_id, color_opt_id, and size_opt_id
                // $item = [
                //     'product_id'   => $productId,
                //     'color_opt_id' => $color,
                //     'size_opt_id'  => $size,
                //     'qty'          => $qty,
                //     'price'        => $price
                // ];

                // $cart_cookie = json_decode($request->cookie('cart'), true) ?? [];

                // $existingItem = array_filter($cart_cookie, function ($cartItem) use ($item) {
                //     return $cartItem['product_id'] === $item['product_id'] &&
                //         $cartItem['color_opt_id'] === $item['color_opt_id'] &&
                //         $cartItem['size_opt_id'] === $item['size_opt_id'];
                // });

                // if (!empty($existingItem)) {
                //     // Item exists, update the quantity
                //     $existingItemKey = key($existingItem);
                //     $cart_cookie[$existingItemKey]['qty'] += $qty;
                //     $cookie = Cookie::make('cart', json_encode($cart_cookie));
                //     return redirect()->back()->with('cart_added', true)->cookie($cookie);
                // }

                // $cart[] = $item;

                // $cookie = Cookie::make('cart', json_encode($cart));
                // return redirect()->back()->with('cart_added', true)->cookie($cookie);

                // dd($cek);
                // echo json_decode(json_encode($cek->data), true); die;
                // $decoded_data = json_decode(json_decode(json_encode($cek->data), true));
                // $cart = json_decode(request()->cookie('cart')) ?? [];

                // $new_item = [
                //     'product_id'   => $productId,
                //     'color_opt_id' => $color,
                //     'size_opt_id'  => $size,
                //     'qty'          => $qty,
                //     'price'        => $price
                // ];

                // // Iterate over the array
                // foreach ($decoded_data as &$item) {
                //     // Check if the combination matches
                //     if ($item->product_id === $new_item['product_id']
                //         && $item->color_opt_id === $new_item['color_opt_id']
                //         && $item->size_opt_id === $new_item['size_opt_id']
                //     ) {
                //         // Update the quantity value
                //         $item->qty = strval(intval($item->qty) + intval($new_item['qty']));
                //         break; // Stop iterating after finding the match
                //     }
                // }

                // $new_decoded_data = [];
                // foreach ($decoded_data as $object) {
                //     $new_decoded_data[] = (array) $object;
                // }


                // if (in_array($item, $new_decoded_data)) {
                //     return redirect()->back()->with('cart_exists', true);
                // }

                // $cart_arr = array_merge($new_decoded_data, [$item]);

                // $cart =  Cart::find($cek->id);

                // $cart->user_id = auth()->id();
                // $cart->data = json_encode($cart_arr);
                // $cart->qty     = 22;
                // $cart->save();

                // $cookie = Cookie::make('cart', json_encode($cart_arr));
                // return redirect()->back()->with('cart_added', true)->cookie($cookie);

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

            // Check if the item already exists in the cart based on product_id, color_opt_id, and size_opt_id
            $existingItem = array_filter($cart, function ($cartItem) use ($item) {
                return $cartItem['product_id'] === $item['product_id'] &&
                    $cartItem['color_opt_id'] === $item['color_opt_id'] &&
                    $cartItem['size_opt_id'] === $item['size_opt_id'];
            });

            if (!empty($existingItem)) {
                // Item exists, update the quantity
                $existingItemKey = key($existingItem);
                $cart[$existingItemKey]['qty'] += $qty;
                $cookie = Cookie::make('cart', json_encode($cart));
                return redirect()->back()->with('cart_added', true)->cookie($cookie);
            }

            $cart[] = $item;

            $cookie = Cookie::make('cart', json_encode($cart));
            return redirect()->back()->with('cart_added', true)->cookie($cookie);
        }
    }

    public function getCart()
    {
        $data_db = [];
        $data_cookie = [];
        $data = [];
        if (auth()->check()) {
            echo "db";
            $cart = Cart::where('user_id', auth()->id())
            ->select('product_id', 'color_opt_id', 'size_opt_id', 'qty', 'price')
            ->get();
            $cart_arr = json_decode($cart, true);
            $data = $cart_arr;
        } else {
            $cart_cookie = json_decode(request()->cookie('cart'), true) ?? [];
            $data = $cart_cookie;
        }

        // dd($data);

        if(empty($data)){
            return redirect()->route('home');
        }

        $result = [];
        foreach ($data as $key => $value) {
            $sql = "
            select
            po.id as opt_id,
            p.id as product_id, p.product_name, pco.id as color_opt_id, pco.color_name , pso.id as size_opt_id, pso.`size`, po.price,
            po.qty,
            -- po.base_price, po.disc,
            pi2.file_name
            -- from product_options po
            from carts po
            join products p on p.id = po.product_id
            join product_color_options pco on pco.id = po.color_opt_id
            join product_size_options pso on pso.id = po.size_opt_id
            LEFT JOIN
                product_images pi2 ON pi2.product_id = p.id AND pi2.is_thumbnail = 1
            where p.id = ? and pco.id = ? and pso.id = ?";

            $data_obj = DB::select($sql, [$value['product_id'], $value['color_opt_id'], $value['size_opt_id']]);
            array_push($result, $data_obj[0]);
        }

        foreach ($result as $key => $value) {
            $result[$key]->price      = (int) $result[$key]->price;
        }

        $data = $result;
        return view('cart', compact( 'data'));
    }

    // public function synccart(Request $request)
    // {
    //     // Perform synchronization logic for the authenticated user
    //     if (auth()->check()) {
    //         // Get the cart items for the authenticated user
    //         $cart = Cart::where('user_id', auth()->id())->get();

    //         // Perform synchronization logic here
    //         // Example pseudocode: Loop through $request->input('cart') and sync with $cart

    //         // Return the synchronized cart items as a response
    //         return response()->json($cart);
    //     } else {
    //         // Retrieve the cart from localStorage
    //         $cart = json_decode(request()->cookie('cart'), true) ?? [];

    //         // Perform synchronization logic with the $cart array
    //         // Example pseudocode: Loop through $request->input('cart') and sync with $cart

    //         // Store the updated cart in localStorage
    //         return response()->json($cart)->withCookie(cookie('cart', json_encode($cart)));
    //     }
    // }
}