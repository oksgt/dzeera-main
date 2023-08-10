<section>
    <div class="header-module">
      <nav class="navbar navbar-expand-lg bg-dark p-0 text-white fixed-top" id="nav1">
        <div class="container-fluid">
          <div class="nav nav-tabs nav-fill me-auto my-2 my-lg-0" id="nav-tab" role="tablist"
            style="border: none !important">
            <a class="nav-item nav-link brand-link brand-active" id="nav-home-tab" data-toggle="tab" href="#nav-home"
              role="tab" aria-controls="nav-home" aria-selected="true">Brand 1</a>
            <a class="nav-item nav-link brand-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile"
              role="tab" aria-controls="nav-profile" aria-selected="false">Brand 2</a>
            <a class="nav-item nav-link brand-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact"
              role="tab" aria-controls="nav-contact" aria-selected="false">Brand 3</a>
          </div>
          <button class="btn d-none d-lg-block" type="button" id="languageDropdown" data-bs-toggle="modal"
            data-bs-target="#languageModal">
            <img src="asset_sample/img/ina-flag.png" alt="Language" width="20" height="20" class="me-2">
          </button>
          <button class="btn btn-lg btn-circle btn-outline-transparent d-none d-lg-block"><i
              class="fa fa-user-o"></i></button>
        </div>
      </nav>

      <nav class="navbar navbar-expand-md navbar-dark bg-dark text-white p-0 d-none d-lg-block fixed-top"
        style=" margin-top: 40px;" id="nav2">
        <div class="container-fluid ">
          <div class="navbar w-100 ">
          </div>
          <div class="mx-auto order-0 d-none d-lg-block ">
            <a class="navbar-brand mx-auto" href="#">
              <img src="asset_sample/img/logo.png" alt="Logo" class="d-inline-block align-text-top" style="width: 100%">
            </a>
          </div>

          <div class="navbar w-100 order-3 dual-collapse2  p-0">
            <div class="ms-auto d-none d-lg-block p-0">
              <button class="btn btn-lg btn-circle btn-outline-transparent"><i class="fa fa-search"></i></button>
              <button class="btn btn-lg btn-circle btn-outline-transparent position-relative">
                <i class="fa fa-heart-o"></i>
                <span class="position-absolute top-0 end-0 badge rounded-pill text-danger">5</span>
              </button>
              <button class="btn btn-lg btn-circle btn-outline-transparent position-relative">
                <i class="fa fa-shopping-bag"></i>
                <span class="position-absolute top-0 end-0 badge rounded-pill text-danger">5</span>
              </button>
            </div>
          </div>

        </div>
      </nav>

      <nav class="navbar navbar-expand-lg navbar-dark bg-dark text-white p-0 fixed-top mobile-navbar" id="nav3">
        <div class="container ">

          <button class="navbar-toggler text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa fa-angle-double-right "></i>
          </button>

          <div class="mx-auto order-0 d-lg-none " style="max-width: 100px;">
            <a class="navbar-brand ml-0" href="#">
              <img src="asset_sample/img/logo.png" alt="Logo" class="d-inline-block align-text-top" style="width: 100%">
            </a>
          </div>

          <div class="d-lg-none ">
            <div class="mx-auto">
              <button class="btn btn-circle btn-outline-transparent"><i class="fa fa-search"></i></button>
              <button class="btn btn-lg btn-circle btn-outline-transparent position-relative">
                <i class="fa fa-heart-o"></i>
                <span class="position-absolute top-0 end-0 badge rounded-pill text-danger">5</span>
              </button>
              <button class="btn btn-lg btn-circle btn-outline-transparent position-relative">
                <i class="fa fa-shopping-bag"></i>
                <span class="position-absolute top-0 end-0 badge rounded-pill text-danger">5</span>
              </button>
            </div>
          </div>

          <div class="mobile-navbar-menu offcanvas offcanvas-start" tabindex="-1" id="navbarNav"
            aria-labelledby="navbarNavLabel">
            <div class="offcanvas-header ">
              <button class="ml-0 btn btn-lg  btn-outline-transparent "><i class="fa fa-user"></i></button>

              <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <img src="asset_sample/img/ina-flag.png" alt="Language" width="20" height="20" class="me-2"> Indonesia
                </button>
                <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                  <li><a class="dropdown-item" href="#"><img src="asset_sample/img/uk-flag.png" alt="Language" width="20" height="20"
                        class="me-2"> English</a></li>
                  <li><a class="dropdown-item" href="#"><img src="asset_sample/img/ina-flag.png" alt="Language" width="20"
                        height="20" class="me-2"> Indonesia</a></li>
                </ul>
              </div>

              <button type="button" class="btn-close  text-reset" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
            </div>
            <div class="offcanvas-body flex-column flex-sm-row ">
              <ul class="navbar-nav mx-auto mobile-navbar-nav">
                <li class="nav-item ">
                  <a class="nav-link category-link" href="#">Category 1</a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link category-link" href="#">Category 2</a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link category-link" href="#">Category 3 </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </nav>
    </div>


  </section>