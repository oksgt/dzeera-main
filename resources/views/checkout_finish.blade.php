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
                        <p>Invoice No. #123</p>
                        <p class="display-4 text-danger" style="font-size: 30px">Unpaid</p>
                    </div>
                </div>
                <div class="col  text-center">
                    <p>{{ __('general.total_transaction') }}</p>
                    <p class="display-4 text-dark" style="font-size: 30px">Rp. 123.000,00</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 offset-md-3 text-center">
                    <div class="alert alert-danger" role="alert">
                        {{ __('general.payment_due_date') }} :
                    </div>
                </div>
            </div>



            <div class="row mt-4 mb-2">
                <div class="col-md-6 offset-md-3 text-center">
                    <p>{{ __('general.choose_list') }}</p>

                    <ul class="list-group">
                        @foreach($bank as $account)
                            <li class="list-group-item border-0">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $account->bank_name }}</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">{{ __('general.account_number') }} {{ $account->account_number }}</h6>
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
        </div>

    </section>
@endsection


@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endpush
