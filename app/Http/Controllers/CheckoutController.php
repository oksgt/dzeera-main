<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Cart;
use App\Models\City;
use App\Models\Province;
use App\Models\Voucher;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session as FacadesSession;
use Illuminate\Support\Facades\Validator;
use Kavist\RajaOngkir\Facades\RajaOngkir;

class CheckoutController extends Controller
{

    public function checkout(Request $request){
        $data_db = [];
        $data_cookie = [];
        $data = [];
        if (auth()->check()) {
            $cart = Cart::where('user_id', auth()->id())
            ->select('product_id', 'color_opt_id', 'size_opt_id', 'qty', 'price')
            ->get();
            $cart_arr = json_decode($cart, true);
            $data = $cart_arr;
        } else {
            $cart_cookie = json_decode(request()->cookie('cart'), true) ?? [];
            $data = $cart_cookie;
        }

        $result = [];
        foreach ($data as $key => $value) {
            $sql = "
            select
            po.id as cart_id,
            po.id as opt_id,
            p.id as product_id, p.product_name, pco.id as color_opt_id, pco.color_name , pso.id as size_opt_id, pso.`size`, po.price,
            po.qty,
            po.qty * po.price as total_price,
            -- po.base_price, po.disc,
            pi2.file_name
            -- from product_options po
            from carts po
            join products p on p.id = po.product_id
            join product_color_options pco on pco.id = po.color_opt_id
            join product_size_options pso on pso.id = po.size_opt_id
            LEFT JOIN
                product_images pi2 ON pi2.product_id = p.id AND pi2.is_thumbnail = 1
            where p.id = ? and pco.id = ? and pso.id = ? and po.user_id = ?";

            $data_obj = DB::select($sql, [$value['product_id'], $value['color_opt_id'], $value['size_opt_id'], auth()->id()]);
            array_push($result, $data_obj[0]);
        }

        foreach ($result as $key => $value) {
            $result[$key]->price      = (int) $result[$key]->price;
        }

        $cart_list = $result;
        $provinces = Province::pluck('name', 'province_id');
        $activeAccounts = DB::table('bank_accounts')
        ->where('is_active', 'y')
        ->whereNull('deleted_at')
        ->select('bank_name', 'account_number', 'account_name')
        ->get();
        return view('checkout', compact('provinces', 'cart_list', 'activeAccounts'));
    }

    public function getCities($id)
    {
        $city = City::where('province_id', $id)->pluck('name', 'city_id');
        return response()->json($city);
    }

    public function getCityName($provinceId, $cityId)
    {
        $city = City::where('province_id', $provinceId)
                    ->where('city_id', $cityId)
                    ->pluck('name');

        return response()->json($city);
    }

    public function getProvinceName($id)
    {
        $province = Province::where('id', $id)->pluck('name');
        return response()->json($province);
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

    public function getVouchersByCode($code)
    {
        $vouchers = Voucher::where('code', $code)
                    ->where('is_active', 'y')
                    ->get();

        if ($vouchers->isEmpty()) {
            return response()->json([
                'status' => false,
                'data' => null
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $vouchers[0]
        ]);
    }

    public function store_voucher(Request $request)
    {
        $code = $request->input('value');

        $voucherKey = 'voucher';
        $existingVoucher = $request->session()->get($voucherKey);

        if ($existingVoucher && $existingVoucher['code'] === $code) {
            return response()->json([
                'status' => false,
                'message' => 'The same voucher code is already applied.'
            ]);
        }

        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid voucher code.'
            ]);
        }

        $voucherData = [
            'id' => $voucher->id,
            'voucher_name' => $voucher->voucher_name,
            'voucher_desc' => $voucher->voucher_desc,
            'code' => $voucher->code,
            'start_date' => $voucher->start_date,
            'end_date' => $voucher->end_date,
            'is_percent' => $voucher->is_percent,
            'value' => $voucher->value,
            'is_active' => $voucher->is_active,
        ];

        $request->session()->put($voucherKey, $voucherData);

        return response()->json([
            'status' => true,
            'message' => 'Voucher applied successfully',
            'data' => $voucherData
        ]);
    }

    public function printSessions(Request $request)
    {
        $sessions = FacadesSession::all();
        dd($sessions['voucher']);
    }

    public function removeVoucher()
    {
        Session::forget('voucher');
        return response()->json(['success' => true]);
    }

    public function checkout_finish(Request $request){
        $responseData = [
            'status' => 'success',
            'message' => 'Checkout finished successfully',
        ];

        return response()->json($responseData);
    }

    public function finish(Request $request){
        // get bank list
        $bank = BankAccount::where('is_active', 'y')->get();
        return view('checkout_finish', compact('bank'));
    }
}
