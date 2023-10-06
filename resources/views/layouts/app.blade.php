<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>D'Zeera | Official</title>
    <link rel="icon" href="{{ asset('asset_sample/img/icon.png') }}" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href=" https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css " rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/wrunner-default-theme.css') }}">
    {{-- <script src="https://use.fontawesome.com/3ada90b5cb.js"></script> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body>

    @include('layouts.header')
    @yield('content')
    @include('layouts.footer')

    <!-- modal -->
    <div class="modal fade" id="languageModal" tabindex="-1" aria-labelledby="languageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body justify-content-center d-flex">
                    <div class="dropdown w-100">
                        <button class="btn dropdown-toggle w-100" type="button" id="languageDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('asset_sample/img/' . __('general.flag') . '-flag.png') }}"
                                alt="{{ __('general.language') }}" width="20" height="20" class="me-2">
                            {{ strtoupper(Lang::locale()) == 'ID' ? 'Bahasa Indonesia' : 'English' }}
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="languageDropdown">
                            <li><a class="dropdown-item" href="{{ route('lang', ['locale' => 'en']) }}"><img
                                        src="{{ asset('asset_sample/img/uk-flag.png') }}"
                                        alt="{{ __('general.language') }}" width="20" height="20" class="me-2">
                                    English</a></li>
                            <li><a class="dropdown-item" href="{{ route('lang', ['locale' => 'id']) }}"><img
                                        src="{{ asset('asset_sample/img/ina-flag.png') }}"
                                        alt="{{ __('general.language') }}" width="20" height="20"
                                        class="me-2"> Bahasa Indonesia</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body justify-content-center d-flex">
                    <div class="input-group">

                        <input type="text" name="input_search" class="form-control"
                            placeholder="{{ __('general.searchProduct') }}"
                            aria-label="{{ __('general.searchProduct') }}" aria-describedby="button-addon2">
                        <button class="btn btn-dark" type="submit" id="button-search">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- modal -->
    <div class="modal fade w-100" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="card-title">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body justify-content-center d-flex">
                    <div class="card text-center  w-100 border-0">
                        <div class="card-body p-2">
                            <h6 class="text-muted">Please login with your social media account below</h6>
                            <a href="{{ url('/auth/google') }}" class="btn btn-sm btn-outline-dark mt-3">
                                <i class="fab fa-google"></i>
                                Login with Google</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-home" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn-close m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="card text-center border-0">
                        <div class="card-body p-0 mb-3">
                            <h5 class="card-title"><b>Penting!</b></h5>
                            <p class="card-text">Batas waktu pembayaran adalah <b>1 Jam</b></p>
                            <h5 class="card-title"><b>Happy Shopping!</b></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="https://api.whatsapp.com/send?phone=6282136667287" class="float" target="_blank">
        <i class="fab fa-whatsapp my-float"></i>
    </a>



    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src=" https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js "></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/wrunner-native.js') }}"></script>
    <script type="text/javascript"
        {{-- src="https://app.stg.midtrans.com/snap/snap.js" --}}
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        {{-- src="https://app.sandbox.midtrans.com/snap/v1/transactions"; --}}
    data-client-key="{{ config('midtrans.client_key') }}"></script>
    @stack('scripts')
    <script>
        $(document).ready(function() {
            // Attach a click event handler to the search button
            $('#button-search').click(function() {
                // alert('s');
                var inputSearchValue = $('input[name="input_search"]').val();
                var tokenValue = $('input[name="_token"]').val();

                var baseUrl = "{{ url('/') }}"; // Base URL from Laravel
                var queryString = '_token=' + encodeURIComponent(tokenValue) +
                    '&input_search=' + encodeURIComponent(inputSearchValue) +
                    '&page=1';

                var searchUrl = baseUrl + '/search?' + queryString;
                window.open(searchUrl, '_self');

            });

            var route = '{{ Route::currentRouteName() }}';
            if(route == 'home'){
                $('#modal-home').modal('show');
            }

        });
    </script>
</body>

</html>
