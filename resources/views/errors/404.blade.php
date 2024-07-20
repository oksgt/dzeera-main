@extends('layouts.app')

@section('content')
    <section class="" style="margin-top: 140px">
        <div class="container-fluid p-4">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">

                        <div class="card-body">
                            <h1>Oops! Page not found.</h1>
                            <p>The page you are looking for might have been removed, had its name changed, or is temporarily
                                unavailable.</p>
                            <div class="d-flex justify-content-center mt-3">
                                <a class="btn btn-secondary previous" href="{{ url()->previous() }}"><i
                                        class="fas fa-angle-left"></i>
                                    {{ __('general.back') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
