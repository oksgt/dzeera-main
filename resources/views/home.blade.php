@extends('layouts.app')

@section('content')
<div id="" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        @foreach ($banner as $index => $item)
            <div class="carousel-item @if ($index === 0) active @endif s-{{imageDir()}}">
            <img src="{{ imageDir().'banner/'.$item->file_name}}" class="d-block w-100" alt="{{$item->file_name}}">
          </div>
        @endforeach
      {{-- <div class="carousel-item active">
        <img src="asset_sample/img/img_1.jpg" class="d-block w-100" alt="Slide 1">
      </div>
      <div class="carousel-item">
        <img src="asset_sample/img/img_2.jpg" class="d-block w-100" alt="Slide 2">
      </div> --}}
    </div>
  </div>
@endsection
