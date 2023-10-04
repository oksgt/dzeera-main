<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Cart;
use App\Models\City;
use App\Models\Province;
use App\Models\Transaction;
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

    public function checkout(Request $request)
    {
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
            if (!$data_obj) {
                return redirect()->route('home');
            }
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

    // public function checkout_finish(Request $request)
    // {
    //     $data = json_decode($request->getContent(), true);

    //     extract($data);

    //     $cart_result = Cart::where('user_id', auth()->user()->id)
    //         ->selectRaw('SUM(qty) as total_qty, SUM(price) as total_price')
    //         ->first();

    //     $transaction = new Transaction();
    //     $transaction->user_id = auth()->user()->id;
    //     $transaction->trans_number = substr(uniqid(), -6);

    //     $transaction->qty = $cart_result->total_qty;;
    //     $transaction->price = $cart_result->total_price;

    //     $is_percent = false;

    //     if ($input_kupon !== "-") {
    //         $voucher = Voucher::where('code', $code)
    //             ->whereNull('deleted_at')
    //             ->first();

    //         if (!$voucher) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Invalid voucher code.'
    //             ]);
    //         }

    //         $start_date = $voucher->start_date;
    //         $end_date = $voucher->end_date;

    //         $current_date = date('Y-m-d');

    //         if ($current_date < $start_date || $current_date > $end_date) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Expired voucher code.'
    //             ]);
    //         }

    //         if ($voucher->is_active !== 'y') {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Invalid voucher code.'
    //             ]);
    //         }

    //         $transaction->voucher_id = $voucher->code;
    //         $transaction->cut_off_value = $cut_off_value;

    //         if ($voucher->is_percent === 'y') {
    //             $is_percent = true;
    //         }
    //     } else {
    //         $transaction->voucher_id = "-";
    //         $transaction->cut_off_value = "0";
    //     }

    //     $ongkir_list_data = explode("_", $ongkir_list);

    //     $transaction->expedition_id = 1; //JNE
    //     $transaction->expedition_service_type = $ongkir_list_data[0];
    //     $transaction->shipping_cost = $ongkir_list_data[1];
    //     $transaction->shipping_code = "-";

    //     if ($is_percent) {
    //         $transaction->final_price = $transaction->price - ($transaction->price * $transaction->cut_off_value / 100);
    //     } else {
    //         $transaction->final_price = $transaction->price - $transaction->cut_off_value;
    //     }

    //     $transaction->shipping_address = $recp_address;
    //     $transaction->city = $city;
    //     $transaction->province = $province;
    //     $transaction->postal_code = $postal_code;
    //     $transaction->phone_number = $phone_number;
    //     $transaction->notes = "_";
    //     $transaction->trans_status = 'unpaid';

    //     $transaction->cust_name = $cust_name;
    //     $transaction->cust_email = $cust_email;
    //     $transaction->cust_phone = $cust_phone;
    //     $transaction->cust_address = $cust_address;

    //     $transaction->recp_name  = $recp_name;
    //     $transaction->recp_email = "-";
    //     $transaction->recp_phone = $recp_phone;
    //     $transaction->recp_address = $recp_address;

    //     $transaction->payment_method = $payment_method;

    //     $currentTimestamp = time();
    //     $appSetting = DB::table('app_settings')
    //         ->where('status', 1)
    //         ->where('key', 'batas_transfer')
    //         ->first();

    //     if ($appSetting) {
    //         $value = $appSetting->value;
    //         $minute = $value;
    //         $nextTimestamp = $currentTimestamp + ($minute * 60);
    //         $nextDateTime = date('Y-m-d H:i:s', $nextTimestamp);
    //         $transaction->max_time = $nextDateTime;

    //     } else {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Max transfer time not set'
    //         ]);
    //     }

    //     $transaction->save();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Ok'
    //     ]);
    // }

    public function checkout_finish(Request $request)
    {
        $data = $request->json()->all();

        $validator = Validator::make($data, [
            'input_kupon' => 'required|string',
        ]);

        $inputKupon = $data['_voucher'];

        if ($inputKupon === '-' && !array_key_exists('code', $data)) {
            $validator->errors()->add('code', 'The code field is required when input kupon is -.');
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $cartResult = Cart::where('user_id', auth()->user()->id)
            ->selectRaw('SUM(qty) as total_qty, SUM(price) as total_price')
            ->first();

        $transaction = new Transaction();
        $transaction->user_id = auth()->user()->id;
        $transaction->trans_number = substr(uniqid(), -6);
        $transaction->qty = $cartResult->total_qty;
        $transaction->price = $cartResult->total_price;

        $voucherCode = $data['_voucher'];
        $voucher = Voucher::where('code', $voucherCode)
            ->whereNull('deleted_at')
            ->where('is_active', 'y')
            ->where(function ($query) {
                $query->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now());
            })
            ->first();
        // dd($voucher);
        if ($voucher) {
            $transaction->voucher_id = $voucher->code;
            $transaction->cut_off_value = $voucher->value;

            if($voucher->is_percent === 'y' && $voucher->value > 0)
                $transaction->final_price = $transaction->price - ($transaction->price * $transaction->cut_off_value / 100);
            else if($voucher->is_percent === 'n' && $voucher->value > 0)
                $transaction->final_price = $transaction->price - $transaction->cut_off_value;
            else if($voucher->is_percent === 'n' && $voucher->value == 0)
                $transaction->final_price = $transaction->price;
            else if($voucher->is_percent === 'y' && $voucher->value == 0)
            $transaction->final_price = $transaction->price;

        } else {
            $transaction->voucher_id = "x";
            $transaction->cut_off_value = 0;
            $transaction->final_price = $transaction->price;
        }

        // $ongkirListData = explode("_", $data['ongkir_list']);
        $transaction->expedition_id = 1; // JNE
        $transaction->expedition_service_type = $data['_service'];
        $transaction->shipping_cost = $data['_service_price'];
        $transaction->shipping_code = "-";


        $transaction->notes = "_";
        $transaction->trans_status = 'unpaid';

        $transaction->cust_name = $data['cust_name'];
        $transaction->cust_email = $data['cust_email'];
        $transaction->cust_phone = $data['cust_phone'];
        $transaction->cust_address = $data['cust_address'];

        $transaction->recp_name = $data['recp_name'];
        $transaction->recp_email = "-";
        $transaction->recp_phone = $data['recp_phone'];
        $transaction->recp_address = $data['recp_address'];
        $transaction->shipping_address = $data['recp_address'];
        $transaction->city = $data['_city'];
        $transaction->province = $data['_province'];
        $transaction->postal_code = $data['kode_pos'];
        $transaction->phone_number = $data['recp_phone'];

        $transaction->payment_method = $data['payment_method'];

        $appSetting = DB::table('app_settings')
            ->where('status', 1)
            ->where('key', 'batas_transfer')
            ->first();

        if ($appSetting) {
            $value = $appSetting->value;
            $minute = $value;
            $nextDateTime = now()->addMinutes($minute);
            $transaction->max_time = $nextDateTime;
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Max transfer time not set'
            ]);
        }

        $transaction->save();

        return response()->json([
            'status' => true,
            'message' => 'Ok'
        ]);
    }

    public function finish(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Define validation rules for the incoming data if necessary
        ]);

        if ($validator->fails()) {
            // Handle validation errors
        }

        $data = json_decode($request->getContent(), true);
        dd($data);

        $bank = BankAccount::where('is_active', 'y')->get();
        return view('checkout_finish', compact('bank'));
    }
}
