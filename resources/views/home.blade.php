@extends('layouts.app')

@section('content')
    <div id="" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach ($banner as $index => $item)
                <div class="carousel-item @if ($index === 0) active @endif ">
                    <img src="{!! imageDir() . 'banner/' . $item->file_name !!}" class="d-block w-100" alt="{{ $item->file_name }}">
                </div>
            @endforeach
        </div>
    </div>

    <section id="special" class="">
        <div class="container mt-3">
            <div class="title text-center py-3">
                <h3 class="position-relative d-inline-block">New Arrivals</h3>
            </div>
            <div class="">
                <div class="row">
                    <div class="splide" aria-labelledby="carousel-heading" id="slide_new_arrivals">
                        <div class="splide__track">
                            <div class="splide__list">
                                <div class="col-sm-4 splide__slide m-2">
                                    <div class="product-card ">
                                        <div class="special-img position-relative overflow-hidden "
                                            style="border-radius: 10px 10px 0px 0px; z-index: 1 !important;">
                                            <img src="images/special_product_1.jpg" class="w-100">
                                        </div>
                                        <div class="justify-content-center p-2 product-card-info mt-1 mb-3">
                                            <h5 class="text-capitalize mt-1 mb-1">gray shirt </h5>
                                            <span class="d-inline-block text-muted "
                                                style="text-decoration: line-through; ">Rp 500.000</span>
                                            <span class="fw-bold d-inline-block" style="color: #e5345b;">Rp 399.000</span>
                                            <div class="d-flex justify-content-between">
                                                <a href="#" class="float-left btn mt-1 btn-outline-transparent "
                                                    style="width: 100% !important; ">Whistlist
                                                </a>
                                                <a href="#" class="float-right btn mt-1 btn-outline-transparent "
                                                    style="width: 100% !important; font-weight: bolder; color: #e5345b;">Buy
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <a href="#" class="d-block btn mt-1 btn-outline-transparent "
                        style="width: 100% !important; border: 1px solid inherit !important; max-width: 100px !important;">View
                        All</a>
                </div>
            </div>
        </div>
    </section>

    <div class="container mt-4">
        <div class="row">
            <div class="splide" aria-labelledby="carousel-heading" id="slide_category">
                <div class="splide__track">
                    <div class="splide__list">

                        @php
                            $categories = getAllCategoriesByBrand();
                        @endphp

                        @foreach ($categories as $index => $item)
                            @php
                                $image = ($item->image == null || $item->image == "") ? 'images/no-image.png' : 'banner/' . $item->image;
                            @endphp
                            <div class="col-sm-4 splide__slide m-2">
                                <div class="card" style="border: none !important">
                                    <div class="ratio ratio-1x1 rounded-circle overflow-hidden">
                                        <img src="{!! imageDir() . $image !!}" class="card-img-top img-cover " width="70"
                                            height="70" alt="">
                                    </div>
                                    <div class="card-body  p-0 text-center">
                                        <h6 class="card-title card-title-category mt-2">{{$item->category_name}}</h6>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
