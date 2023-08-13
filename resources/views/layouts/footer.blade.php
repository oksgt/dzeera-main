<footer class="footer mt-auto py-3">
    <div class="container">
      <div class="row">
        <div class="col-sm">
          <h5>About Us</h5>
          <ul class="list-unstyled text-small">
            <li><a class="link-secondary" href="#">Link 1</a></li>
            <li><a class="link-secondary" href="#">Link 2</a></li>
          </ul>
        </div>
        <div class="col-sm">
          <h5>Social Media</h5>
          <ul class="social-media-list">
            @php
                $SocialMedia = getSocialMedia();
            @endphp
            @foreach ($SocialMedia as $index => $item)
                <li><a target="_blank" class="link-secondary" href="{!! $item->url !!}" ><i class="{{$item->icon}} link-secondary"></i> {{$item->social_media}}</a></li>
            @endforeach

            {{-- <li><a class="link-secondary" href="#"><i class="fa fa-twitter link-secondary"></i> Twitter</a></li>
            <li><a class="link-secondary" href="#"><i class="fa fa-instagram link-secondary"></i> Instagram</a></li>
            <li><a class="link-secondary" href="#"><i class="fa fa-youtube link-secondary"></i> YouTube</a></li> --}}
          </ul>
        </div>
        <div class="col-sm">
          <h5>Payment Method</h5>
          <ul class="payment-method-list">
            <li><a class="link-secondary" href="#"><i class="fa fa-cc-visa link-secondary"></i> Visa</a></li>
            <li><a class="link-secondary" href="#"><i class="fa fa-cc-mastercard link-secondary"></i> Mastercard</a></li>
            <li><a class="link-secondary" href="#"><i class="fa fa-cc-paypal link-secondary"></i> PayPal</a></li>
            <li><a class="link-secondary" href="#"><i class="fa fa-cc-amazon-pay link-secondary"></i> Amazon Pay</a></li>
          </ul>
        </div>
        <div class="col-sm">
          <h5>Copyright D'zeera @ 2023</h5>
        </div>
      </div>
    </div>
  </footer>
