@extends('layouts.app')

@section('content')
    <section class="" style="margin-top: 140px">
        <div class="container-fluid mt-3 mb-4 ">
            <div class="row mt-4 mb-4">
                <div class="col-lg-12 col-md-6 col-6 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <h3>{{ __('general.yourWishlist') }}</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">

                    <div class="row row-cols-2 row-cols-sm-4 row-cols-md-6 g-3 mt-0" id="product-container">
                        @foreach ($data as $item)
                            @php
                                $image = $item->file_name == null || $item->file_name == '' ? 'images/no-image.png' : 'img_product/' . $item->file_name;
                            @endphp
                            @if ($item->base_price > 0)
                                <div class="col ">
                                    <div class="product-card ">
                                        <div class="special-img position-relative overflow-hidden "
                                            style="border-radius: 10px 10px 0px 0px; z-index: 1 !important;">
                                            <img src="{!! imageDir() . $image !!}" class="w-100">
                                        </div>
                                        <div class="justify-content-center p-2 product-card-info mt-1 mb-3">
                                            <div style="text-align: center">
                                                <h5 class="text-capitalize mt-1 mb-1" style="font-weight: 100">
                                                    {{ $item->product_name . ' - ' . $item->color_name }}
                                                </h5>
                                                <span class="d-inline-block text-muted "
                                                    style="text-decoration: line-through; ">Rp.
                                                    {{ formatNumber($item->base_price) }}</span>
                                                <span class="d-inline-block" style="font-weight: 200; color: #e5345b;">Rp.
                                                    {{ formatNumber($item->price) }}</span>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <form action="{{ url('/wishlist') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_item_id"
                                                        value="{{ $item->item_id }}">
                                                    <input type="hidden" name="product_item_slug"
                                                        value="{{ $item->item_slug }}">
                                                    <input type="hidden" name="product_name"
                                                        value="{{ $item->product_name }}">
                                                    <input type="hidden" name="color_name"
                                                        value="{{ $item->color_name }}">
                                                    <button type="submit"
                                                        class="float-left btn mt-1 btn-outline-transparent "
                                                        style="width: 100% !important; ">{{ __('general.remove') }}</button>
                                                </form>

                                                <a href="#" class="float-right btn mt-1 btn-outline-transparent " id="btn-buy"
                                                    style="width: 100% !important; font-weight: bolder; color: #e5345b;">{{ __('general.buy') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
