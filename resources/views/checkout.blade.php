@extends('layouts.app')

@section('content')
    <section class="" style="margin-top: 140px">
        <div class="container-fluid mt-3 mb-4 ">
            <div class="row mt-4 mb-4">
                <div class="col-lg-12 col-md-6 col-6 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <h3>Checkout</h3>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center row mb-4">
                <div class="col-md-7">
                    <form action="{{ route('checkout.next') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Province</label>
                        <select class="form-control provinsi-tujuan" name="province_destination" onchange="getCities(this.value)">
                            <option value="0">-- pilih provinsi tujuan --</option>
                            @foreach ($provinces as $province => $value)
                                <option value="{{ $province }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">City</label>
                        <select class="form-control kota-tujuan" name="city_destination" id="city_destination" onchange="checkOngkir()">
                            <option value="">-- pilih kota tujuan --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Postal Code</label>
                        <input type="text" class="form-control" id="kode_pos" name="kode_pos" placeholder="Enter your Postal Code" required>
                    </div>

                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Ongkir</label>
                        <select class="form-control" name="ongkir_list" id="ongkir_list" >
                            <option value="">-- pilih layanan --</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                </div>
                <div class="col-md-3">
                    <div class="card text-center mt-2">
                        <div class="card-header">
                            Summary
                        </div>
                        <div class="card-body">
                            {{-- <h5 class="card-title">Total Items: {{ $total_items }}</h5> --}}
                            {{-- <p class="card-text">Total Price: Rp. {{ formatnumber($total_price) }}</p> --}}
                            <a href="{{ route('checkout') }}" class="d-block btn btn-success">Checkout</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>
@endsection


@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script> --}}
    <script>
        //ajax select kota tujuan
        let isProcessing = false;

        function getCities(provinceId) {
            // Send an AJAX request to the getCities route
            $.ajax({
                url: '/getCities/' + provinceId,
                type: 'GET',
                success: function(response) {
                    // On success, populate the second select element with cities
                    var citiesSelect = $('.kota-tujuan');
                    // Clear existing options
                    citiesSelect.html('<option value="">-- pilih kota tujuan --</option>');
                    // Add new options based on the response data
                    for (var cityId in response) {
                        var cityName = response[cityId];
                        var option = $('<option>').val(cityId).text(cityName);
                        citiesSelect.append(option);
                    }
                },
                error: function(error) {
                    // Handle error if any
                    console.log(error);
                }
            });
        }
        //ajax check ongkir

        function checkOngkir() {
            let token = $("meta[name='csrf-token']").attr("content");
            let city_destination = $('select[name=city_destination]').val();

            if (isProcessing) {
                return;
            }

            isProcessing = true;
            $.ajax({
                url: "/ongkir",
                data: {
                    _token: token,
                    city_origin: 105,
                    city_destination: city_destination,
                    courier: ['jne'],
                    weight: 1000,
                },
                dataType: "JSON",
                type: "POST",
                success: function (response) {
                    isProcessing = false;
                    if (response) {
                        console.log(response);
                        var selectOptions = '';

                        response[0].costs.forEach(function(cost) {
                        var service = cost.service;
                        var description = cost.description;
                        var costValue = cost.cost[0].value;

                        var optionText = service + '\n' + '(' + description + ')' + '\t' + 'Rp. ' + costValue;
                        selectOptions += '<option value="' + service+"_"+ costValue + '">JNE ' + optionText + '</option>';
                        });

                        $('#ongkir_list').append(selectOptions);
                    }
                },
                error: function (error) {
                    isProcessing = false;
                    console.log(error);
                }
            });
        }

    </script>
@endpush
