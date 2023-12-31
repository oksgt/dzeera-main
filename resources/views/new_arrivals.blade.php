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
                <div class="col-md-6 col-6 d-flex d-md-flex d-lg-none  align-items-center justify-content-end">
                    <button data-bs-toggle="modal" data-bs-target="#filterModal"
                        class="btn btn-lg  {{ Route::currentRouteName() == 'home' ? 'btn-outline-transparent' : 'btn-outline-transparent-other' }}"><i
                            class="fa fa-filter"></i></button>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 product-filter-card p-3 d-lg-block d-none">
                    <form
                        action="{{ route('newArrivals', ['brandslug' => session('active-brand-name'), 'page' => $page]) }}"
                        method="GET">
                        <input type="hidden" name="use_filter" value="1">
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">{{ __('general.search') }}</label>
                            <div class="list-group">
                                <input type="text" class="form-control" name="search">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">{{ __('general.category') }}</label>
                            <div class="list-group">
                                @php
                                    $categories = getAllCategoriesByBrand();
                                @endphp
                                <div class="form-check mx-3 p-2">
                                    <input class="form-check-input" type="radio" name="input_category" id="all"
                                        value="" checked>
                                    <label class="form-check-label" for="all">
                                        {{ __('general.all') }}
                                    </label>
                                </div>
                                @foreach ($categories as $item)
                                    <div class="form-check mx-3 p-2">
                                        <input class="form-check-input" type="radio" name="input_category"
                                            id="{{ $item->id }}" value="{{ $item->id }}">
                                        <label class="form-check-label" for="{{ $item->id }}">
                                            {{ $item->category_name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">{{ __('general.price') }}</label>
                            <div class="range_container">
                                <div class="sliders_control">
                                    <input id="fromSlider" type="range" value="150000" min="10000" max="1000000" />
                                    <input id="toSlider" type="range" value="300000" min="10000" max="1000000" />
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">{{ __('general.minPrice') }}</label>
                            <input class="form-control form_control_container__time__input" type="text" id="fromInput"
                                name="fromInput" value="150000" min="10000" max="1000000" />
                        </div>

                        <div class="mb-3">
                            <label for="exampleFormControlInput2" class="form-label">{{ __('general.maxPrice') }}</label>
                            <input class="form-control form_control_container__time__input" type="text" id="toInput"
                                name="toInput" value="300000" min="10000" max="1000000" />
                        </div>

                        <div class="mb-3 text-center">
                            <button class="btn btn-sm btn-dark" type="submit">
                                {{ __('general.applyFilter') }}
                            </button>
                            @if ($filtered_['use_filter'] !== '')
                                <a class="btn btn-sm btn-light" type="button"
                                    href="{{ route('newArrivals', ['brandslug' => session('active-brand-name')]) }}">
                                    {{ __('general.removeFilter') }}
                                </a>
                            @endif

                        </div>
                    </form>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-9">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            @if ($filtered_['use_filter'] !== '')
                                <p class="m-0">{{ __('general.appliedFilter') }}</p>
                            @endif
                        </div>
                        <div class="col text-end">
                            <div class="btn-group">
                                <button id="dropdownButton" class="btn btn-outline-transparent dropdown-toggle"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">

                                    @if ($filtered_['sort'] !== '')
                                        @if ($filtered_['sort'] == 'newest')
                                            <i class="fas fa-arrow-up-wide-short"></i> <span
                                                id="selectedOptionText">{{ __('general.newest') }}</span>
                                        @elseif ($filtered_['sort'] == 'oldest')
                                            <i class="fas fa-arrow-down-wide-short"></i> <span
                                                id="selectedOptionText">{{ __('general.oldest') }}</span>
                                        @elseif ($filtered_['sort'] == 'priceHigh')
                                            <i class="fas fa-arrow-down-9-1'"></i> <span
                                                id="selectedOptionText">{{ __('general.priceHigh') }}</span>
                                        @elseif ($filtered_['sort'] == 'priceLow')
                                            <i class="fas fa-arrow-down-1-9"></i> <span
                                                id="selectedOptionText">{{ __('general.priceLow') }}</span>
                                        @elseif ($filtered_['sort'] == 'nameAsc')
                                            <i class="fas fa-arrow-down-a-z"></i> <span
                                                id="selectedOptionText">{{ __('general.nameAsc') }}</span>
                                        @elseif ($filtered_['sort'] == 'nameDesc')
                                            <i class="fas fa-arrow-down-z-a"></i> <span
                                                id="selectedOptionText">{{ __('general.nameDesc') }}</span>
                                        @endif
                                    @else
                                        <i class="fas fa-sort"></i> <span
                                            id="selectedOptionText">{{ __('general.sortOption') }}</span>
                                    @endif

                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item my-link" href="{{ url()->full() }}&sort=newest"
                                            onclick="changeButtonText('{{ __('general.newest') }}', 'fas fa-arrow-up-wide-short', 'newest')"><i
                                                class="fas fa-arrow-up-wide-short"></i> {{ __('general.newest') }}</a>
                                    </li>
                                    <li><a class="dropdown-item my-link" href="{{ url()->full() }}&sort=oldest"
                                            onclick="changeButtonText('{{ __('general.oldest') }}', 'fas fa-arrow-down-wide-short', 'oldest')"><i
                                                class="fas fa-arrow-down-wide-short"></i> {{ __('general.oldest') }}</a>
                                    </li>
                                    <li><a class="dropdown-item my-link" href="{{ url()->full() }}&sort=priceHigh"
                                            onclick="changeButtonText('{{ __('general.priceHigh') }}', 'fas fa-arrow-down-9-1', 'priceHigh')"><i
                                                class="fas fa-arrow-down-9-1"></i> {{ __('general.priceHigh') }}</a></li>
                                    <li><a class="dropdown-item my-link" href="{{ url()->full() }}&sort=priceLow"
                                            onclick="changeButtonText('{{ __('general.priceLow') }}', 'fas fa-arrow-down-1-9', 'priceLow')"><i
                                                class="fas fa-arrow-down-1-9"></i> {{ __('general.priceLow') }}</a></li>
                                    <li><a class="dropdown-item my-link" href="{{ url()->full() }}&sort=nameAsc"
                                            onclick="changeButtonText('{{ __('general.nameAsc') }}', 'fas fa-arrow-down-a-z', 'nameAsc')"><i
                                                class="fas fa-arrow-down-a-z"></i> {{ __('general.nameAsc') }}</a></li>
                                    <li><a class="dropdown-item my-link" href="{{ url()->full() }}&sort=nameDesc"
                                            onclick="changeButtonText('{{ __('general.nameDesc') }}', 'fas fa-arrow-down-z-a', 'nameDesc')"><i
                                                class="fas fa-arrow-down-z-a"></i> {{ __('general.nameDesc') }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            {{-- @if (checkArrayValuesNotEmpty($filtered_)) --}}
                            @if ($filtered_['search'] !== '')
                                <span class="badge text-bg-secondary mx-2">{{ $filtered_['search'] }}</span>
                            @endif

                            @if ($filtered_['category'] !== '')
                                <span class="badge text-bg-secondary mx-2">{{ $filtered_['category'] }}</span>
                            @endif

                            @if ($filtered_['from'] !== '')
                                <span class="badge text-bg-secondary mx-2">{{ $filtered_['from'] }}</span>
                            @endif

                            @if ($filtered_['to'] !== '')
                                <span class="badge text-bg-secondary mx-2">{{ $filtered_['to'] }}</span>
                            @endif
                            {{-- @endif --}}
                        </div>
                    </div>
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

                                                @php
                                                    $slug = $item->slug."__".preg_replace("/\s+/", "_", $item->color_name);
                                                    $slug = strtolower($slug);
                                                @endphp

                                                <a href="{{ route('product', [ 'productslug' => $slug ]) }}" class="float-right btn mt-1 btn-outline-transparent " id="btn-buy"
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
                            <nav aria-label="Page navigation">
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

                                                if($filtered_['sort'] !== ''){
                                                    $updatedUrl = $updatedUrl . '&sort='. $filtered_['sort'];
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

                                                if($filtered_['sort'] !== ''){
                                                    $updatedUrl = $updatedUrl . '&sort='. $filtered_['sort'];
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

                                                if($filtered_['sort'] !== ''){
                                                    $updatedUrl = $updatedUrl . '&sort='. $filtered_['sort'];
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
                                                    if($filtered_['sort'] !== ''){
                                                        $updatedUrl = $updatedUrl . '&sort='. $filtered_['sort'];
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
                                                if($filtered_['sort'] !== ''){
                                                    $updatedUrl = $updatedUrl . '&sort='. $filtered_['sort'];
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
                                                if($filtered_['sort'] !== ''){
                                                    $updatedUrl = $updatedUrl . '&sort='. $filtered_['sort'];
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

    <!-- modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body justify-content-center d-flex">
                    <form
                        action="{{ route('newArrivals', ['brandslug' => session('active-brand-name'), 'page' => $page]) }}"
                        method="GET">
                        <input type="hidden" name="use_filter" value="1">
                        <div class="row">
                            <div class="mb-3">
                                <label for="exampleFormControlInput1"
                                    class="form-label">{{ __('general.search') }}</label>
                                <div class="list-group">
                                    <input type="text" class="form-control" name="search">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="exampleFormControlInput1"
                                    class="form-label">{{ __('general.category') }}</label>
                                <div class="list-group">
                                    @php
                                        $categories = getAllCategoriesByBrand();
                                    @endphp
                                    <div class="form-check mx-3 p-2">
                                        <input class="form-check-input" type="radio" name="input_category"
                                            id="all" value="" checked>
                                        <label class="form-check-label" for="all">
                                            {{ __('general.all') }}
                                        </label>
                                    </div>
                                    @foreach ($categories as $item)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="input_category"
                                                id="{{ $item->id }}" value="{{ $item->id }}">
                                            <label class="form-check-label" for="{{ $item->id }}">
                                                {{ $item->category_name }}
                                            </label>
                                        </div>
                                    @endforeach

                                </div>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label for="exampleFormControlInput1"
                                    class="form-label">{{ __('general.price') }}</label>
                                <div class="range_container">
                                    <div class="sliders_control">
                                        <input id="fromSlider2" type="range" value="150000" min="10000"
                                            max="1000000" />
                                        <input id="toSlider2" type="range" value="300000" min="10000"
                                            max="1000000" />
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="exampleFormControlInput1"
                                    class="form-label">{{ __('general.minPrice') }}</label>
                                <input class="form-control form_control_container__time__input" type="text"
                                    id="fromInput2" name="fromInput2" value="150000" min="10000" max="1000000" />
                            </div>

                            <div class="mb-3">
                                <label for="exampleFormControlInput1"
                                    class="form-label">{{ __('general.maxPrice') }}</label>
                                <input class="form-control form_control_container__time__input" type="text"
                                    id="toInput2" name="toInput2" value="300000" min="10000" max="1000000" />
                            </div>

                            <div class="mb-3 text-center">
                                <button class="btn btn-sm btn-dark btn-primary">
                                    {{ __('general.applyFilter') }}
                                </button>
                                @if ($filtered_['use_filter'] !== '')
                                    <a class="btn btn-sm btn-light" type="button"
                                        href="{{ route('newArrivals', ['brandslug' => session('active-brand-name')]) }}">
                                        {{ __('general.removeFilter') }}
                                    </a>
                                @endif
                            </div>

                        </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // document.addEventListener("DOMContentLoaded", function() {
            //     var lastCard = document.querySelector(".product-card");
            //     @if ($filtered_['page'] >= 3)
            //     lastCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            //     @endif
            // });;
        </script>
        <script>
            function controlFromInput(fromSlider, fromInput, toInput, controlSlider) {
                const [from, to] = getParsed(fromInput, toInput);
                fillSlider(fromInput, toInput, '#C6C6C6', '#25daa5', controlSlider);
                if (from > to) {
                    fromSlider.value = to;
                    fromInput.value = to;
                } else {
                    fromSlider.value = from;
                }
            }

            function controlToInput(toSlider, fromInput, toInput, controlSlider) {
                const [from, to] = getParsed(fromInput, toInput);
                fillSlider(fromInput, toInput, '#C6C6C6', '#25daa5', controlSlider);
                setToggleAccessible(toInput);
                if (from <= to) {
                    toSlider.value = to;
                    toInput.value = to;
                } else {
                    toInput.value = from;
                }
            }

            function controlFromSlider(fromSlider, toSlider, fromInput) {
                const [from, to] = getParsed(fromSlider, toSlider);
                fillSlider(fromSlider, toSlider, '#C6C6C6', '#25daa5', toSlider);
                if (from > to) {
                    fromSlider.value = to; //to;
                    fromInput.value = to; //to;
                } else {
                    fromInput.value = from; //from;
                }
            }

            function controlToSlider(fromSlider, toSlider, toInput) {
                const [from, to] = getParsed(fromSlider, toSlider);
                fillSlider(fromSlider, toSlider, '#C6C6C6', '#25daa5', toSlider);
                setToggleAccessible(toSlider);
                if (from <= to) {
                    toSlider.value = to; //to;
                    toInput.value = to; //to;
                } else {
                    toInput.value = from; //from;
                    toSlider.value = from; //from;
                }
            }

            function getParsed(currentFrom, currentTo) {
                const from = parseInt(currentFrom.value, 10);
                const to = parseInt(currentTo.value, 10);
                return [from, to];
            }

            function fillSlider(from, to, sliderColor, rangeColor, controlSlider) {
                const rangeDistance = to.max - to.min;
                const fromPosition = from.value - to.min;
                const toPosition = to.value - to.min;
                controlSlider.style.background = `linear-gradient(
                    to right,
                    ${sliderColor} 0%,
                    ${sliderColor} ${(fromPosition)/(rangeDistance)*100}%,
                    ${rangeColor} ${((fromPosition)/(rangeDistance))*100}%,
                    ${rangeColor} ${(toPosition)/(rangeDistance)*100}%,
                    ${sliderColor} ${(toPosition)/(rangeDistance)*100}%,
                    ${sliderColor} 100%)`;
            }

            function setToggleAccessible(currentTarget) {
                // console.log(currentTarget);
                const toSlider = document.querySelector('#toSlider');
                if (Number(currentTarget.value) <= 0) {
                    toSlider.style.zIndex = 2;
                } else {
                    toSlider.style.zIndex = 0;
                }
            }

            const fromSlider = document.querySelector('#fromSlider');
            const toSlider = document.querySelector('#toSlider');
            const fromInput = document.querySelector('#fromInput');
            const toInput = document.querySelector('#toInput');
            fillSlider(fromSlider, toSlider, '#C6C6C6', '#25daa5', toSlider);
            setToggleAccessible(toSlider);

            fromSlider.oninput = () => controlFromSlider(fromSlider, toSlider, fromInput);
            toSlider.oninput = () => controlToSlider(fromSlider, toSlider, toInput);
            fromInput.oninput = () => controlFromInput(fromSlider, fromInput, toInput, toSlider);
            toInput.oninput = () => controlToInput(toSlider, fromInput, toInput, toSlider);
        </script>

        <script>
            function controlFromInput_2(fromSlider, fromInput, toInput, controlSlider) {
                const [from_2, to_2] = getParsed_2(fromInput, toInput);
                fillSlider_2(fromInput, toInput, '#C6C6C6', '#25daa5', controlSlider);
                if (from_2 > to_2) {
                    fromSlider.value = to_2;
                    fromInput.value = to_2;
                } else {
                    fromSlider.value = from_2;
                }
            }

            function controlToInput_2(toSlider, fromInput, toInput, controlSlider) {
                const [from_2, to_2] = getParsed_2(fromInput, toInput);
                fillSlider_2(fromInput, toInput, '#C6C6C6', '#25daa5', controlSlider);
                setToggleAccessible_2(toInput);
                if (from_2 <= to_2) {
                    toSlider.value = to_2;
                    toInput.value = to_2;
                } else {
                    toInput.value = from_2;
                }
            }

            function controlFromSlider_2(fromSlider, toSlider, fromInput) {
                const [from_2, to_2] = getParsed_2(fromSlider, toSlider);
                fillSlider_2(fromSlider, toSlider, '#C6C6C6', '#25daa5', toSlider);
                if (from_2 > to_2) {
                    fromSlider.value = to_2; //to;
                    fromInput.value = to_2; //to;
                } else {
                    fromInput.value = from_2; //from;
                }
            }

            function controlToSlider_2(fromSlider, toSlider, toInput) {
                const [from_2, to_2] = getParsed_2(fromSlider, toSlider);
                fillSlider_2(fromSlider, toSlider, '#C6C6C6', '#25daa5', toSlider);
                setToggleAccessible_2(toSlider);
                if (from_2 <= to_2) {
                    toSlider.value = to_2; //to;
                    toInput.value = to_2; //to;
                } else {
                    toInput.value = from_2; //from;
                    toSlider.value = from_2; //from;
                }
            }

            function getParsed_2(currentFrom, currentTo) {
                const from_2 = parseInt(currentFrom.value, 10);
                const to_2 = parseInt(currentTo.value, 10);
                return [from_2, to_2];
            }

            function fillSlider_2(from, to, sliderColor, rangeColor, controlSlider) {
                const rangeDistance_2 = to.max - to.min;
                const fromPosition_2 = from.value - to.min;
                const toPosition_2 = to.value - to.min;
                controlSlider.style.background = `linear-gradient(
            to right,
            ${sliderColor} 0%,
            ${sliderColor} ${(fromPosition_2)/(rangeDistance_2)*100}%,
            ${rangeColor} ${((fromPosition_2)/(rangeDistance_2))*100}%,
            ${rangeColor} ${(toPosition_2)/(rangeDistance_2)*100}%,
            ${sliderColor} ${(toPosition_2)/(rangeDistance_2)*100}%,
            ${sliderColor} 100%)`;
            }

            function setToggleAccessible_2(currentTarget) {
                // console.log(currentTarget);
                const toSlider_2 = document.querySelector('#toSlider2');
                if (Number(currentTarget.value) <= 0) {
                    toSlider_2.style.zIndex = 2;
                } else {
                    toSlider_2.style.zIndex = 0;
                }
            }

            const fromSlider_2 = document.querySelector('#fromSlider2');
            const toSlider_2 = document.querySelector('#toSlider2');
            const fromInput_2 = document.querySelector('#fromInput2');
            const toInput_2 = document.querySelector('#toInput2');
            fillSlider_2(fromSlider_2, toSlider_2, '#C6C6C6', '#25daa5', toSlider_2);
            setToggleAccessible_2(toSlider_2);

            fromSlider_2.oninput = () => controlFromSlider_2(fromSlider_2, toSlider_2, fromInput_2);
            toSlider_2.oninput = () => controlToSlider_2(fromSlider_2, toSlider_2, toInput_2);
            fromInput_2.oninput = () => controlFromInput_2(fromSlider_2, fromInput_2, toInput_2, toSlider_2);
            toInput_2.oninput = () => controlToInput_2(toSlider_2, fromInput_2, toInput_2, toSlider_2);

            function changeButtonText(text, iconClass, value) {
                document.getElementById('selectedOptionText').innerText = text;
                document.getElementById('selectedOptionText').previousElementSibling.className = iconClass;
                console.log('Data value:', value);
                var current_url = window.location.href;

                var url = new URL(current_url);
                url.searchParams.delete('sort');
                var updatedUrlString = url.toString();
                window.location.replace(updatedUrlString + '&sort=' + value);
            }
        </script>
    @endpush
@endsection
