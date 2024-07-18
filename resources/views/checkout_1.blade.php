@extends('layouts.app')

@section('content')
    <section class="" style="margin-top: 140px">

        <div class="container-fluid mt-3 mb-4">
            <div class="row mt-4 mb-2 ">
                <div class="col-lg-12 col-md-12 col-12 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <h3>Checkout</h3>
                    </div>
                </div>
            </div>

            <div class="container ">
                <div class="wizard ">
                    <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
                        <li class="nav-item flex-fill" role="presentation" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Customer Information" >
                            <a class="nav-link active mx-auto d-flex align-items-center justify-content-center"
                                href="#step1" id="step1-tab" data-bs-toggle="tab" role="tab" aria-controls="step1"
                                aria-selected="true"  @disabled(true)>
                                <i class="fas fa-user"></i>
                            </a>
                        </li>
                        <li class="nav-item flex-fill" role="presentation" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Shipping Information">
                            <a class="nav-link rounded-circle mx-auto d-flex align-items-center justify-content-center"
                                href="#step2" id="step2-tab" data-bs-toggle="tab" role="tab" aria-controls="step2"
                                aria-selected="false" title="Step 2" @disabled(true)    >
                                <i class="fas fa-shipping-fast"></i>
                            </a>
                        </li>
                        <li class="nav-item flex-fill" role="presentation" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Payment Method">
                            <a class="nav-link rounded-circle mx-auto d-flex align-items-center justify-content-center"
                                href="#step3" id="step3-tab" data-bs-toggle="tab" role="tab" aria-controls="step3"
                                aria-selected="false" title="Step 3"  @disabled(true)>
                                <i class="fas fa-money-check-alt"></i>
                            </a>
                        </li>
                        <li id="ddd" class="nav-item flex-fill" role="presentation" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="Summary">
                            <a class="nav-link rounded-circle mx-auto d-flex align-items-center justify-content-center"
                                href="#step4" id="step4-tab" data-bs-toggle="tab" role="tab" aria-controls="step4"
                                aria-selected="false" title="Step 4"  @disabled(true)>
                                <i class="fas fa-flag-checkered"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        @csrf
                        <div class="tab-pane fade show active mt-3 text-center" role="tabpanel" id="step1"
                            aria-labelledby="step1-tab">
                            <h5 style="color: #e30c83 ">{{ __('general.customer_information') }} </h5>
                            <hr>
                            <div class="text-start">
                                <div class="mb-3 mt-3">
                                    <label for="name" class="form-label">{{ __('general.name') }}</label>
                                    <input type="text" class="form-control" id="cust_name" name="cust_name"
                                        placeholder="Enter your name" required value="{{ auth()->user()->name }}" autocomplete="off" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('general.email_address') }}</label>
                                    <input type="email" class="form-control" id="cust_email" name="cust_email"
                                        placeholder="Enter your email address" required value={{ auth()->user()->email }} autocomplete="off" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">{{ __('general.phone') }}</label>
                                    <input type="text" class="form-control" id="cust_phone" name="cust_phone"
                                        placeholder="Enter your phone number" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">{{ __('general.address') }}</label>
                                    <textarea class="form-control" id="address" name="cust_address" rows="3" required></textarea>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-info next" id="btn-continue-1" onclick="checkout_data_session('step_1')"
                                        >{{ __('general.continue') }}
                                        <i class="fas fa-angle-right"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade mt-3 text-center" role="tabpanel" id="step2"
                            aria-labelledby="step2-tab">
                            <h5 style="color: #e30c83 ">{{ __('general.shipping_information') }}</h5>
                            <hr>
                            <div class="row text-start">
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="mb-3 mt-3">
                                        <label for="name"
                                            class="form-label">{{ __('general.recipient_name') }}</label>
                                        <input type="text" class="form-control" id="recp_name" name="recp_name"
                                            placeholder="Enter your name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone"
                                            class="form-label">{{ __('general.recipient_phone') }}</label>
                                        <input type="text" class="form-control" id="recp_phone" name="recp_phone"
                                            placeholder="Enter your phone number">
                                    </div>
                                    <div class="mb-3">
                                        <label for="address"
                                            class="form-label">{{ __('general.recipient_address') }}</label>
                                        <textarea class="form-control" id="recp_address" name="recp_address" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="mb-3 mt-3">
                                        <label for="exampleFormControlInput1"
                                            class="form-label">{{ __('general.province') }}</label>
                                            <select class="form-control provinsi-tujuan" id="province_destination"
                                                name="province_destination" onchange="getCities(this.value)" autocomplete="off">
                                                <option value="0">-- {{ __('general.choose_province') }} --</option>
                                                @foreach ($provinces as $province => $value)
                                                    <option value="{{ $province }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1"
                                            class="form-label">{{ __('general.city') }}</label>
                                        <select class="form-control kota-tujuan" name="city_destination"
                                            id="city_destination" onchange="checkOngkir()">
                                            <option value="">-- {{ __('general.choose_city') }} --</option>

                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1"
                                            class="form-label">{{ __('general.postal_code') }}</label>
                                        <input type="text" class="form-control" id="kode_pos" name="kode_pos"
                                            placeholder="Enter your Postal Code" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1"
                                            class="form-label">{{ __('general.shipping_service') }}</label>
                                        <select class="form-control" name="ongkir_list" id="ongkir_list"
                                            onchange="getSelectedOngkirList()">
                                            <option value="">-- {{ __('general.choose_service') }} --</option>
                                            <!-- Rest of the options -->
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a class="btn btn-secondary previous"><i class="fas fa-angle-left"></i>
                                    {{ __('general.back') }}</a>
                                <button id="btn-continue-2" class="btn btn-info next" onclick="checkout_data_session('step_2')"
                                    >{{ __('general.continue') }} <i class="fas fa-angle-right"></i></button>
                            </div>
                        </div>

                        <div class="tab-pane fade mt-3 text-center " role="tabpanel" id="step3"
                            aria-labelledby="step3-tab">
                            <h5 style="color: #e30c83 ">{{ __('general.payment_method') }}</h5>
                            <hr>
                            <div class="row d-flex justify-content-center">

                                <div class="col-md-4 col-lg-4 col-sm-4">

                                    <label>
                                        <input type="radio" name="payment_method" value="Bank Transfer" selected
                                            checked class="card-input-element" />

                                        <div class="card card-default card-input">
                                            <div class="card-body">
                                                Bank Transfer
                                            </div>
                                        </div>

                                    </label>

                                </div>
                                <div class="col-md-4 col-lg-4 col-sm-4">

                                    <label>
                                        <input type="radio" name="payment_method" value="Merchant"
                                            class="card-input-element" />

                                        <div class="card card-default card-input">
                                            <div class="card-body">
                                                Online / Merchant
                                            </div>
                                        </div>
                                    </label>

                                </div>

                            </div>

                            <div class="d-flex justify-content-between">
                                <a class="btn btn-secondary previous"><i class="fas fa-angle-left"></i>
                                    {{ __('general.back') }}</a>
                                <a class="btn btn-info next"
                                 {{-- onclick="getInputValues()" --}}
                                 onclick="checkout_data_session('step_3')"
                                 >{{ __('general.continue') }} <i
                                        class="fas fa-angle-right"></i></a>
                            </div>
                        </div>

                            <div class="tab-pane fade mt-3" role="tabpanel" id="step4"
                            aria-labelledby="step4-tab">
                            <h5 style="color: #e30c83 ">{{ __('general.summary') }}</h5>
                            <hr>
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-6  mb-2">
                                    <div class="card">
                                        <div class="card-header">{{ __('general.customer_information') }}</div>
                                        <div class="card-body table-responsive">
                                            <table class="table table-sm small text-muted">
                                                <tr>
                                                    <td>{{ __('general.name') }}</td>
                                                    <td>
                                                        <input type="text" id="_cust_name" name="_cust_name"
                                                            class="readonly-input" readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('general.email_address') }}</td>
                                                    <td>
                                                        <input type="text" id="_cust_email" name="_cust_email"
                                                            class="readonly-input" readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('general.phone') }}</td>
                                                    <td>
                                                        <input type="text" id="_cust_phone" name="_cust_phone"
                                                            class="readonly-input" readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('general.address') }}</td>
                                                    <td>
                                                        <input type="text" id="_cust_address" name="_cust_address"
                                                            class="readonly-input" readonly>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-md-6 col-lg-6 ">
                                    <div class="card">
                                        <div class="card-header">{{ __('general.shipping_information') }}</div>
                                        <div class="card-body table-responsive">
                                            <table class="table table-sm small text-muted">
                                                <tr>
                                                    <td>{{ __('general.recipient_name') }}</td>
                                                    <td>
                                                        <input type="text" id="_recp_name" name="_recp_name"
                                                            class="readonly-input" readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('general.recipient_phone') }}</td>
                                                    <td>
                                                        <input type="text" id="_recp_phone" name="_recp_phone"
                                                            class="readonly-input" readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('general.recipient_address') }}</td>
                                                    <td>
                                                        <input type="text" id="_recp_add" name="_recp_add"
                                                            class="readonly-input" readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('general.province') }}</td>
                                                    <td>
                                                        <input type="text" id="_recp_prov" name="_recp_prov"
                                                            class="readonly-input" readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('general.city') }}</td>
                                                    <td>
                                                        <input type="text" id="_recp_city" name="_recp_city"
                                                            class="readonly-input" readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('general.postal_code') }}</td>
                                                    <td>
                                                        <input type="text" id="_recp_postal_code"
                                                            name="_recp_postal_code" class="readonly-input" readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('general.shipping_service') }}</td>
                                                    <td>
                                                        <input type="text" id="_recp_shipping_service"
                                                            name="_recp_shipping_service" class="readonly-input" readonly>
                                                        <input type="hidden" id="_service" name="_service"
                                                            class="readonly-input" readonly>
                                                        <input type="hidden" id="_service_price" name="_service_price"
                                                            class="readonly-input" readonly>
                                                        {{-- <input type="hidden" id="_city" name="_city" class="readonly-input" readonly>
                                              <input type="hidden" id="_province" name="_province" class="readonly-input" readonly> --}}
                                                        <input type="hidden" id="_voucher" name="_voucher"
                                                            class="readonly-input" readonly value="-">
                                                    </td>
                                                </tr>

                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card bg bg-white mt-3">
                                <div class="card-body table-responsive border p-0">
                                    <table class="table table-bordered table-sm w-100 text-start" id="table-summary"
                                        style="font-size: 15px">
                                        <thead>
                                            <tr>
                                                <th>{{ __('general.product_name') }}</th>
                                                {{-- <th>{{ __('general.quantity') }}</th> --}}
                                                <th>{{ __('general.price') }}</th>
                                                <th>{{ __('general.total_price') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $grandTotal = 0; // Initialize grand total variable
                                            foreach ($cart_list as $index => $row):
                                                $grandTotal += $row->total_price; // Accumulate the total price for each row
                                            ?>
                                            <tr>
                                                {{-- <td><?php echo $index + 1; ?></td> --}}
                                                <td>
                                                    <?php echo $row->product_name . ' - ' . $row->color_name . ' - (Size ' . $row->size . ')'; ?>
                                                    <br />
                                                    <?php echo 'Qty ' . $row->qty . ' item'; ?>
                                                </td>
                                                {{-- <td><?php echo $row->qty; ?></td> --}}
                                                <td><?php echo formatNumber($row->price); ?></td>
                                                <td><?php echo formatNumber($row->total_price); ?></td>
                                            </tr>
                                            <?php endforeach; ?>

                                            <tr id="exp_container">

                                            </tr>

                                            @php
                                                $appliedVoucher = getAppliedVoucher();
                                                $v_value = 0;
                                            @endphp

                                            @if ($appliedVoucher !== null)
                                                @php
                                                    if ($appliedVoucher['is_percent'] == 'y') {
                                                        $label_value = $appliedVoucher['value'] . '%';
                                                        $discountAmount = $grandTotal * ($appliedVoucher['value'] / 100);
                                                        $grandTotal = $grandTotal - $discountAmount;
                                                    } else {
                                                        $label_value = formatNumber($appliedVoucher['value']);
                                                        $v_value = $appliedVoucher['value'];
                                                        $grandTotal = $grandTotal - $v_value;
                                                    }

                                                @endphp
                                                <tr class="table-success">
                                                    <td>
                                                        {{ __('general.applied_voucher') }} :
                                                        {{ $appliedVoucher['code'] }}
                                                        <button id="btn-remove-voucher" class="btn btn-sm btn-warning"
                                                            onclick="removeVoucher()" title="Remove Voucher">
                                                            <i class="fa fa-remove"></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="appliedVoucherCode" id="appliedVoucherCode" value="{{ $appliedVoucher['code'] }}">
                                                        <input type="hidden" name="appliedVoucherValue" id="appliedVoucherValue" value="{{ $v_value }}">
                                                        Disc.
                                                    </td>
                                                    <td>{{ $label_value }}</td>
                                                </tr>
                                            @endif


                                            <tr>
                                                <td>
                                                    <a class="btn btn-sm btn-outline-success"
                                                        onclick="openModalVoucher()">Voucher <i
                                                            class="fas fa-ticket"></i></a>
                                                </td>
                                                <td>
                                                    <h4><strong>Grand Total:</strong></h4>
                                                </td>
                                                <td>
                                                    <h4>
                                                        <strong id="display_grandtotal"><?php echo formatNumber($grandTotal); ?></strong>
                                                    </h4>
                                                    <input type="hidden" name="grandTotal" id="grandTotal"
                                                        value="{{ $grandTotal }}">
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>

                                    <small class="text-muted" id="label_payment_method"></small>
                                </div>
                            </div>

                            <div class="row mt-3 d-none">
                                <div class="col-6 table-responsive">
                                    <div class="card">
                                        <div class="card-header">Payment Method</div>
                                        <div class="card-body">
                                            <h6>Available Bank Account</h6>
                                            {{-- <table class="table table-sm small text-muted">
                                                <tr><td>Name</td><td><label id="_cust_name" for=""></label></td></tr>
                                                <tr><td>Email</td><td><label id="_cust_email" for=""></label></td></tr>
                                                <tr><td>Phone Number</td><td><label id="_cust_phone" for=""></label></td></tr>
                                                <tr><td>Address</td><td><label id="_cust_address" for=""></label></td></tr>
                                            </table> --}}

                                            <table class="table table-sm small text-muted">
                                                <thead>
                                                    <tr>
                                                        <th>Bank Name</th>
                                                        <th>Account Number</th>
                                                        <th>Account Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($activeAccounts as $account)
                                                        <tr>
                                                            <td>{{ $account->bank_name }}</td>
                                                            <td>{{ $account->account_number }}</td>
                                                            <td>{{ $account->account_name }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <a class="btn btn-secondary previous"><i class="fas fa-angle-left"></i>
                                    {{ __('general.back') }}</a>
                                <a class="btn btn-info finish" onclick="paid()">{{ __('general.finish_checkout') }} <i
                                        class="fas fa-angle-right"></i></a>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>

    </section>

    <!-- modal -->
    <div class="modal fade" id="kuponModal" tabindex="-1" aria-labelledby="kuponModalLabel" aria-text="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body justify-content-center d-flex">
                    <div class="input-group">

                        <input type="text" name="input_kupon_" id="input_kupon_" class="form-control"
                            placeholder="Voucher Code" aria-label="Voucher Code" aria-describedby="button-addon2">

                        <input type="hidden" name="input_kupon" id="input_kupon" class="form-control"
                            placeholder="Voucher Code" aria-label="Voucher Code" aria-describedby="button-addon2">

                        <button class="btn btn-dark" type="button" id="button-voucher-apply"
                            onclick="callGetVouchersByCode()">
                            <i class="fa fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        var next_page = false;
        $(document).ready(function() {

            // Get the hash from the current URL
            var hash = window.location.hash;
            // console.log(hash);

            // Check if the hash is "#step4"
            if (hash === "#step4") {
                // Get the tab element by its ID
                var tab = document.querySelector('#step4-tab');

                // Activate the tab using the Bootstrap Tab API
                var tabTrigger = new bootstrap.Tab(tab);
                tabTrigger.show();
                // getInputValues();
            }

            //Enable Tooltips
            var tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            //Advance Tabs
            // $(".next").click(function() {
            //     if(next_page){
            //         const nextTabLinkEl = $(".nav-tabs .active")
            //         .closest("li")
            //         .next("li")
            //         .find("a")[0];
            //         const nextTab = new bootstrap.Tab(nextTabLinkEl);
            //         nextTab.show();
            //     }
            // });

            $(".previous").click(function() {
                const prevTabLinkEl = $(".nav-tabs .active")
                    .closest("li")
                    .prev("li")
                    .find("a")[0];
                const prevTab = new bootstrap.Tab(prevTabLinkEl);
                prevTab.show();
            });
        });

        function checkout_data_session(step) {
            let csrfToken = $("meta[name='csrf-token']").attr("content");
            let formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('last_wizard_step', step);
            formData.append('cust_name', $('#cust_name').val()); // Add the 'cust_name' parameter from the input element
            formData.append('cust_email', $('#cust_email').val()); // Add the 'cust_email' parameter from the input element
            formData.append('cust_phone', $('#cust_phone').val()); // Add the 'cust_phone' parameter from the input element
            formData.append('cust_address', $('textarea[name="cust_address"]').val()); // Add the 'cust_address' parameter from the input element
            formData.append('recp_name', $('#recp_name').val()); // Add the 'recp_name' parameter from the input element
            formData.append('recp_phone', $('#recp_phone').val()); // Add the 'recp_phone' parameter from the input element
            formData.append('recp_address', $('textarea[name="recp_address"]').val()); // Add the 'recp_address' parameter from the input element
            formData.append('province_destination', $('#province_destination').val()); // Add the 'province_destination' parameter from the input element
            formData.append('city_destination', $('#city_destination').val()); // Add the 'city_destination' parameter from the input element
            formData.append('kode_pos', $('#kode_pos').val()); // Add the 'kode_pos' parameter from the input element
            formData.append('ongkir_list', $('#ongkir_list').val()); // Add the 'ongkir_list' parameter from the input element
            formData.append('payment_method', $('input[name="payment_method"]:checked').val()); // Add the 'payment_method' parameter from the input element

            $.ajax({
                url: '/checkout-data-session',
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken // Include the CSRF token as a header
                },
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function(data) {
                    var json = JSON.parse(data);
                    console.log(json.last_step_status)
                    if(json.last_step_status == true) {
                        const nextTabLinkEl = $(".nav-tabs .active")
                        .closest("li")
                        .next("li")
                        .find("a")[0];
                        const nextTab = new bootstrap.Tab(nextTabLinkEl);
                        nextTab.show();

                        if(json.data.last_step == 'step_3') {
                            //set value javasctipy by id usin vanilla js
                            document.getElementById('_cust_name').value = json.data.step_1.name;
                            document.getElementById('_cust_email').value = json.data.step_1.email;
                            document.getElementById('_cust_phone').value = json.data.step_1.phone;
                            document.getElementById('_cust_address').value = json.data.step_1.address;

                            document.getElementById('_recp_name').value = json.data.step_2.recipient_name;
                            document.getElementById('_recp_phone').value = json.data.step_2.recipient_phone;
                            document.getElementById('_recp_add').value = json.data.step_2.recipient_address;
                            document.getElementById('_recp_prov').value = json.data.step_2.province_name;
                            document.getElementById('_recp_city').value = json.data.step_2.city_name;
                            document.getElementById('_recp_postal_code').value = json.data.step_2.postal_code;
                            document.getElementById('_recp_shipping_service').value = json.data.step_2.name;

                            var targetTbody = $('#table-summary tbody');

                            var targetRow = targetTbody.find('#exp_container');

                            var nameTd = $('<td>' + json.data.step_2.name + '</td>');
                            var priceTd = $('<td>' + json.data.step_2.formated_price + '</td>');
                            var priceTd_2 = $('<td>' + json.data.step_2.formated_price + '</td>');

                            targetRow.append(nameTd);
                            targetRow.append(priceTd);
                            targetRow.append(priceTd_2);

                            var gt = {{ $grandTotal; }};
                            var gts = gt + json.data.step_2.price;

                            $('#grandTotal').val(gts);
                            $('#display_grandtotal').text(formatNumber(gts));
                        }

                    } else {
                        alert(json.error_message);
                    }
                    // window.location.href = '/checkout#step4';
                    // location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle the error response
                }
            });
        }
    </script>


    <script>
        const custNameInput = document.getElementById('cust_name');
        const custEmailInput = document.getElementById('cust_email');
        const custPhoneInput = document.getElementById('cust_phone');
        const addressInput = document.getElementById('address');
        const btnContinue1 = document.getElementById('btn-continue-1');

        // Add event listeners to input fields
        // custNameInput.addEventListener('input', checkInputs);
        // custEmailInput.addEventListener('input', checkInputs);
        // custPhoneInput.addEventListener('input', checkInputs);
        // addressInput.addEventListener('input', checkInputs);

        // Function to check if all inputs are filled
        function checkInputs_old() {
            const custNameValue = custNameInput.value.trim();
            const custEmailValue = custEmailInput.value.trim();
            const custPhoneValue = custPhoneInput.value.trim();
            const addressValue = addressInput.value.trim();

            if (custNameValue !== '' && custEmailValue !== '' && custPhoneValue !== '' && addressValue !== '') {
                btnContinue1.removeAttribute('disabled');
            } else {
                btnContinue1.setAttribute('disabled', 'disabled');
            }
        }

        // Get the input elements
        const recpNameInput = document.getElementById('recp_name');
        const recpPhoneInput = document.getElementById('recp_phone');
        const recpAddressInput = document.getElementById('recp_address');
        const provinceInput = document.getElementById('province_destination');
        const cityInput = document.getElementById('city_destination');
        const postalCodeInput = document.getElementById('kode_pos');
        const ongkirListInput = document.getElementById('ongkir_list');
        const btnContinue2 = document.getElementById('btn-continue-2');

        // Add event listeners to input fields
        // recpNameInput.addEventListener('input', checkInputs2);
        // recpPhoneInput.addEventListener('input', checkInputs2);
        // recpAddressInput.addEventListener('input', checkInputs2);
        // provinceInput.addEventListener('change', checkInputs2);
        // cityInput.addEventListener('change', checkInputs2);
        // postalCodeInput.addEventListener('input', checkInputs2);
        // ongkirListInput.addEventListener('change', checkInputs2);

        // // Function to check if all inputs are filled
        // function checkInputs2() {
        //     const recpNameValue = recpNameInput.value.trim();
        //     const recpPhoneValue = recpPhoneInput.value.trim();
        //     const recpAddressValue = recpAddressInput.value.trim();
        //     const provinceValue = provinceInput.value;
        //     const cityValue = cityInput.value;
        //     const postalCodeValue = postalCodeInput.value.trim();
        //     const ongkirListValue = ongkirListInput.value;

        //     if (
        //         recpNameValue !== '' &&
        //         recpPhoneValue !== '' &&
        //         recpAddressValue !== '' &&
        //         provinceValue !== '' &&
        //         cityValue !== '' &&
        //         postalCodeValue !== '' &&
        //         ongkirListValue !== ''
        //     ) {
        //         btnContinue2.disabled = false;
        //     } else {
        //         btnContinue2.disabled = true;
        //     }
        // }

        let isProcessing = false;
        let start_date;
        let end_date;
        let voucher;
        let service_;

        function openModalVoucher() {
            $('#kuponModal').modal('show');
        }

        function getLocalDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }



        function store_code(code) {
            let csrfToken = $("meta[name='csrf-token']").attr("content");
            $.ajax({
                url: '/store-session',
                method: 'POST',
                data: {
                    _token: csrfToken,
                    value: code
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken // Include the CSRF token as a header
                },
                success: function(response) {
                    window.location.href = '/checkout#step4';
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle the error response
                }
            });
        }

        function callGetVouchersByCode() {
            let code = $('#input_kupon_').val();
            voucher = code;
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: '/vouchers/' + code, // Replace `/vouchers` with the actual URL of your route
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            let current_date = getLocalDate();
                            start_date = response.data.start_date;
                            end_date = response.data.end_date;

                            if (current_date < start_date || current_date > end_date) {
                                alert('Voucher not valid');
                            } else {
                                store_code(code);
                                $('#_voucher').val(code);
                            }
                        } else {
                            alert('Voucher not found');
                        }
                    },
                    error: function(xhr, status, error) {
                        reject(error);
                    }
                });
            });
        }

        function getCities(provinceId) {
            // Send an AJAX request to the getCities route
            $('#ongkir_list').empty();
            $('#ongkir_list').prop('readonly', true);
            $('#ongkir_list').append('<option value="">-- Pilih Layanan --</option>');
            $.ajax({
                url: '/getCities/' + provinceId,
                type: 'GET',
                success: function(response) {
                    // On success, populate the second select element with cities
                    var citiesSelect = $('.kota-tujuan');
                    // Clear existing options
                    citiesSelect.html('<option value="">-- ' + '{{ __('general.choose_city') }}' +
                        ' --</option>');
                    // Add new options based on the response data
                    for (var cityId in response) {
                        var cityName = response[cityId];
                        var option = $('<option>').val(cityId).text(cityName);
                        citiesSelect.append(option);
                    }
                },
                error: function(error) {
                    // Handle error if any
                    // console.log(error);
                }
            });
        }
        //ajax check ongkir

        function checkOngkir() {
            let token = $("meta[name='csrf-token']").attr("content");
            let city_destination = $('select[name=city_destination]').val();

            if (isProcessing) {
                return;
            }

            isProcessing = true;
            $('#ongkir_list').empty();
            $('#ongkir_list').prop('readonly', true);

            // Add the '--loading--' text while the data is being fetched
            $('#ongkir_list').append('<option value="">-- loading --</option>');

            $.ajax({
                url: "/ongkir",
                data: {
                    _token: token,
                    city_origin: 105,
                    city_destination: city_destination,
                    courier: ['jne'],
                    weight: 1000,
                },
                dataType: "JSON",
                type: "POST",
                success: function(response) {
                    isProcessing = false;
                    if (response) {
                        // Clear the '--loading--' option
                        $('#ongkir_list').empty();

                        // Add the '-- please select --' option as the first option
                        $('#ongkir_list').append('<option value="">-- please select --</option>');

                        response[0].costs.forEach(function(cost) {
                            var service = cost.service;
                            var description = cost.description;
                            var costValue = cost.cost[0].value;

                            var optionText = service + '\n' + '(' + description + ')' + '\t' + 'Rp ' + formatNumber(costValue);
                            $('#ongkir_list').append('<option value="' + service + "_" + costValue + '">JNE ' + optionText + '</option>');
                        });

                        $('#ongkir_list').prop('readonly', false);
                    }
                },
                error: function(error) {
                    isProcessing = false;
                    // console.log(error);
                }
            });
        }

        function callGetCityName(provinceId, cityId) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: '/getCityName/' + provinceId + '/' +
                        cityId, // Replace `/getCityName` with the actual URL of your route
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        resolve(response);
                    },
                    error: function(xhr, status, error) {
                        reject(error);
                    }
                });
            });
        }

        function callGetProvinceName(id) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: '/getProvinceName/' +
                        id, // Replace `/getProvinceName` with the actual URL of your route
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        resolve(response);
                    },
                    error: function(xhr, status, error) {
                        reject(error);
                    }
                });
            });
        }

        function getInputValues() {
            var inputs = {};

            // Get CSRF token
            var csrfToken = $('input[name="_token"]').val();
            inputs._token = csrfToken;

            // Get customer information
            var custName = $('#cust_name').val();
            var custEmail = $('#cust_email').val();
            var custPhone = $('#cust_phone').val();
            var custAddress = $('#address').val();

            inputs.cust_name = custName;
            inputs.cust_email = custEmail;
            inputs.cust_phone = custPhone;
            inputs.cust_address = custAddress;

            // Get shipping information
            var recpName = $('#recp_name').val();
            var recpPhone = $('#recp_phone').val();
            var recpAddress = $('#recp_address').val();
            var provinceDestination = $('#province_destination').val();
            $('#_province').val(provinceDestination);
            var cityDestination = $('#city_destination').val();
            $('#_city').val(cityDestination);
            var postalCode = $('#kode_pos').val();
            var ongkirList = localStorage.getItem('ongkirList'); //$('#ongkir_list').val();
            inputs.recp_name = recpName;
            inputs.recp_phone = recpPhone;
            inputs.recp_address = recpAddress;
            inputs.province_destination = provinceDestination;
            inputs.city_destination = cityDestination;
            inputs.kode_pos = postalCode;
            inputs.ongkir_list = ongkirList;

            // Get payment method
            var paymentMethod = $('input[name="payment_method"]:checked').val();
            inputs.payment_method = paymentMethod;


            const ongkirList_ = inputs.ongkir_list;
            const separatedValues = ongkirList_.split("_");
            const service = separatedValues[0];
            const service_price = parseInt(separatedValues[1]);

            // var targetTbody = $('#table-summary tbody');
            // // var targetRow = targetTbody.find('#exp_container');
            // targetRow.empty();

            // var newRow = $('' +
            //     '<td>JNE ' + service + '</td>' +
            //     '<td></td>' +
            //     '<td>' + formatNumber(service_price) + '</td>' +
            //     '');

            // //get grandtotal
            // var gt = <?php echo $grandTotal; ?>;
            // var gts = gt + service_price;
            // $('#grandTotal').val(gts);
            // $('#display_grandtotal').text(formatNumber(gts));
            // Find the <tbody> element of the table with ID "table-summary"
            var targetTbody = $('#table-summary tbody');

            // Find the <tr> element with ID "exp_container" inside the target <tbody> element
            var targetRow = targetTbody.find('#exp_container');

            // Append the new row to the target <tr> element
            targetRow.append(newRow);

            $('#_service').val(service);
            $('#_service_price').val(service_price);

            // Update table cells with input values
            $('#_cust_name').val(inputs.cust_name);
            $('#_cust_email').val(inputs.cust_email);
            $('#_cust_phone').val(inputs.cust_phone);
            $('#_cust_address').val(inputs.cust_address);

            $('#_recp_name').val(inputs.recp_name);
            $('#_recp_phone').val(inputs.recp_phone);
            $('#_recp_add').val(inputs.recp_address);

            $('#_recp_postal_code').val(inputs.kode_pos);
            $('#_recp_shipping_service').val("JNE " + service);
            $('#label_payment_method').val("Selected payment method : " + inputs.payment_method);

            callGetCityName(inputs.province_destination, inputs.city_destination)
                .then(function(cityResponse) {
                    // Handle the city response data
                    // // console.log(cityResponse);
                    $('#_recp_city').val(cityResponse.toString());
                })
                .catch(function(error) {
                    // Handle the error
                    console.error(error);
                });

            callGetProvinceName(inputs.province_destination)
                .then(function(provinceResponse) {
                    // Handle the province response data
                    // // console.log(provinceResponse);
                    $('#_recp_prov').val(provinceResponse.toString());
                })
                .catch(function(error) {
                    // Handle the error
                    console.error(error);
                });

            // console.log(inputs);
        }

        function removeVoucher() {
            $.ajax({
                url: "/remove-voucher",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#_voucher').val("-");
                    window.location.href = '/checkout#step4';
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle the error response
                    console.error(error);
                }
            });
        }

        function collectInputValues() {
            var collectedValues = {};
            $('input[name]').each(function() {
                var inputName = $(this).attr('name');
                var inputValue = $(this).val();
                collectedValues[inputName] = inputValue;
            });
            collectedValues['cust_address'] = $('textarea[name="cust_address"]').val();
            collectedValues['recp_address'] = $('textarea[name="recp_address"]').val();
            collectedValues['province_destination'] = $('#province_destination').val();
            collectedValues[''] = $('#city_destination').val();
            collectedValues['ongkir_list'] = $('#ongkir_list').val();

            collectedValues['payment_method'] = $('input[name="payment_method"]:checked').val();

            delete collectedValues['input_search'];
            delete collectedValues['ongkir_list'];
            delete collectedValues['province_destination'];
            delete collectedValues['city_destination'];
            delete collectedValues[''];

            if (collectedValues['input_kupon'].trim() === "") {
                collectedValues['input_kupon'] = "-";
            }

            if (collectedValues['input_kupon_'].trim() === "") {
                collectedValues['input_kupon_'] = "-";
            }

            return collectedValues;
        }

        function validateObject(obj) {
            return !Object.values(obj).some(value => {
                return value === null || value === undefined || value === '';
            });
        }

        function paid() {
            // var collectedInputs = collectInputValues();
            // var validated = validateObject(collectedInputs);
            // console.log(collectedInputs);
            // console.log(validated);
            // Email validation regex pattern
            // const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            // const custEmailInput = document.getElementById('cust_email');
            // const custEmailValue = custEmailInput.value.trim();

            let csrfToken = $("meta[name='csrf-token']").attr("content");
            let formData = new FormData();
            formData.append('_token', csrfToken);

            const appliedVoucherCodeElement = document.getElementById('appliedVoucherCode');
            if (appliedVoucherCodeElement) {
                formData.append('voucher', appliedVoucherCodeElement.value);
            } else {
                formData.append('voucher', '-');
            }

            // if (validated) {
                // if(emailRegex.test(custEmailValue)){
                    $.ajax({
                        url: '/checkout/finish',
                        type: 'POST',
                        contentType: 'application/json',
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // Include the CSRF token as a header
                        },
                        processData: false, // Prevent jQuery from processing the data
                        contentType: false, // Prevent jQuery from setting the content type
                        success: function(data) {
                            console.log(data);
                            // console.log(data.data.payment);
                            // if(data.data.payment === "Bank Transfer"){
                                window.location.href = '/finish/' + data.data.trans_code;
                            // }

                        },
                        error: function(error) {
                            console.error('Error:', error);
                        }
                    });
                // } else {
                //     alert('Email not valid');
                // }
            // }
        }

        function formatNumber(number) {
            return number.toLocaleString('id-ID');
        }


        function getSelectedOngkirList() {
            var data = $('#ongkir_list').val();
            localStorage.setItem('ongkirList', data);
        }
    </script>
@endpush
