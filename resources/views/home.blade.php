@extends('layouts.app')

@section('content')
<div id="" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        @foreach ($banner as $index => $item)
            <div class="carousel-item @if ($index === 0) active @endif ">
                <img src="{!! imageDir().'banner/'.$item->file_name !!}" class="d-block w-100" alt="{{$item->file_name}}">
            </div>
        @endforeach
    </div>
  </div>
@endsection
