<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Cart;
use App\Models\City;
use App\Models\Province;
use App\Models\Transaction;
use App\Models\TransactionDetail;
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

        if (!$data) {
            return redirect()->route('home');
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

        return view('checkout_1', compact('provinces', 'cart_list', 'activeAccounts'));
    }

    public function setCheckoutSession(Request $request)
    {
        $lastStep = $request->last_wizard_step;

        // Add email validation
        if (!filter_var($request->cust_email, FILTER_VALIDATE_EMAIL)) {
            return json_encode(['last_step_status' => false, 'error_message' => 'Invalid email address']);
        }

        $checkout_session = [
            'last_step' => $lastStep,
            'step_1' => [
                'name' => (isset($request->cust_name)) ? $request->cust_name : '',
                'email' => (isset($request->cust_email)) ? $request->cust_email : '',
                'phone' => (isset($request->cust_phone)) ? $request->cust_phone : '',
                'address' => (isset($request->cust_address)) ? $request->cust_address : '',
            ],

            'step_2' => [
                'recipient_name' => (isset($request->recp_name)) ? $request->recp_name : '',
                'recipient_phone' => (isset($request->recp_phone)) ? $request->recp_phone : '',
                'recipient_address' => (isset($request->recp_address)) ? $request->recp_address : '',
                'province_id' => (isset($request->province_destination)) ? $request->province_destination : '',
                'city_id' => (isset($request->city_destination)) ? $request->city_destination : '',
                'postal_code' => (isset($request->kode_pos)) ? $request->kode_pos : '',
                'shipping_service' => (isset($request->ongkir_list)) ? $request->ongkir_list : '',
            ],

            'step_3' => [
                'payment_method' => (isset($request->payment_method)) ? $request->payment_method : '',
            ]
        ];

        $shipping_details = [];
        if ($checkout_session['step_2']['shipping_service'] !== '') {
            $shipping_details = [
                'name' => 'JNE ' . explode('_', $checkout_session['step_2']['shipping_service'])[0],
                'formated_price' => $this->formatNumber(explode('_', $checkout_session['step_2']['shipping_service'])[1]),
                'price' => (int) explode('_', $checkout_session['step_2']['shipping_service'])[1],
            ];
        }

        $shipping_target = [];
        if ($checkout_session['step_2']['province_id'] !== '' && $checkout_session['step_2']['city_id'] !== '') {
            $province = Province::where('id', $checkout_session['step_2']['province_id'])->pluck('name');

            //get city name
            $city = City::where('province_id', $checkout_session['step_2']['province_id'])
                ->where('city_id', $checkout_session['step_2']['city_id'])
                ->pluck('name');

            $shipping_target = [
                'province_name' => $province[0],
                'city_name' => $city[0],
            ];
        }

        $updated_checkout_session = array_merge_recursive($checkout_session, ['step_2' => $shipping_target]);

        $updated_checkout_session = array_merge_recursive($updated_checkout_session, ['step_2' => $shipping_details]);

        session()->put('checkout_session', $updated_checkout_session);

        // Check for empty values based on the last step
        switch ($lastStep) {
            case 'step_1':
                if (
                    empty($checkout_session['step_1']['name']) ||
                    empty($checkout_session['step_1']['email']) ||
                    empty($checkout_session['step_1']['phone']) ||
                    empty($checkout_session['step_1']['address'])
                ) {
                    return json_encode(['last_step_status' => false, 'error_message' => 'Please fill in all required fields']);
                }
                break;
            case 'step_2':
                if (
                    empty($checkout_session['step_2']['recipient_name']) ||
                    empty($checkout_session['step_2']['recipient_phone']) ||
                    empty($checkout_session['step_2']['recipient_address']) ||
                    empty($checkout_session['step_2']['province_id']) ||
                    empty($checkout_session['step_2']['city_id']) ||
                    empty($checkout_session['step_2']['postal_code']) ||
                    empty($checkout_session['step_2']['shipping_service'])
                ) {
                    return json_encode(['last_step_status' => false, 'error_message' => 'Please fill in all required fields']);
                }
                break;
            case 'step_3':

                if (
                    empty($checkout_session['step_3']['payment_method'])
                ) {
                    return json_encode(['last_step_status' => false, 'error_message' => 'Please fill in all required fields']);
                }
                break;
        }

        return json_encode(['last_step_status' => true, 'data' => $updated_checkout_session]);
    }

    private function formatNumber($number, $decimals = 0, $decPoint = '.', $thousandsSep = '.')
    {
        $formattedNumber = number_format($number, $decimals, $decPoint, $thousandsSep);
        return $formattedNumber;
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
        // dump($request->all());
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
        $data = $request->all();
        $checkout_session = session()->get('checkout_session');

        // dump($checkout_session);
        // dump($data);

        $validator = Validator::make($data, [
            'voucher' => 'required|string',
        ]);

        $inputKupon = $data['voucher'];

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

        // if ($cartResult->isEmpty()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'No cart data found for the user.',
        //     ]);
        // }

        // move cart data to transaction detail
        $cartData = Cart::where('user_id', auth()->user()->id)->get();

        // dd($$cartData);

        // if ($cartData->isEmpty()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'No cart data found for the user.',
        //     ]);
        // }

        DB::beginTransaction();

        try {
            $transaction = new Transaction();
            $transaction->user_id = auth()->user()->id;
            $transaction->trans_number = substr(uniqid(), -6);

            $voucherCode = $data['voucher'];
            $voucher = Voucher::where('code', $voucherCode)
                ->whereNull('deleted_at')
                ->where('is_active', 'y')
                ->where(function ($query) {
                    $query->whereDate('start_date', '<=', now())
                        ->whereDate('end_date', '>=', now());
                })
                ->first();

            $data_ = [];
            if (auth()->check()) {
                $cart = Cart::where('user_id', auth()->id())
                    ->select('product_id', 'color_opt_id', 'size_opt_id', 'qty', 'price')
                    ->get();
                $cart_arr = json_decode($cart, true);
                $data_ = $cart_arr;
            } else {
                $cart_cookie = json_decode(request()->cookie('cart'), true) ?? [];
                $data_ = $cart_cookie;
            }

            if (!$data_) {
                return redirect()->route('home');
            }

            $result = [];
            foreach ($data_ as $key => $value) {
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

            $grand_qty = 0;
            $grand_total = 0;

            foreach ($cart_list as $key => $value) {
                $grand_qty += $value->qty;
                $grand_total += $value->total_price;
            }

            $transaction->qty = $grand_qty;
            $transaction->price = $grand_total;

            // dump($voucher);
            // dump($cart_list);

            // echo $grand_qty;
            // echo "<br>";
            // echo $grand_total;

            // die;

            if ($voucher) {
                $transaction->voucher_id = $voucher->code;
                $transaction->cut_off_value = $voucher->value;

                if ($voucher->is_percent === 'y' && $voucher->value > 0)
                    $transaction->final_price = $transaction->price - ($transaction->price * $transaction->cut_off_value / 100);
                else if ($voucher->is_percent === 'n' && $voucher->value > 0)
                    $transaction->final_price = $transaction->price - $transaction->cut_off_value;
                else if ($voucher->is_percent === 'n' && $voucher->value == 0)
                    $transaction->final_price = $grand_total;
                else if ($voucher->is_percent === 'y' && $voucher->value == 0)
                    $transaction->final_price = $grand_total;
            } else {
                $transaction->voucher_id = "-";
                $transaction->cut_off_value = 0;
                $transaction->final_price = $grand_total;
            }

            $transaction->expedition_id = 1; // JNE
            $transaction->expedition_service_type = $checkout_session['step_2']['name'];
            $transaction->shipping_cost = $checkout_session['step_2']['price'];
            $transaction->shipping_code = "-";

            $transaction->final_price = $checkout_session['step_2']['price'] + $transaction->final_price;

            $transaction->notes = "_";
            $transaction->trans_status = 'unpaid';

            $transaction->cust_name = $checkout_session['step_1']['name'];
            $transaction->cust_email = $checkout_session['step_1']['email'];
            $transaction->cust_phone = $checkout_session['step_1']['phone'];
            $transaction->cust_address = $checkout_session['step_1']['address'];

            $transaction->recp_name = $checkout_session['step_2']['recipient_name'];
            $transaction->recp_email = "-";
            $transaction->recp_phone = $checkout_session['step_2']['recipient_phone'];
            $transaction->recp_address = $checkout_session['step_2']['recipient_address'];
            $transaction->shipping_address = $checkout_session['step_2']['recipient_address'];
            $transaction->city = $checkout_session['step_2']['city_id'];
            $transaction->province = $checkout_session['step_2']['province_id'];
            $transaction->postal_code = $checkout_session['step_2']['postal_code'];
            $transaction->phone_number = $checkout_session['step_2']['recipient_phone'];

            $transaction->payment_method = $checkout_session['step_3']['payment_method'];

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

            //insert transaction
            $transaction->save();

            // Insert cart data into transaction_details table
            foreach ($cartData as $cartItem) {
                $transactionDetail = new TransactionDetail([
                    'trans_number' => $transaction->trans_number, // Replace with your logic to generate transaction number
                    'user_id' => $cartItem->user_id,
                    'product_id' => $cartItem->product_id,
                    'color_opt_id' => $cartItem->color_opt_id,
                    'size_opt_id' => $cartItem->size_opt_id,
                    'qty' => $cartItem->qty,
                    'price' => $cartItem->price,
                    'is_gift' => false, // Replace with your logic to determine if it's a gift or not
                ]);

                $transactionDetail->save();
            }

            // Delete cart data from carts table
            Cart::where('user_id', auth()->user()->id)->delete();

            // Commit the transaction
            DB::commit();

            return response()->json([
                'status' => true,
                'data' => [
                    'trans_code'  => $transaction->trans_number,
                    'grand_total' => $transaction->final_price,
                    'payment'     => $checkout_session['step_3']['payment_method']
                ],
                'message' => 'Ok'
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurred
            DB::rollback();

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while finishing checkout: ' . $e->getMessage(),
            ]);
        }

        //send email notication to user

    }

    public function finish(Request $request)
    {
        $snapToken = "";
        $validator = Validator::make($request->all(), [
            // Define validation rules for the incoming data if necessary
        ]);

        if ($validator->fails()) {
            // Handle validation errors
        }

        $data = json_decode($request->getContent(), true);
        // dd($data);

        $bank = BankAccount::where('is_active', 'y')->get();
        $code = $request->code;
        $transaction = Transaction::where('trans_number', $code)
            ->first();

        $finalPrice = $transaction->final_price;
        $maxTime = $transaction->max_time;

        if (!$transaction) {
            return redirect()->route('home');
        }

        if($transaction->snapToken == null){

            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            // Set sanitization on (default)
            \Midtrans\Config::$isSanitized = true;
            // Set 3DS transaction for credit card to true
            \Midtrans\Config::$is3ds = true;

            $error = [];

            try {
                $params = array(
                    'transaction_details' => array(
                        'order_id' => $transaction->trans_number,
                        'gross_amount' => $transaction->final_price,
                    ),
                    'customer_details' => array(
                        'first_name' => $transaction->cust_name,
                        'last_name' => '-',
                        'email' => $transaction->cust_email,
                        'phone' => $transaction->cust_phone,
                    ),
                );

                $snapToken = \Midtrans\Snap::getSnapToken($params);

                $transaction->snapToken = $snapToken;
                $transaction->save();
            } catch (\Exception $e) {
                $error[] = $e->getMessage();
                dd($error);
            }
        } else {
            $snapToken = $transaction->snapToken;
        }

        $payment_method = $transaction->payment_method;

        return view('checkout_finish', compact('bank', 'code', 'finalPrice', 'maxTime', 'snapToken', 'payment_method'));
    }

    public function midtransCallback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == "capture" or $request->transaction_status == "settlement") {
                $affectedRows = DB::table('transactions')
                    ->where('trans_number', $request->order_id)
                    ->update(['trans_status' => 'paid']);
            }
        }
    }

    public function thankYou(Request $request)
    {
        echo 'Thank you for shopping with us!';
    }
}
