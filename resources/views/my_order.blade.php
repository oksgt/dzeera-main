@extends('layouts.app')

@section('content')
    <section class="" style="margin-top: 140px">
        <div class="container-fluid mt-3 mb-4 ">
            <div class="row mt-4 mb-4 ">
                <div class="col-lg-12 col-md-12 col-12 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <h3>{{ __('general.myOrder') }}</h3>
                    </div>
                </div>
            </div>
            <div class="row p-2 gx-5 d-flex justify-content-center">
                @foreach ($transactions as $item)
                        <div class="col-lg-8">
                            <div class="card mb-3" style="">
                                <div class="row">
                                    <div class=" col-sm-10 col-md-10">
                                        <div class="card-body p-1">
                                            @php
                                                $dateString = $item['created_at'];
                                                $dateTime = new DateTime($dateString);
                                                $formattedDate = $dateTime->format('d F Y');
                                            @endphp
                                            <div class="text-dark" style="font-size: 14px;">
                                                <span><i class="fa fa-shopping-bag" style="color: #e30c83 !important"></i> Shopping {{ $formattedDate }}</span>


                                                @if ($item['trans_status'] == 'paid')
                                                    <span class="badge rounded-pill text-dark" style="background-color: #d6ffde; font-size: 12px; font-weight: 200;">
                                                        {{ ucwords($item['payment_status']) }}
                                                    </span>
                                                @else
                                                    <span class="badge rounded-pill text-dark bg-{{ $item['trans_status'] == 'paid' ? 'success' : 'warning' }}"
                                                    style="font-size: 12px; font-weight: 200;"
                                                    >
                                                    {{ ucwords($item['payment_status']) }}
                                                    </span>
                                                @endif


                                                <span >Trans code #{{ $item['trans_number'] }}</span>
                                            </div>

                                            <div class="mt-2 text-dark" style="font-size: 14px;">
                                                <span><i class="far fa-user" style="color: #e30c83 !important"></i> {{ $item['recp_name'] }} ({{ $item['recp_phone'] }})</span>
                                            </div>

                                            @foreach ($item['detail'] as $itemDetail)
                                                @if ($loop->first)
                                                    @php
                                                        $image = $itemDetail->file_name == null || $itemDetail->file_name == '' ? 'images/no-image.png' : 'img_product/' . $itemDetail->file_name;
                                                    @endphp
                                                    <div class="card mt-2 mb-0 border-0" >
                                                        <div class="row g-0">
                                                            <div class="col-2">
                                                                <img src="{!! imageDir() . $image !!}" class="img-fluid rounded w-100">
                                                            </div>
                                                            <div class="col-10">
                                                                <div class="card-body p-0" style="padding-left: 16px !important">
                                                                    <p class="card-text" style="margin-bottom: 0px !important">{{ $itemDetail->product_name }}</p>
                                                                    <p class="card-text "><small class="text-body-secondary">{{ $itemDetail->qty }} pcs x Rp{{ formatNumber($itemDetail->price) }}</small></p>

                                                                    @if (count($item['detail']) > 1)
                                                                    <p class="card-text small" style="margin-bottom: 0px !important">+{{ count($item['detail'])-1}} other product(s)</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach

                                            {{-- <h5 class="card-title">Trans Code: {{ $item'trans_number' }}</h5>
                                            <p class="card-text"><small class="text-body-secondary">Date: {{ $formattedDate }}</small></p>
                                            <h5 class="card-title">Total: {{ formatCurrency($item->final_price) }}</h5> --}}

                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-2 d-flex justify-content-center align-items-center text-center ">
                                        <div class="row">
                                            <div class="col-12">
                                                <span>
                                                    Total Rp{{ formatNumber($item['final_price']) }}
                                                </span>
                                                <a class="btn" style="color: #e30c83 !important" href="{{ route('my-order-detail', ['code' => $item['trans_number']]) }}">Detail</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        &nbsp;
                    @endforeach
            </div>

        </div>

    </section>
@endsection


@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script></script>
@endpush
