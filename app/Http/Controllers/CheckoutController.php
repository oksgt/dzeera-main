<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Kavist\RajaOngkir\Facades\RajaOngkir;

class CheckoutController extends Controller
{

    public function checkout(Request $request){
        $provinces = Province::pluck('name', 'province_id');
        return view('checkout', compact('provinces'));
    }

    public function getCities($id)
    {
        $city = City::where('province_id', $id)->pluck('name', 'city_id');
        return response()->json($city);
    }

    public function check_ongkir(Request $request)
    {
        $cost = RajaOngkir::ongkosKirim([
            'origin'        => $request->city_origin, // ID kota/kabupaten asal
            'originType' => "city",
            'destination'   => $request->city_destination, // ID kota/kabupaten tujuan
            'weight'        => $request->weight, // berat barang dalam gram
            'destinationType' => "city",
            'courier'       => 'jne', //$request->courier, // kode kurir pengiriman: ['jne', 'tiki', 'pos'] untuk starter
        ])->get();

        return response()->json($cost);
    }

    public function checkout_next(Request $request)
    {
        // Validate the form input
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
            'province_destination' => 'required',
            'city_destination' => 'required',
            'ongkir_list' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Process the form data
        // Retrieve the form values using $request->input('input_name')

        // Redirect or perform further actions after processing
        dump($request->all());
        // return redirect()->route('success.page');
    }
}
