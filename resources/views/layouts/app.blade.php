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


    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src=" https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js "></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/wrunner-native.js') }}"></script>

    @stack('scripts')
</body>

</html>
