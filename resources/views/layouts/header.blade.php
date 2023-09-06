<section>
    <div class="header-module">
        <nav class="navbar navbar-expand-lg bg-dark p-0 text-white fixed-top {{ Route::currentRouteName() == 'home' ? '' : 'is-scrolled' }}"
            id="nav1">
            <div class="container-fluid">
                <div class="nav nav-tabs nav-fill me-auto my-2 my-lg-0" id="nav-tab" role="tablist"
                    style="border: none !important">

                    @php
                        $brands = getAllActiveBrand();
                    @endphp

                    @foreach ($brands as $index => $item)
                        @php
                            if (session('active-brand') === $item->id) {
                                $active_brand = 'brand-active';
                            } else {
                                $active_brand = '';
                            }
                        @endphp
                        <a class="nav-item nav-link {{ Route::currentRouteName() == 'home' ? 'brand-link' : 'brand-link-other' }} {{ $active_brand }}"
                            id="nav-home-tab" data-toggle="tab" href="{{ route('home', ['brandslug' => $item->slug]) }}"
                            role="tab" aria-controls="nav-home" aria-selected="true">{{ $item->brand_name }}
                        </a>
                    @endforeach

                </div>
                <button class="btn d-none d-lg-block" type="button" id="languageDropdown" data-bs-toggle="modal"
                    data-bs-target="#languageModal">
                    <img src="{{ asset('asset_sample/img/' . __('general.flag') . '-flag.png') }}" alt="Language"
                        width="20" height="20" class="me-2">
                </button>

                @guest
                    <button class="btn btn-lg btn-circle btn-outline-transparent d-none d-lg-block" data-bs-toggle="modal"
                    data-bs-target="#loginModal" title="Login">
                        <i class="fa fa-sign-in"></i>
                        <small style="font-size: 14px"></small>
                    </button>
                @else
                    <button class="btn btn-lg btn-circle btn-outline-transparent d-none d-lg-block" >
                        <i class="fa fa-user"></i>
                        <small style="font-size: 14px">{{ Auth::user()->name }}</small>
                    </button>

                    <a class="btn btn-lg btn-circle btn-outline-transparent d-none d-lg-block" title="Logout" href="{{ url('/signout') }}">
                        <i class="fa fa-sign-out"></i>
                        <small style="font-size: 14px"></small>
                    </a>
                @endguest

            </div>
        </nav>

        <nav class="navbar navbar-expand-md navbar-dark bg-dark text-white p-0 d-none d-lg-block fixed-top {{ Route::currentRouteName() == 'home' ? '' : 'is-scrolled' }}"
            style=" margin-top: 40px; {{ Route::currentRouteName() !== 'home' ? 'background: white !important;' : '' }}"
            id="nav2">
            <div class="container-fluid ">
                <div class="navbar w-100 ">
                </div>
                <div class="mx-auto order-0 d-none d-lg-block ">
                    <a class="navbar-brand mx-auto" href="#">
                        <img src="{{ asset('asset_sample/img/logo.png') }}" alt="Logo"
                            class="d-inline-block align-text-top" style="width: 100%">
                    </a>
                </div>

                <div class="navbar w-100 order-3 dual-collapse2  p-0">
                    <div class="ms-auto d-none d-lg-block p-0">
                        <button
                            class="btn btn-lg  {{ Route::currentRouteName() == 'home' ? 'btn-outline-transparent' : 'btn-outline-transparent-other' }}"
                            data-bs-toggle="modal" data-bs-target="#searchModal">
                            <i class="fa fa-search"></i></button>
                            <a href="{{url('/wishlist/show')}}"
                            class="btn btn-lg  {{ Route::currentRouteName() == 'home' ? 'btn-outline-transparent' : 'btn-outline-transparent-other' }} position-relative">
                            <i class="fa fa-heart"></i>
                            @php
                                $wishlist = json_decode(request()->cookie('wishlist'), true) ?? [];
                                $count_wishlist = count($wishlist);
                            @endphp
                            <span class="position-absolute top-0 end-0 badge rounded-pill text-danger">{{$count_wishlist}}</span>
                        </a>
                        <button
                            class="btn btn-lg  {{ Route::currentRouteName() == 'home' ? 'btn-outline-transparent' : 'btn-outline-transparent-other' }} position-relative">
                            <i class="fa fa-shopping-bag"></i>
                            @php
                                $cart = json_decode(request()->cookie('cart'), true) ?? [];
                                $count_cart = count($cart);
                            @endphp
                            <span class="position-absolute top-0 end-0 badge rounded-pill text-danger">{{$count_cart}}</span>
                        </button>
                    </div>
                </div>

            </div>
        </nav>

        <nav class="navbar navbar-expand-lg navbar-dark bg-dark text-white p-0 fixed-top mobile-navbar {{ Route::currentRouteName() == 'home' ? '' : 'is-scrolled' }}"
            id="nav3" style="{{ Route::currentRouteName() !== 'home' ? 'background: white !important;' : '' }}">
            <div class="container ">

                <button class="navbar-toggler {{ Route::currentRouteName() == 'home' ? 'text-white' : 'text-dark' }}"
                    type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarNav" aria-controls="navbarNav"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-angle-double-right "></i>
                </button>

                <div class="mx-auto order-0 d-lg-none " style="max-width: 100px;">
                    <a class="navbar-brand ml-0" href="#">
                        <img src="{{ asset('asset_sample/img/logo.png') }}" alt="Logo"
                            class="d-inline-block align-text-top" style="width: 100%">
                    </a>
                </div>

                <div class="d-lg-none ">
                    <div class="mx-auto">
                        <button
                            class="btn {{ Route::currentRouteName() == 'home' ? 'btn-outline-transparent' : 'btn-outline-transparent-other' }}"
                            data-bs-toggle="modal" data-bs-target="#searchModal">
                            <i class="fa fa-search"></i></button>
                        <button
                            class="btn btn-lg {{ Route::currentRouteName() == 'home' ? 'btn-outline-transparent' : 'btn-outline-transparent-other' }} position-relative">
                            <i class="fa fa-heart"></i>
                            @php
                                $wishlist = json_decode(request()->cookie('wishlist'), true) ?? [];
                                $count_wishlist = count($wishlist);
                            @endphp
                            <span class="position-absolute top-0 end-0 badge rounded-pill text-danger">{{$count_wishlist}}</span>
                        </button>
                        <button
                            class="btn btn-lg {{ Route::currentRouteName() == 'home' ? 'btn-outline-transparent' : 'btn-outline-transparent-other' }} position-relative">
                            <i class="fa fa-shopping-bag"></i>
                            @php
                                $cart = json_decode(request()->cookie('cart'), true) ?? [];
                                $count_cart = count($cart);
                            @endphp
                            <span class="position-absolute top-0 end-0 badge rounded-pill text-danger">{{ $count_cart }}</span>
                        </button>
                    </div>
                </div>

                <div class="mobile-navbar-menu offcanvas offcanvas-start" tabindex="-1" id="navbarNav"
                    aria-labelledby="navbarNavLabel">
                    <div class="offcanvas-header ">
                        {{-- <button
                            class="ml-0 btn btn-lg  {{ Route::currentRouteName() == 'home' ? 'btn-outline-transparent' : 'btn-outline-transparent-other' }} "><i
                                class="fa fa-user"></i></button> --}}

                        <div class="dropdown ml-0 ">
                            <button class="btn dropdown-toggle ml-0" type="button" id="languageDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ asset('asset_sample/img/' . __('general.flag') . '-flag.png') }}"
                                    alt="{{ __('general.language') }}" width="20" height="20" class="me-2">
                                {{ strtoupper(Lang::locale()) == 'ID' ? 'Indonesia' : 'English' }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                                <li><a class="dropdown-item" href="{{ route('lang', ['locale' => 'en']) }}"><img
                                            src="{{ asset('asset_sample/img/uk-flag.png') }}"
                                            alt="{{ __('general.language') }}" width="20" height="20"
                                            class="me-2"> English</a></li>
                                <li><a class="dropdown-item" href="{{ route('lang', ['locale' => 'id']) }}"><img
                                            src="{{ asset('asset_sample/img/ina-flag.png') }}"
                                            alt="{{ __('general.language') }}" width="20" height="20"
                                            class="me-2"> Indonesia</a></li>
                            </ul>
                        </div>

                        <button type="button" class="btn-close  text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body flex-column flex-sm-row mt-0">
                        <ul
                            class="navbar-nav mx-auto {{ Route::currentRouteName() == 'home' ? 'mobile-navbar-nav' : 'mobile-navbar-nav-other' }}">

                            @guest
                                <li class="nav-item d-block d-lg-none">
                                    <a class="nav-link category-link" href="#" data-bs-toggle="modal"
                                    data-bs-target="#loginModal" title="Login">
                                        <i class="fa fa-sign-in"></i>
                                        <small style="font-size: 14px"> Login</small>
                                    </a>
                                </li>
                            @else
                                <li class="nav-item d-block d-lg-none ">
                                    <a class="nav-link category-link" href="#">
                                        <i class="fa fa-user"></i> {{ Auth::user()->name }}
                                    </a>
                                </li>

                                <li class="nav-item d-block d-lg-none ">
                                    <a class="nav-link category-link" href="{{ url('/signout') }}">
                                        <i class="fa fa-sign-out"></i> Logout
                                    </a>
                                </li>
                            @endguest

                            <hr>

                            @php
                                $categories = getAllCategoriesByBrand();
                            @endphp
                            <li class="nav-item ">
                                <a class="nav-link category-link" href="#"></a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link category-link" href="#">All Product</a>
                            </li>
                            @foreach ($categories as $index => $item)
                                <li class="nav-item ">
                                    <a class="nav-link category-link"
                                        href="#">{{ strtoupper($item->category_name) }}</a>
                                </li>
                            @endforeach


                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </div>


</section>
