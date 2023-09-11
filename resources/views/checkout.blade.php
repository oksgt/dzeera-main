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
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Name</label>
                        <input type="text" class="form-control" id="exampleFormControlInput1"
                            placeholder="name@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="exampleFormControlInput1"
                            placeholder="name@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="exampleFormControlInput1"
                            placeholder="name@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Address</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
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
                        <label for="exampleFormControlInput1" class="form-label">Ongkir</label>
                        <select class="form-control" name="ongkir_list" id="ongkir_list" >
                            <option value="">-- pilih layanan --</option>
                        </select>
                    </div>

                </div>
                <div class="col-md-3">

                </div>
            </div>

        </div>

    </section>
@endsection


@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script> --}}
    <script>
        // $(document).ready(function(){
        //active select2
        // $(".provinsi-asal , .kota-asal, .provinsi-tujuan, .kota-tujuan").select2({
        //     theme:'bootstrap4',width:'style',
        // });
        // //ajax select kota asal
        // $('select[name="province_origin"]').on('change', function () {
        //     let provindeId = $(this).val();
        //     if (provindeId) {
        //         jQuery.ajax({
        //             url: '/cities/'+provindeId,
        //             type: "GET",
        //             dataType: "json",
        //             success: function (response) {
        //                 $('select[name="city_origin"]').empty();
        //                 $('select[name="city_origin"]').append('<option value="">-- pilih kota asal --</option>');
        //                 $.each(response, function (key, value) {
        //                     $('select[name="city_origin"]').append('<option value="' + key + '">' + value + '</option>');
        //                 });
        //             },
        //         });
        //     } else {
        //         $('select[name="city_origin"]').append('<option value="">-- pilih kota asal --</option>');
        //     }
        // });
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
                        // $('#ongkir').empty();
                        // $('.ongkir').addClass('d-block');
                        // $.each(response[0]['costs'], function (key, value) {
                        //     $('#ongkir').append('<li class="list-group-item">' + response[0].code.toUpperCase() + ' : <strong>' + value.service + '</strong> - Rp. ' + value.cost[0].value + ' (' + value.cost[0].etd + ' hari)</li>');
                        // });
                        var selectOptions = '';

                        response[0].costs.forEach(function(cost) {
                        var service = cost.service;
                        var description = cost.description;
                        var costValue = cost.cost[0].value;

                        var optionText = service + '\n' + '(' + description + ')' + '\t' + 'Rp. ' + costValue;
                        selectOptions += '<option value="' + service + '">' + optionText + '</option>';
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
