@extends('layouts.app')

@section('content')
    <section class="" style="margin-top: 140px">
        <div class="container-fluid mt-3 mb-4 ">
            <div class="row mt-4 mb-4">
                <div class="col-lg-12 col-md-6 col-6 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <h3>{{ __('general.cart') }}</h3>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center row mb-4">
                <div class="col-md-7">
                    @php
                        $total_items = 0;
                        $total_price = 0;
                    @endphp

                    @foreach ($data as $item)
                        @php
                            $image = $item->file_name == null || $item->file_name == '' ? 'images/no-image.png' : 'img_product/' . $item->file_name;

                            $total_items += $item->qty;
                            $total_price += $item->price;

                        @endphp

                        <div class="row p-2 bg-white border rounded mt-2">
                            <div class="col-md-2 mt-1"><img class="img-fluid img-responsive rounded product-image"
                                    src="{!! imageDir() . $image !!}"></div>
                            <div class="col-md-7 mt-1">
                                <h5>{{ $item->product_name . ' - ' . $item->color_name }}</h5>
                                <p class="text-justify text-truncate para mb-0">Size {{ $item->size }}<br></p>
                                <p class="text-justify text-truncate para mb-0">Qty {{ $item->qty }} Items<br><br></p>
                                <h5 class="text-justify text-truncate para mb-0">Rp. {{ formatNumber($item->price) }}</h5>
                            </div>
                            <div class="align-items-center align-content-center col-md-3 border-left mt-1">
                                <div class="d-flex flex-column mt-0">

                                    <div class="input-group mb-3">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="button-addon1">
                                            <i class="fa fa-arrow-up"></i>
                                        </button>
                                        <input type="number" class="form-control" placeholder="{{ $item->qty }}" value="{{ $item->qty }}"
                                            aria-label="Example text with button addon" aria-describedby="button-addon1">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="button-addon2"><i class="fa fa-arrow-down"></i></button>
                                    </div>

                                    </button>


                                    <button class="btn btn-outline-danger btn-sm mt-2" type="button">Remove From
                                        Cart</button>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
                <div class="col-md-3">
                    <div class="card text-center mt-2">
                        <div class="card-header">
                            Summary
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Total Items: {{ $total_items }}</h5>
                            <p class="card-text">Total Price: Rp. {{ formatnumber($total_price) }}</p>
                            <a href="#" class="d-block btn btn-success">Checkout</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>
@endsection
