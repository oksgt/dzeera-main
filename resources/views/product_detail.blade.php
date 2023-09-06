@extends('layouts.app')

@section('content')
    <section class="" style="margin-top: 140px">
        <div class="container-fluid mt-3  ">
            <div class="row mt-0 ">
                <div class="card card-product-img mt-0 bg bg-white border-0">
                    <div class="container-fliud">
                        <div class="wrapper row">
                            <div class="preview col-md-6">
                                <div class="preview-pic tab-content">
                                    @if (!empty($images))
                                        @foreach ($images as $item)
                                            @php
                                                $image = $item->file_name == null || $item->file_name == '' ? 'images/no-image.png' : 'img_product/' . $item->file_name;
                                            @endphp
                                            <div class="tab-pane {{ ($item->is_thumbnail == 1) ? 'active' : '' }}" id="pic-{{ $item->id }}"><img src="{!! imageDir() . $image !!}" /></div>
                                        @endforeach

                                        <ul class="preview-thumbnail nav nav-tabs">
                                            @foreach ($images as $item)
                                                @php
                                                    $image = $item->file_name == null || $item->file_name == '' ? 'images/no-image.png' : 'img_product/' . $item->file_name;
                                                @endphp
                                                <li>
                                                    <a data-target="#pic-{{ $item->id }}" data-toggle="tab">
                                                        <img src="{!! imageDir() . $image !!}" />
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="tab-panel text-center" id="pic-1"><img class="w-25" src="{!! imageDir() . 'images/no-image.png' !!}" /></div>
                                    @endif
                                </div>

                            </div>

                            <div class="details col-md-6">
                                <h6><span class="badge bg-{{ $product_detail->product_status == 'ready' ? 'warning' : 'secondary' }}">{{ strtoupper($product_detail->product_status) }}</span></h6>
                                <h3 class="product-title">{{ $product_detail->product_name }} -
                                    {{ $product_detail->color_name }}</h3>
                                <div class="rating">
                                    <div class="stars">
                                        @php
                                            $rate = $product_detail->rating;
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

                                    </div>
                                    <span class="review-no">{{ $product_detail->rating }} Rating</span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table">
                                      <thead>
                                        <tr>
                                          <th style="border: none;">Size</th>
                                          <th style="border: none;"></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        @foreach ($options as $item)
                                            <tr>
                                                <td>
                                                <label for="ss">{{ $item->size }}</label><br>
                                                <label for="">Rp. {{ formatNumber($item->price) }}</label>
                                                @if ($item->stock == 0)
                                                    (stok habis)
                                                @endif
                                                </td>
                                                <td style="width: 200px">
                                                    <form action="{{ url('/cart') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="product_id"
                                                            value="{{ $item->product_id }}">
                                                        <input type="hidden" name="color_opt_id"
                                                            value="{{ $item->color }}">
                                                        <input type="hidden" name="size_opt_id"
                                                            value="{{ $item->size_opt_id }}">
                                                        <input type="hidden" name="price"
                                                            value="{{ $item->price }}">
                                                        {{-- <button type="submit" class="btn btn-outline-dark btn-sm float-end w-50">{{ __('general.buy') }}</button> --}}
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" placeholder="Quantity"
                                                            aria-label="Quantity" aria-describedby="button-addon2" value="1" name="qty">
                                                            <button type="submit" class="btn btn-outline-dark btn-sm float-end w-50">{{ __('general.buy') }}</button>
                                                          </div>

                                                    </form>

                                                </td>
                                            </tr>
                                        @endforeach

                                      </tbody>
                                    </table>
                                  </div>

                                {{-- <h4 class="price">current price: <span>$180</span></h4>
                                <p class="vote"><strong>91%</strong> of buyers enjoyed this product! <strong>(87
                                        votes)</strong></p>
                                <h5 class="sizes">sizes:
                                    <span class="size" data-toggle="tooltip" title="small">s</span>
                                    <span class="size" data-toggle="tooltip" title="medium">m</span>
                                    <span class="size" data-toggle="tooltip" title="large">l</span>
                                    <span class="size" data-toggle="tooltip" title="xtra large">xl</span>
                                </h5>
                                <h5 class="colors">colors:
                                    <span class="color orange not-available" data-toggle="tooltip"
                                        title="Not In store"></span>
                                    <span class="color green"></span>
                                    <span class="color blue"></span>
                                </h5>
                                <div class="action">
                                    <button class="add-to-cart btn btn-default" type="button">add to cart</button>
                                    <button class="like btn btn-default" type="button"><span
                                            class="fa fa-heart"></span></button>
                                </div> --}}

                                <div class="product-description-container mt-4">
                                    <h5>Product Detail</h5>
                                    <div class="product-description collapsed" id="productDescription">
                                      {!! $product_detail->product_desc !!}
                                    </div>
                                    <p>
                                      <a href="#" id="seeMoreLink">See More</a>
                                    </p>
                                  </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="special" class="">
        <div class="container">
            <div class="title text-center py-3">
                <h5 class="position-relative d-inline-block">{{ __('general.youmightalsolikes') }}</h5>
            </div>
            <div class="">
                <div class="row">
                    <div class="splide" aria-labelledby="carousel-heading" id="slide_new_arrivals">
                        <div class="splide__track">
                            <div class="splide__list">
                                @php
                                    $newArrivals = getYouMightLike($product_detail->product_id);
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
                                                    <div style="text-align: center">
                                                        <h5 class="text-capitalize mt-1 mb-1" style="font-weight: 100">
                                                            {{ $item->product_name . ' - ' . $item->color_name }}
                                                        </h5>
                                                        <span class="d-inline-block text-muted "
                                                            style="text-decoration: line-through; ">Rp.
                                                            {{ formatNumber($item->base_price) }}</span>
                                                        <span class="d-inline-block"
                                                            style="font-weight: 200; color: #e5345b;">Rp.
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
                                                                style="width: 100% !important; ">Wishlist</button>
                                                        </form>

                                                        <a href="#"
                                                            class="float-right btn mt-1 btn-outline-transparent "
                                                            id="btn-buy"
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

            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.preview-thumbnail a').on('click', function(e) {
                    e.preventDefault();
                    var target = $(this).attr('data-target');
                    $('.preview-pic .tab-pane').removeClass('active');
                    $(target).addClass('active');
                });

                var description = $('#productDescription');
                var seeMoreLink = $('#seeMoreLink');

                seeMoreLink.on('click', function(e) {
                e.preventDefault();
                description.toggleClass('collapsed');
                if (description.hasClass('collapsed')) {
                    seeMoreLink.text('See More');
                } else {
                    seeMoreLink.text('See Less');
                }
                });
            });
        </script>
    @endpush
@endsection
