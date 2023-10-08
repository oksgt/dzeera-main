@extends('layouts.app')

@section('content')
    <section class="" style="margin-top: 140px">
        <div class="container-fluid mt-3 mb-4 ">
            <div class="row mt-4 mb-4 ">
                <div class="col-lg-12 col-md-6 col-6 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <h3>{{ __('general.myOrder') }}</h3>
                    </div>
                </div>
            </div>
            <div class="row ">
                <div class="d-flex justify-content-center row mb-4">
                    @foreach ($transactions as $item)
                        <div class="card mb-3" style="max-width: 540px;">
                            <div class="row g-0">
                                <div class="col-md-6">
                                    <div class="card-body">
                                        <h5 class="card-title">Trans Code: {{ $item->trans_number }}</h5>
                                        @php
                                            $dateString = $item->created_at;
                                            $dateTime = new DateTime($dateString);
                                            $formattedDate = $dateTime->format('d F Y H:i:s');
                                        @endphp
                                        <p class="card-text"><small class="text-body-secondary">Date: {{ $formattedDate }}</small></p>
                                        <h5 class="card-title">Total: {{ formatCurrency($item->final_price) }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex justify-content-center align-items-center text-center ">
                                    <div class="row">
                                        <div class="col-12">

                                            @php

                                            $currentDateTime = new DateTime(); // Get the current date and time
                                            $givenDateTime = new DateTime($item->max_time); // Convert the given time string to a DateTime object

                                            @endphp

                                            @if ($item->payment_method == 'Bank Transfer')

                                                @if ($givenDateTime < $currentDateTime )

                                                    <h4><span class="badge bg-danger">Cancelled</span></h4>

                                                @else
                                                    @if ($item->trans_status == 'paid')
                                                        <h4><span class="badge bg-success">Paid</span></h4>
                                                    @else
                                                        <h4><span class="badge bg-warning">Unpaid</span></h4>
                                                    @endif
                                                @endif

                                            @else
                                                <h4><span class="badge bg-{{ $item->trans_status == 'paid' ? 'success' : 'warning' }}">{{ ucwords($item->trans_status) }}</span></h4>
                                            @endif


                                        </div>
                                        <div class="col-12">
                                            <a class="btn text-success" href="{{ route('my-order-detail', ['code' => $item->trans_number]) }}">Detail</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        &nbsp;
                    @endforeach
                </div>
            </div>

        </div>

    </section>
@endsection


@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script></script>
@endpush
