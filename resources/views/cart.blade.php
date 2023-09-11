@extends('layouts.app')

@section('content')
    <section class="" style="margin-top: 140px">
        <div class="container-fluid mt-3 mb-4 ">
            <div class="row mt-4 mb-4">
                <div class="col-lg-12 col-md-6 col-6 d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <h3>{{ __('general.cart') }}</h3>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center row mb-4">
                <div class="col-md-7">
                    @php
                        $total_items = 0;
                        $total_price = 0;
                    @endphp

                    @foreach ($data as $item)
                        @php
                            $image = $item->file_name == null || $item->file_name == '' ? 'images/no-image.png' : 'img_product/' . $item->file_name;

                            $total_items += $item->qty;
                            $total_price += $item->total_price;

                        @endphp

                        <div class="row p-2 bg-white border rounded mt-2">
                            <div class="col-md-2 mt-1"><img class="img-fluid img-responsive rounded product-image"
                                    src="{!! imageDir() . $image !!}"></div>
                            <div class="col-md-7 mt-1">
                                <h5>{{ $item->product_name . ' - ' . $item->color_name }}</h5>
                                <p class="text-justify text-truncate para mb-0">Size {{ $item->size }}<br></p>
                                <p class="text-justify text-truncate para mb-0">Qty {{ $item->qty }} Items<br><br></p>
                                <h5 class="text-justify text-truncate para mb-0">Rp. {{ formatNumber($item->price) }}</h5>
                            </div>
                            <div class="align-items-center align-content-center col-md-3 border-left mt-1">
                                <div class="d-flex flex-column mt-0">

                                    <form action="{{ route('updateCart') }}" method="POST">
                                        @csrf
                                        <div class="input-group input-group-sm mb-3">
                                            <input type="hidden" name="cart_id" value="{{ $item->cart_id }}">
                                            <input type="text" class="form-control" name="qty" value="{{ $item->qty }}">
                                            <button class="btn btn-sm btn-outline-secondary" type="submit" id="button-addon2">Update Qty</button>
                                        </div>
                                    </form>


                                    <form action="{{ route('removecart') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="cart_id" value="{{ $item->cart_id }}">
                                        <button class="btn btn-block btn-outline-danger btn-sm mt-2" type="submit">Remove From Cart</button>
                                      </form>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
                <div class="col-md-3">
                    <div class="card text-center mt-2">
                        <div class="card-header">
                            Summary
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Total Items: {{ $total_items }}</h5>
                            <p class="card-text">Total Price: Rp. {{ formatnumber($total_price) }}</p>
                            <a href="#" class="d-block btn btn-success">Checkout</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </section>
@endsection


@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    $(document).ready(function() {
      // Get the quantity input field and cart ID
      var quantityInput = $('#quantity-input');
      var cartId = "{{ $item->cart_id }}"; // Replace with the actual cart ID

      // Increase button click handler
      $('#button-increase').click(function() {
        var currentQuantity = parseInt(quantityInput.val());
        var newQuantity = currentQuantity + 1;
        updateQuantity(newQuantity);
      });

      // Decrease button click handler
      $('#button-decrease').click(function() {
        var currentQuantity = parseInt(quantityInput.val());
        console.log('s');
        if (currentQuantity > 1) {
          var newQuantity = currentQuantity - 1;
          updateQuantity(newQuantity);
        }
      });

      // Function to update the quantity via AJAX
      function updateQuantity(newQuantity) {
        $.ajax({
          url: '/update-cart-quantity', // Replace with the actual route URL
          method: 'POST',
          data: {
            cart_id: cartId,
            quantity: newQuantity
          },
          success: function(response) {
            // Update the quantity input field value
            quantityInput.val(newQuantity);
          },
          error: function(xhr, status, error) {
            // Handle error
            console.log(error);
          }
        });
      }
    });
  </script>
@endpush
