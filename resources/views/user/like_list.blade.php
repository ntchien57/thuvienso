@extends('index.index')
@section('title')
    Danh sách yêu thích
@endsection
@section('style')
    <style>
        #ulmenu {
            position: absolute;
            display: none;
            z-index: 10;
            background: white;
            box-sizing: border-box;
        }

        #mainmenu #menucontact div:hover #ulmenu {
            display: block;
        }

        #content {
            z-index: 9.5;
        }
    </style>
@endsection
@section('header')
    @include('page.header')
    @include('page.mainmenu')
@endsection
@section('content')
    <div id="content" class="mb-4">
        <h2 class="mb-4"> Sách yêu thích </h2>
        <div class="list_pd">
            @php
                $dem = 1;
                $sbc = 1;
                $sgg = 1;
            @endphp
            @foreach ($likes as $sachyeuthich)
                @if ($sachyeuthich && $dem <= 4)
                    @php $dem=$dem+1; @endphp <div class="product mr-4">
                        <div class="image m-0">
                            <div style="position: relative;">
                                <a href="{{ route('chitietsanpham', $sachyeuthich->id) }}"><img
                                        src="{{ asset('storage/uploads') . '/' . $sachyeuthich->anh }}" alt=""
                                        class=""></a>
                            </div>
                            <a href="{{ route('chitietsanpham', $sachyeuthich->id) }}">
                                <div class="product_name m-2" title="{{ $sachyeuthich->tensach }}">
                                    {{ $sachyeuthich->tensach }}</div>
                            </a>
                            <div class="product_composer ml-2" style="width:200px"> Tác Giả: <span
                                    style="color:red">{{ $sachyeuthich->tentacgia }}<span>
                            </div>
                            <div class="d-flex justify-content-between" style="width:200px"> 
                                <div class="rating m-2">
                                    <span class="fa fa-star checked"></span>
                                    <span class="fa fa-star checked"></span>
                                    <span class="fa fa-star checked"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="fa fa-star"></span>
                                    <span class="comment">(30 nhận xét)</span>
                                </div>
                                <div style="align-items: center">
                                    <a href=""><i class="fa fa-heart" aria-hidden="true" style="font-size: 20px; color:red"></i></a>
                                </div>
                            </div>

                        </div>
                    </div>
                @endif
            @endforeach()
        </div>
    </div>
@endsection
@section('footer')
    @include('page.footer')
@endsection
