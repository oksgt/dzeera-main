@extends('layouts.app')

@section('content')
    <section class="" style="margin-top: 140px">

        <div class="container-fluid mt-3  ">
            <div class="row mt-4 mb-4 ">
                <div class="col-lg-12 col-md-6 col-6 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <h3>{{ __('general.payment_info') }}</h3>
                    </div>
                </div>
            </div>

            <div class="row ">
                <div class="col  text-center">
                    <div class="col">
                        <p>Invoice No. #{{ $code }}</p>
                        <p class="display-4 text-danger" style="font-size: 30px">Unpaid</p>
                    </div>
                </div>
                <div class="col  text-center">
                    <p>{{ __('general.total_transaction') }}</p>
                    <p class="display-4 text-dark" style="font-size: 30px">Rp. {{ formatNumber($finalPrice) }}</p>
                </div>
            </div>

            @if ($payment_method == 'Bank Transfer')
                <div class="row">
                    <div class="col-md-6 offset-md-3 text-center">
                        <div class="alert alert-danger" role="alert">
                            {{ __('general.payment_due_date') }} : {{ $maxTime }}
                        </div>
                    </div>
                </div>

                <div class="row mt-4 mb-2">
                    <div class="col-md-6 offset-md-3 text-center">
                        <p>{{ __('general.choose_list') }}</p>

                        <ul class="list-group">
                            @foreach ($bank as $account)
                                <li class="list-group-item border-0">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $account->bank_name }}</h5>
                                            <h6 class="card-subtitle mb-2 text-muted">{{ __('general.account_number') }}
                                                {{ $account->account_number }}</h6>
                                            <p class="text-muted">a/n</p>
                                            <h6 class="card-text">{{ $account->account_name }}</h6>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>

                <div class="row mb-4">
                    <div class="col d-flex justify-content-center">
                        <a href="./" class="btn btn-sm btn-primary finish">{{ __('general.back_to_home') }}</a>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-6 offset-md-3 text-center">
                        <div id="snap-container" class="w-100"></div>
                    </div>
                </div>
            @endif


        </div>

    </section>
@endsection


@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            if('{{ $payment_method  }}' == 'Merchant'){
                window.snap.embed('{{ $snapToken }}', {
                    embedId: 'snap-container',
                    onSuccess: function(result) {
                        /* You may add your own implementation here */
                        alert("payment success!");
                        console.log(result);
                    },
                    onPending: function(result) {
                        /* You may add your own implementation here */
                        alert("wating your payment!");
                        console.log(result);
                    },
                    onError: function(result) {
                        /* You may add your own implementation here */
                        alert("payment failed!");
                        console.log(result);
                    },
                    onClose: function() {
                        /* You may add your own implementation here */
                        alert('you closed the popup without finishing the payment');
                    }
                });
            }
        });
    </script>
@endpush
