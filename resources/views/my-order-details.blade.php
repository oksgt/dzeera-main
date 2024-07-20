@extends('layouts.app')

@section('content')
    <section class="" style="margin-top: 140px">
        <div class="container-fluid mt-3 mb-4 ">
            <div class="row mt-4 mb-4 ">
                <div class="col-lg-12 col-md-12 col-12 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <h3>{{ __('general.myOrder') }} #{{ $transaction->trans_number }}</h3>
                    </div>
                </div>
            </div>
            <div class="row ">
                <div class="">
                    <div class="tab-pane show" role="tabpanel" id="step4" aria-labelledby="step4-tab">
                        @php
                            $currentDateTime = new DateTime(); // Get the current date and time
                            $givenDateTime = new DateTime($transaction->max_time); // Convert the given time string to a DateTime object
                        @endphp

                        @if ($transaction->payment_method == 'Bank Transfer')
                            @if ($givenDateTime < $currentDateTime)
                                <div class="row">
                                    <div class="col-md-12 d-flex align-items-center justify-content-center">
                                        <div class="alert alert-danger">{{ __('general.cancelled') }}</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 offset-md-3 text-center">
                                        <div class="alert alert-danger" role="alert">
                                            {{ __('general.order_cancel') }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-md-12 d-flex align-items-center justify-content-center">
                                        <div
                                            class="alert alert-{{ $transaction->trans_status == 'paid' ? 'success' : 'warning' }}">
                                            {{ strtoupper($transaction->trans_status) }}</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 offset-md-3 text-center">
                                        <div class="alert alert-warning" role="alert">
                                            {{ __('general.payment_due_date') }} : {{ $transaction->max_time }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                        <div class="row">

                            <div class="col-lg-6 col-md-12 col-sm-12 table-responsive mt-3 p-3 pt-0">

                                @if ($transaction->payment_status == 'Success')
                                    <div class="alert alert-success d-flex align-items-center" role="alert">
                                        <div>
                                            <i class="fa fa-circle-check"> </i>
                                            {{ __('general.payment_success') }}
                                        </div>
                                    </div>
                                @elseif ($transaction->payment_status !== 'Success')
                                    <div id="snap-container" class="w-100" style="box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); border-radius: 10px; ">
                                    </div>
                                @endif


                            </div>

                            <div class="col-lg-6 col-md-12 col-sm-12 table-responsive mt-3">
                                <div class="card">
                                    <div class="card-header" style="background: #fbdce1 !important">
                                        {{ __('general.customer_information') }}</div>
                                    <div class="card-body table-responsive">
                                        <table class="table table-sm small text-muted">
                                            <tr>
                                                <td>{{ __('general.name') }}</td>
                                                <td>
                                                    <input type="text" id="_cust_name" name="_cust_name"
                                                        class="readonly-input" readonly
                                                        value="{{ $transaction->cust_name }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('general.email_address') }}</td>
                                                <td>
                                                    <input type="text" id="_cust_email" name="_cust_email"
                                                        class="readonly-input" readonly
                                                        value="{{ $transaction->cust_email }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('general.phone') }}</td>
                                                <td>
                                                    <input type="text" id="_cust_phone" name="_cust_phone"
                                                        class="readonly-input" readonly
                                                        value="{{ $transaction->cust_phone }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('general.address') }}</td>
                                                <td>
                                                    <input type="text" id="_cust_address" name="_cust_address"
                                                        class="readonly-input" readonly
                                                        value="{{ $transaction->cust_address }}">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header" style="background: #fbdce1 !important">
                                        {{ __('general.shipping_information') }}</div>
                                    <div class="card-body table-responsive">
                                        <table class="table table-sm small text-muted">
                                            <tr>
                                                <td>{{ __('general.recipient_name') }}</td>
                                                <td>
                                                    <input type="text" id="_recp_name" name="_recp_name"
                                                        class="readonly-input" readonly
                                                        value="{{ $transaction->recp_name }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('general.recipient_phone') }}</td>
                                                <td>
                                                    <input type="text" id="_recp_phone" name="_recp_phone"
                                                        class="readonly-input" readonly
                                                        value="{{ $transaction->recp_phone }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('general.recipient_address') }}</td>
                                                <td>
                                                    <input type="text" id="_recp_add" name="_recp_add"
                                                        class="readonly-input" readonly
                                                        value="{{ $transaction->recp_address }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('general.province') }}</td>
                                                <td>
                                                    <input type="text" id="_recp_prov" name="_recp_prov"
                                                        class="readonly-input" readonly
                                                        value="{{ $transaction->province_name }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('general.city') }}</td>
                                                <td>
                                                    <input type="text" id="_recp_city" name="_recp_city"
                                                        class="readonly-input" readonly
                                                        value="{{ $transaction->city_name }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('general.postal_code') }}</td>
                                                <td>
                                                    <input type="text" id="_recp_postal_code" name="_recp_postal_code"
                                                        class="readonly-input" readonly
                                                        value="{{ $transaction->cust_address }}">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('general.shipping_service') }}</td>
                                                <td>
                                                    <input type="text" id="_recp_shipping_service"
                                                        name="_recp_shipping_service" class="readonly-input" readonly
                                                        value="JNE {{ $transaction->expedition_service_type }}">
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

                                <div class="card bg bg-white mt-3">
                                    <div class="card-header" style="background: #fbdce1 !important">Item List</div>
                                    <div class="card-body">
                                        <table class="table table-sm small" id="table-summary">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('general.product_name') }}</th>
                                                    <th>{{ __('general.quantity') }}</th>
                                                    <th>{{ __('general.price') }}</th>
                                                    <th>{{ __('general.total_price') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $grandTotal = 0; // Initialize grand total variable
                                                foreach ($trans_detail as $index => $row):
                                                    $grandTotal += $row->total_price; // Accumulate the total price for each row
                                                ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo $row->product_name . ' - ' . $row->color_name . ' - ' . $row->size; ?></td>
                                                    <td><?php echo $row->qty; ?></td>
                                                    <td><?php echo formatNumber($row->price); ?></td>
                                                    <td><?php echo formatNumber($row->total_price); ?></td>
                                                </tr>
                                                <?php endforeach; ?>

                                                <tr>
                                                    <td></td>
                                                    <td>JNE <?php echo $transaction->expedition_service_type; ?></td>
                                                    <td>1</td>
                                                    <td><?php echo formatNumber($transaction->shipping_cost); ?></td>
                                                    <td><?php echo formatNumber($transaction->shipping_cost); ?></td>
                                                </tr>

                                                @if (!empty($voucher))
                                                    <tr>
                                                        <td></td>
                                                        <td>Voucher Id</td>
                                                        <td>
                                                            @if ($voucher->is_percent == 'y')
                                                                @php
                                                                    $voucher->value . ' %';
                                                                @endphp
                                                            @else
                                                                @php
                                                                    $voucher->value . ' Off';
                                                                @endphp
                                                            @endif
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>

                                        <small class="text-muted" id="label_payment_method"></small>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="d-flex justify-content-center mt-3">
                            <a class="btn btn-secondary previous" href="{{ url()->previous() }}"><i
                                    class="fas fa-angle-left"></i>
                                {{ __('general.back') }}</a>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </section>
@endsection


@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            if ('{{ $transaction->payment_method }}' == 'Merchant') {
                if('{{ $transaction->payment_status }}' !== 'Success'){
                    window.snap.embed('{{ $transaction->snapToken }}', {
                        embedId: 'snap-container'
                    });
                }
            }
        });
    </script>
@endpush
