@extends('layouts.app')

@section('content')
    <section class="" style="margin-top: 140px">
        <div class="container-fluid mt-3 mb-4 ">
            <div class="row mt-4 mb-4">
                <div class="col-lg-12 col-md-6 col-6 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <h3>New Arrivals</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">

                    <div class="row row-cols-2 row-cols-sm-4 row-cols-md-4 g-3 mt-0" id="product-container">
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
                                                        style="width: 100% !important; ">Wishlist</button>
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
                    <div class="row mt-3">
                        <div class="col">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination pagination-sm justify-content-end">
                                    @if ($totalPages > 1)
                                        @php
                                            $currentPage = $page;
                                            $range = 2;
                                        @endphp

                                        <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                                            @php
                                                // $current_url = url()->current() . '?page=1';
                                                if ($filtered_['use_filter'] !== 1) {
                                                    $updatedUrl = url()->current() . '?page=1';
                                                } else {
                                                    $currentUrl = URL::current();
                                                    $queryParameters = Request::query();
                                                    unset($queryParameters['page']);
                                                    $updatedUrl = URL::to($currentUrl) . '?' . http_build_query($queryParameters) . '&page=1';
                                                }

                                            @endphp
                                            <a class="page-link"
                                                href="{{ $updatedUrl }}">{{ __('general.first') }}</a>
                                        </li>

                                        <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                                            @php
                                                if ($filtered_['use_filter'] !== 1) {
                                                    $updatedUrl = url()->current() . '?page=' . $page - 1;
                                                } else {
                                                    // $current_url = url()->current() . '?page=' . $page - 1;
                                                    $currentUrl = URL::current();
                                                    $queryParameters = Request::query();
                                                    unset($queryParameters['page']);
                                                    $updatedUrl = URL::to($currentUrl) . '?' . http_build_query($queryParameters) . '&page=' . $page - 1;
                                                }
                                            @endphp
                                            <a class="page-link"
                                                href="{{ $updatedUrl }}">{{ __('general.previous') }}</a>
                                        </li>

                                        <li class="page-item {{ $page == 1 ? 'active' : '' }}">
                                            @php
                                                if ($filtered_['use_filter'] !== 1) {
                                                    $updatedUrl = url()->current() . '?page=1';
                                                } else {
                                                    $currentUrl = URL::current();
                                                    $queryParameters = Request::query();
                                                    unset($queryParameters['page']);
                                                    $updatedUrl = URL::to($currentUrl) . '?' . http_build_query($queryParameters) . '&page=1';
                                                }
                                            @endphp
                                            <a class="page-link" href="{{ $updatedUrl }}">1</a>
                                        </li>

                                        @if ($currentPage - $range > 2)
                                            <li class="page-item ">
                                                <a class="page-link" href="#">...</a>
                                            </li>
                                        @endif

                                        @php
                                            $startPage = max($currentPage - $range, 2);
                                            $endPage = min($currentPage + $range, $totalPages - 1);
                                        @endphp

                                        @for ($i = $startPage; $i <= $endPage; $i++)
                                            <li class="page-item {{ $i == $page ? 'active' : '' }}">
                                                @php
                                                    if ($filtered_['use_filter'] !== 1) {
                                                        $updatedUrl = url()->current() . '?page=' . $i;
                                                    } else {
                                                        $currentUrl = URL::current();
                                                        $queryParameters = Request::query();
                                                        unset($queryParameters['page']);
                                                        $updatedUrl = URL::to($currentUrl) . '?' . http_build_query($queryParameters) . '&page=' . $i;
                                                    }
                                                @endphp
                                                <a class="page-link" href="{{ $updatedUrl }}">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        @if ($totalPages - $currentPage > $range + 1)
                                            <li class="page-item ">
                                                <a class="page-link" href="#">...</a>
                                            </li>
                                        @endif

                                        <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                                            @php
                                                if ($filtered_['use_filter'] !== 1) {
                                                    $updatedUrl = url()->current() . '?page=' . $page + 1;
                                                } else {
                                                    $currentUrl = URL::current();
                                                    $queryParameters = Request::query();
                                                    unset($queryParameters['page']);
                                                    $updatedUrl = URL::to($currentUrl) . '?' . http_build_query($queryParameters) . '&page=' . $page + 1;
                                                }
                                            @endphp
                                            <a class="page-link" href="{{ $updatedUrl }}">{{ __('general.next') }}</a>
                                        </li>

                                        <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                                            @php
                                                if ($filtered_['use_filter'] !== 1) {
                                                    $updatedUrl = url()->current() . '?page=' . $totalPages;
                                                } else {
                                                    $currentUrl = URL::current();
                                                    $queryParameters = Request::query();
                                                    unset($queryParameters['page']);
                                                    $updatedUrl = URL::to($currentUrl) . '?' . http_build_query($queryParameters) . '&page=' . $totalPages;
                                                }
                                            @endphp
                                            <a class="page-link" href="{{ $updatedUrl }}">{{ __('general.last') }}</a>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
