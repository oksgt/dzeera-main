<footer class="footer mt-auto py-3">
    <div class="container">
        <div class="row">
            <div class="col-sm">
                <h5>{{ __('general.aboutUs') }}</h5>
                <ul class="list-unstyled text-small">
                    @php
                        $brands = getAllActiveBrand();
                    @endphp

                    @foreach ($brands as $index => $item)
                        <li>
                            <a style="text-decoration: none" class="link-secondary" href="{{ route('about-us', ['brandslug' => $item->slug]) }}">
                            <i class="fas fa-bookmark"></i>
                            {{ $item->brand_name }}</a>
                        </li>
                    @endforeach

                </ul>
            </div>
            <div class="col-sm">
                <h5>{{ __('general.socialMedia') }}</h5>
                <ul class="social-media-list">
                    @php
                        $SocialMedia = getSocialMedia();
                    @endphp
                    @foreach ($SocialMedia as $index => $item)
                        <li><a  style="text-decoration: none" target="_blank" class="link-secondary" href="{!! $item->url !!}"><i
                                    class="{{ $item->icon }} link-secondary"></i> {{ $item->social_media }}</a></li>
                    @endforeach

                    {{-- <li><a class="link-secondary" href="#"><i class="fa fa-twitter link-secondary"></i> Twitter</a></li>
            <li><a class="link-secondary" href="#"><i class="fa fa-instagram link-secondary"></i> Instagram</a></li>
            <li><a class="link-secondary" href="#"><i class="fa fa-youtube link-secondary"></i> YouTube</a></li> --}}
                </ul>
            </div>
            <div class="col-sm">
                <h5>{{ __('general.paymentMethods') }}</h5>
                <ul class="payment-method-list">
                    <li><a class="link-secondary"  style="text-decoration: none" href="#">Bank Transfer</a></li>
                </ul>
            </div>
            <div class="col-sm">
                <h5>Copyright D'zeera @ <?= Date('Y')?></h5>
            </div>
        </div>
    </div>
</footer>
