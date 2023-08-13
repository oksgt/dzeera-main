<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>D'Zeera</title>
  <link rel="icon" href="asset_sample/img/icon.png" />
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href=" https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css " rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,700" rel="stylesheet">
  {{-- <script src="https://use.fontawesome.com/3ada90b5cb.js"></script> --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    {{-- @php
        $brands = getAllActiveBrand();
    @endphp --}}
    {{-- {{ formatNumber(12345.6789) }} --}}
  @include('layouts.header')
  @yield('content')
  @include('layouts.footer')

<!-- modal -->
<div class="modal fade" id="languageModal" tabindex="-1" aria-labelledby="languageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body justify-content-center d-flex">
        <div class="dropdown">
          <button class="btn dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown"
            aria-expanded="false">
            <img src="img/ina-flag.png" alt="Language" width="20" height="20" class="me-2"> Indonesia
          </button>
          <ul class="dropdown-menu" aria-labelledby="languageDropdown">
            <li><a class="dropdown-item" href="#"><img src="img/uk-flag.png" alt="Language" width="20" height="20"
                  class="me-2"> English</a></li>
            <li><a class="dropdown-item" href="#"><img src="img/ina-flag.png" alt="Language" width="20" height="20"
                  class="me-2"> Indonesia</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

@yield('scripts')
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src=" https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js "></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="js/app.js"></script>

</body>

</html>
