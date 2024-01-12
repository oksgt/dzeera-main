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
                                @php
                                    $newArrivals = getNewArrivals();
                                @endphp
                                @foreach ($newArrivals as $item)
                                    @php
                                        $image = $item->file_name == null || $item->file_name == '' ? 'images/no-image.png' : 'img_product/' . $item->file_name;
                                    @endphp
                                    @if ($item->base_price > 0)
                                        <div class="col-sm-4 splide__slide m-2">
                                            <div class="product-card ">
                                                <div class="special-img position-relative overflow-hidden "
                                                    style="border-radius: 10px 10px 0px 0px; z-index: 1 !important;">
                                                    <img src="{!! imageDir() . $image !!}" class="w-100">
                                                </div>
                                                <div class="justify-content-center p-2 product-card-info mt-1 mb-3">
                                                    <div style="text-align: left;">
                                                        <h5 class="text-capitalize mt-1 mb-1" style="font-weight: 300">
                                                            {{ $item->product_name . ' - ' . $item->color_name }}
                                                        </h5>
                                                        <span class="d-inline-block text-muted "
                                                            style="text-decoration: line-through; ">Rp.
                                                            {{ formatNumber($item->base_price) }}</span>
                                                        <span class="d-inline-block"
                                                            style="font-weight: 200; color: #e5345b;">Rp.
                                                            {{ formatNumber($item->price) }}</span>
                                                    </div>

                                                    <div class="col-12 d-flex justify-content-between">
                                                        <div class="col-6">
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
                                                                    style="width: 100% !important; ">Wishlist</button>
                                                            </form>
                                                        </div>
                                                        <div class="col-6">
                                                            @php
                                                                $slug = $item->slug."__".preg_replace("/\s+/", "_", $item->color_name);
                                                                $slug = strtolower($slug);
                                                            @endphp

                                                            <a href="{{ route('product', [ 'productslug' => $slug ]) }}"
                                                                class="float-right btn mt-1 btn-outline-transparent "
                                                                id="btn-buy"
                                                                style="width: 100% !important; font-weight: bolder; color: #e5345b;">{{ __('general.buy') }}
                                                            </a>
                                                        </div>
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
                <div class="row justify-content-center">
                    <a href="{{ route('newArrivals', ['brandslug' => session('active-brand-name')]) }}"
                        class="d-block btn mt-1 btn-outline-transparent "
                        style="width: 100% !important; border: 1px solid inherit !important; max-width: 150px !important;">{{ __('general.viewAll') }}</a>
                </div>
            </div>
        </div>
    </section>
    <hr class="style14">
    <div class="container mt-4 p-1">
        <div class="row">
            <div class="splide" aria-labelledby="carousel-heading" id="slide_category">
                <div class="splide__track">
                    <div class="splide__list">
                        @php
                            $categories = getAllCategoriesByBrand();
                        @endphp

                        @foreach ($categories as $index => $item)
                            @php
                                $image = $item->image == null || $item->image == '' ? 'images/no-image.png' : 'images/category/' . $item->image;
                            @endphp
                            <div class="col-sm-4 splide__slide m-2">
                                <div class="card" style="border: none !important">
                                    <div class="ratio ratio-1x1 rounded-circle overflow-hidden">
                                        <img src="{!! imageDir() . $image !!}" class="card-img-top img-cover " width="70"
                                            height="70" alt="">
                                    </div>
                                    <div class="card-body  p-0 text-center">
                                        {{-- <h6 class="card-title card-title-category mt-2 ">{{ $item->category_name }}</h6> --}}
                                        <a style="text-decoration: none" href="{{ route('ProductByCategory', ['categoryslug'=> $item->slug, 'brandslug' => session('active-brand-name'), 'page' => 1]) }}" class="card-title card-title-category mt-2">{{ $item->category_name }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="style14">
    @php
        $highlighted_product = getHighlightedProduct();
        $highlighted_categories = getHighlightedCategories();
        $videos = getVids();
    @endphp

    @if ($highlighted_product && isset($highlighted_product[0]))
        @php
            $image = $highlighted_product[0]->file_name == null || $highlighted_product[0]->file_name == '' ? 'images/no-image.png' : 'img_product/' . $highlighted_product[0]->file_name;
        @endphp

        <div class="container mt-0 p-3">
            <div class="row col">
                <div class="card mb-0 border-0">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{!! imageDir() . $image !!}" class="img-fluid rounded" alt="...">
                        </div>
                        <div class="col-md-8 d-none d-md-block">
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <hr class="style14">
    @if ($highlighted_categories && isset($highlighted_categories[0]))
        <section id="special" class="">
            <div class="container">
                <div class="title text-center py-3">
                    <h3 class="position-relative d-inline-block">{{ $highlighted_categories[0]->category_name }}</h3>
                </div>
                <div class="">
                    <div class="row">
                        <div class="splide" aria-labelledby="carousel-heading" id="slide_category_1">
                            <div class="splide__track">
                                <div class="splide__list">

                                    @php
                                        $productByCategory = getProductByCategoryIndex($highlighted_categories[0]->id);
                                    @endphp

                                    @if (!empty($productByCategory))
                                        @foreach ($productByCategory as $item)
                                            @if ($item->base_price > 0)
                                                @php
                                                    $image = $item->file_name == null || $item->file_name == '' ? 'images/no-image.png' : 'img_product/' . $item->file_name;
                                                @endphp
                                                <div class="splide__slide m-2">
                                                    <div class="product-card ">
                                                        <div class="special-img position-relative overflow-hidden "
                                                            style="border-radius: 10px 10px 0px 0px; z-index: 1 !important;">
                                                            <img src="{!! imageDir() . $image !!}" class="w-100">

                                                            <div class="badge position-absolute top-0 end-0 bg-warning opacity-75 text-capitalize"
                                                                style="border-radius : 10px">
                                                                {{ $item->product_status }}</div>
                                                        </div>

                                                        <div class="p-2 product-card-info mt-1 mb-3">

                                                            @php
                                                                $rate = $item->rating;
                                                                $has_half = false;
                                                                if ($rate != floor($rate)) {
                                                                    $roundedValue = floor($rate);
                                                                    $has_half = true;
                                                                } else {
                                                                    $roundedValue = $rate;
                                                                    $has_half = false;
                                                                }
                                                                $rate_rounded = $roundedValue;
                                                            @endphp

                                                            @for ($i = 1; $i <= $rate_rounded; $i++)
                                                                <i class="fa fa-star text-warning"></i>
                                                            @endfor

                                                            @if ($has_half)
                                                                <i class="fa fa-star-half text-warning"></i>
                                                            @endif

                                                            <i class="text-small text-muted"
                                                                style="font-size: 12px !important;">({{ $item->rating }})</i>

                                                            <div style="text-align: left">
                                                                <h5 class="text-capitalize mt-1 mb-1"
                                                                    style="font-weight: 100">
                                                                    {{ $item->product_name . ' - ' . $item->color_name }}
                                                                </h5>
                                                                @if ($item->disc > 0)
                                                                    <span class="d-inline-block text-muted "
                                                                        style="text-decoration: line-through; ">Rp
                                                                        {{ formatNumber($item->base_price) }}</span>
                                                                @endif

                                                                <span class="fw-bold d-inline-block"
                                                                    style="color: #e5345b;">Rp
                                                                    {{ formatNumber($item->price) }}
                                                                </span>
                                                            </div>


                                                            <div class="col-12 d-flex justify-content-between">
                                                                <div class="col-6">
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
                                                                            style="width: 100% !important; ">Wishlist</button>
                                                                    </form>
                                                                </div>
                                                                <div class="col-6">
                                                                    @php
                                                                        $slug = $item->slug."__".preg_replace("/\s+/", "_", $item->color_name);
                                                                        $slug = strtolower($slug);
                                                                    @endphp

                                                                    <a href="{{ route('product', [ 'productslug' => $slug ]) }}"
                                                                        class="float-right btn mt-1 btn-outline-transparent "
                                                                        style="width: 100% !important; font-weight: bolder; color: #e5345b;">{{ __('general.buy') }}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <a href="#" class="d-block btn mt-1 btn-outline-transparent "
                            style="width: 100% !important; border: 1px solid inherit !important; max-width: 150px !important;">{{ __('general.viewAll') }}</a>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <hr class="style14">
    @if ($highlighted_product && isset($highlighted_product[1]))
        @php
            $image = $highlighted_product[1]->file_name == null || $highlighted_product[1]->file_name == '' ? 'images/no-image.png' : 'img_product/' . $highlighted_product[1]->file_name;
        @endphp

        <div class="container mt-0 p-3">
            <div class="row col">
                <div class="card mb-3 border-0">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="{!! imageDir() . $image !!}" class="img-fluid rounded" alt="...">
                        </div>
                        <div class="col-md-8 d-none d-md-block">
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <hr class="style14">
    @if ($highlighted_categories && isset($highlighted_categories[1]))
        <section id="special" class="">
            <div class="container mt-3">
                <div class="title text-center py-3">
                    <h3 class="position-relative d-inline-block">{{ $highlighted_categories[1]->category_name }}</h3>
                </div>
                <div class="">
                    <div class="row">
                        <div class="splide" aria-labelledby="carousel-heading" id="slide_category_2">
                            <div class="splide__track">
                                <div class="splide__list">

                                    @php
                                        $productByCategory = getProductByCategoryIndex($highlighted_categories[1]->id);
                                    @endphp

                                    @if (!empty($productByCategory))
                                        @foreach ($productByCategory as $item)
                                            @if ($item->base_price > 0)
                                                @php
                                                    $image = $item->file_name == null || $item->file_name == '' ? 'images/no-image.png' : 'img_product/' . $item->file_name;
                                                @endphp
                                                <div class="splide__slide m-2">
                                                    <div class="product-card ">
                                                        <div class="special-img position-relative overflow-hidden "
                                                            style="border-radius: 10px 10px 0px 0px; z-index: 1 !important;">
                                                            <img src="{!! imageDir() . $image !!}" class="w-100">

                                                            <div class="badge position-absolute top-0 end-0 bg-warning opacity-75 text-capitalize"
                                                                style="border-radius : 10px">
                                                                {{ $item->product_status }}</div>
                                                        </div>

                                                        <div class="p-2 product-card-info mt-1 mb-3">

                                                            @php
                                                                $rate = $item->rating;
                                                                $has_half = false;
                                                                if ($rate != floor($rate)) {
                                                                    $roundedValue = floor($rate);
                                                                    $has_half = true;
                                                                } else {
                                                                    $roundedValue = $rate;
                                                                    $has_half = false;
                                                                }
                                                                $rate_rounded = $roundedValue;
                                                            @endphp

                                                            @for ($i = 1; $i <= $rate_rounded; $i++)
                                                                <i class="fa fa-star text-warning"></i>
                                                            @endfor

                                                            @if ($has_half)
                                                                <i class="fa fa-star-half text-warning"></i>
                                                            @endif

                                                            <i class="text-small text-muted"
                                                                style="font-size: 12px !important;">({{ $item->rating }})</i>

                                                            <div style="text-align: left">
                                                                <h5 class="text-capitalize mt-1 mb-1"
                                                                    style="font-weight: 100">
                                                                    {{ $item->product_name . ' - ' . $item->color_name }}
                                                                </h5>
                                                                @if ($item->disc > 0)
                                                                    <span class="d-inline-block text-muted "
                                                                        style="text-decoration: line-through; ">Rp
                                                                        {{ formatNumber($item->base_price) }}</span>
                                                                @endif

                                                                <span class="fw-bold d-inline-block"
                                                                    style="color: #e5345b;">Rp
                                                                    {{ formatNumber($item->price) }}
                                                                </span>
                                                            </div>

                                                            <div class="col-12 d-flex justify-content-between">
                                                                <div class="col-6">
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
                                                                            style="width: 100% !important; ">Wishlist</button>
                                                                    </form>
                                                                </div>
                                                                <div class="col-6">
                                                                    @php
                                                                        $slug = $item->slug."__".preg_replace("/\s+/", "_", $item->color_name);
                                                                        $slug = strtolower($slug);
                                                                    @endphp

                                                                    <a href="{{ route('product', [ 'productslug' => $slug ]) }}"
                                                                        class="float-right btn mt-1 btn-outline-transparent "
                                                                        style="width: 100% !important; font-weight: bolder; color: #e5345b;">{{ __('general.buy') }}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <a href="#" class="d-block btn mt-1 btn-outline-transparent "
                            style="width: 100% !important; border: 1px solid inherit !important; max-width: 150px !important;">{{ __('general.viewAll') }}</a>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <hr class="style14">
    @if ($highlighted_product && isset($highlighted_product[2]))
        @php
            $image = $highlighted_product[2]->file_name == null || $highlighted_product[2]->file_name == '' ? 'images/no-image.png' : 'img_product/' . $highlighted_product[2]->file_name;
        @endphp

        <div class="container mt-0 p-3">
            <div class="row col">
                <div class="card mb-3 border-0">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="{!! imageDir() . $image !!}" class="img-fluid rounded" alt="...">
                        </div>
                        <div class="col-md-8 d-none d-md-block">
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <hr class="style14">
    @if ($highlighted_categories && isset($highlighted_categories[2]))
        <section id="special" class="">
            <div class="container mt-3">
                <div class="title text-center py-3">
                    <h3 class="position-relative d-inline-block">{{ $highlighted_categories[2]->category_name }}</h3>
                </div>
                <div class="">
                    <div class="row">
                        <div class="splide" aria-labelledby="carousel-heading" id="slide_category_3">
                            <div class="splide__track">
                                <div class="splide__list">

                                    @php
                                        $productByCategory = getProductByCategoryIndex($highlighted_categories[2]->id);
                                    @endphp

                                    @if (!empty($productByCategory))
                                        @foreach ($productByCategory as $item)
                                            @if ($item->base_price > 0)
                                                @php
                                                    $image = $item->file_name == null || $item->file_name == '' ? 'images/no-image.png' : 'img_product/' . $item->file_name;
                                                @endphp
                                                <div class="splide__slide m-2">
                                                    <div class="product-card ">
                                                        <div class="special-img position-relative overflow-hidden "
                                                            style="border-radius: 10px 10px 0px 0px; z-index: 1 !important;">
                                                            <img src="{!! imageDir() . $image !!}" class="w-100">

                                                            <div class="badge position-absolute top-0 end-0 bg-warning opacity-75 text-capitalize"
                                                                style="border-radius : 10px">
                                                                {{ $item->product_status }}</div>
                                                        </div>

                                                        <div class="p-2 product-card-info mt-1 mb-3">

                                                            @php
                                                                $rate = $item->rating;
                                                                $has_half = false;
                                                                if ($rate != floor($rate)) {
                                                                    $roundedValue = floor($rate);
                                                                    $has_half = true;
                                                                } else {
                                                                    $roundedValue = $rate;
                                                                    $has_half = false;
                                                                }
                                                                $rate_rounded = $roundedValue;
                                                            @endphp

                                                            @for ($i = 1; $i <= $rate_rounded; $i++)
                                                                <i class="fa fa-star text-warning"></i>
                                                            @endfor

                                                            @if ($has_half)
                                                                <i class="fa fa-star-half text-warning"></i>
                                                            @endif

                                                            <i class="text-small text-muted"
                                                                style="font-size: 12px !important;">({{ $item->rating }})</i>

                                                            <div style="text-align: left">
                                                                <h5 class="text-capitalize mt-1 mb-1"
                                                                    style="font-weight: 100">
                                                                    {{ $item->product_name . ' - ' . $item->color_name }}
                                                                </h5>
                                                                @if ($item->disc > 0)
                                                                    <span class="d-inline-block text-muted "
                                                                        style="text-decoration: line-through; ">Rp
                                                                        {{ formatNumber($item->base_price) }}</span>
                                                                @endif

                                                                <span class="fw-bold d-inline-block"
                                                                    style="color: #e5345b;">Rp
                                                                    {{ formatNumber($item->price) }}
                                                                </span>
                                                            </div>
                                                            <div class="col-12 d-flex justify-content-between">
                                                                <div class="col-6">
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
                                                                            style="width: 100% !important; ">Wishlist</button>
                                                                    </form>
                                                                </div>
                                                                <div class="col-6">
                                                                    @php
                                                                        $slug = $item->slug."__".preg_replace("/\s+/", "_", $item->color_name);
                                                                        $slug = strtolower($slug);
                                                                    @endphp

                                                                    <a href="{{ route('product', [ 'productslug' => $slug ]) }}"
                                                                        class="float-right btn mt-1 btn-outline-transparent "
                                                                        style="width: 100% !important; font-weight: bolder; color: #e5345b;">{{ __('general.buy') }}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <a href="#" class="d-block btn mt-1 btn-outline-transparent "
                            style="width: 100% !important; border: 1px solid inherit !important; max-width: 150px !important;">{{ __('general.viewAll') }}</a>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <hr class="style14">
    @if ($videos)
        @foreach ($videos as $item)
            <div class="container">
                <div class="row mt-0 p-1">
                <div class="text-center my-5 ratio ratio-16x9">
                    <iframe src="{!! $item->url !!}" allowfullscreen>
                    </iframe>
                </div>
                </div>
            </div>
        @endforeach
    @endif

@endsection
