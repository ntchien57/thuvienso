@extends('index.index')
@section('title')
    Chi tiết sản phẩm
@endsection
@section('style')
    <link rel="stylesheet" href="{{ url('css/list_product.css') }}">
    <link href="{{ url('css/chitietsanphams.css') }}" type="text/css" rel="stylesheet" />
    <style>
        #ulmenu {
            position: absolute;
            display: none;
            z-index: 10;
            background: white;
            box-sizing: border-box;
            border-left: 2px solid lavender;
            border-bottom: 2px solid lavender;
            border-right: 2px solid lavender;
        }

        #mainmenu #menucontact div:hover #ulmenu {
            display: block;
        }

        #content {
            z-index: 9.5;
        }

        .products {
            width: 155px;
            background: #FFFFFF;
            padding: 0 5px;
            margin: 10px auto;
            position: relative;
        }

        .products .image .saleprice {
            background: url({{ url('public/image/saleprice.png') }}) no-repeat;
            font-weight: bold;
            text-align: center;
            color: #FFF;
            font-size: 13px;
            line-height: 23px;
            width: 46px;
            height: 37px;
            position: absolute;
            top: 0;
            right: 54px;
    </style>
@endsection
@section('header')
    @include('page.header')
    @include('page.mainmenu')
@endsection
@section('content')
    <div id="container">
        <div class="sortable" id="layoutGroup1">
            <div class="product_view_contener row">
                <div class="showleft col-md-8">
                    <div class="image_contenner">
                        <div class="mainimage">
                            <img src="{{ asset('storage/uploads') . '/' . $sach->anh }}" id="mainimage" width="250" />

                        </div>
                    </div>
                    <div class="product_info">
                        <h1>{{ $sach->tensach }}</h1>
                        <div class="groups">
                            <div class="viewfields">
                                <span>Tác giả: <a href=""
                                        title="{{ $sach->tentacgia }}">{{ $sach->tentacgia }}</a></span>
                            </div>
                        </div>
                        <div class="groups">
                            <div class="viewfields">
                                <span>Tác thể loại: {{ $sach->theloai->tentheloai }}</span>
                            </div>
                        </div>
                        <div class="groups">
                            <div class="viewfields">
                                <span>Nhà xuất bản: {{ $sach->nxb->tennxb }}</a></span>
                            </div>
                        </div>
                        <form action="{{ route('muonSach') }}" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group w-25">
                                <label style="font-weight: bold" for="hantra">Số ngày mượn</label>
                                <input type="number" min="1" class="form-control" id="hantra"
                                    placeholder="Nhập số ngày" name="hantra" value="1">
                                <input type="hidden" name="sach_id" value="{{ $sach->id }}">
                            </div>
                            <div class="groups d-flex mt-5">
                                <a href="{{ route('like', $sach->id) }}" class="btn btn-warning mr-4"><i
                                        class="fa fa-heart" aria-hidden="true"></i>
                                    Yêu thích</a>
                                <button type="submit" class="btn btn-success"><i class="fa fa-shopping-cart"
                                        aria-hidden="true"></i>
                                    Đặt mượn sách</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-4">

                    <h5 class="mb-3">📌 Danh sách đánh giá</h5>

                    <div class="review-box p-3"
                        style="max-height: 400px; overflow-y: auto; background: #fafafa; border-radius: 8px; border: 1px solid #ddd;">

                        @forelse ($reviews as $r)
                            <div class="review-item p-2 mb-3"
                                style="background: white; border-radius: 6px; border: 1px solid #e3e3e3;">

                                <strong>{{ $r->user->tendocgia ?? 'Người dùng' }}</strong>

                                <div class="text-warning mb-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $r->rating)
                                            <i class="fa fa-star"></i>
                                        @else
                                            <i class="fa fa-star-o"></i>
                                        @endif
                                    @endfor
                                </div>

                                <p class="mb-0" style="font-size: 14px; color: #444;">
                                    {{ $r->content }}
                                </p>
                            </div>
                        @empty
                            <p class="text-muted">Chưa có đánh giá nào.</p>
                        @endforelse

                    </div>

                </div>

            </div>

            <div class="block" id="module_ProductDetail">
                <h3>Giới thiệu sách</h3>
                <div class="intro" id="contentid">
                    <p>
                        <span style="color:#ff0000;"><span style="font-size:16px;">
                                {{ $sach->tensach }}
                            </span></span>
                    </p>
                    @php
                        echo $chitietsanpham[0]->noidung;
                    @endphp


                </div>
                {{-- <div class="viewmore" id="viewmore"><span>Xem thêm nội dung <i class="fa fa-sort-desc"></i></span></div> --}}
            </div>
            <div class="block" id="module_ProductFieds">
                <a name="fieldlist"></a>
                <h3>Thông tin chi tiết</h3>
                <table class="fields" cellpadding="0" cellspacing="0" width="100%">
                    <tr class="field_view_contenner row0">
                        <td class="title">
                            <a href="" title="Tác giả">Tác giả</a>
                        </td>
                        <td class="values">
                            <a href="" title="{{ $sach->tentacgia }}">{{ $sach->tentacgia }}</a>
                        </td>
                    </tr>

                    <tr class="field_view_contenner row0">
                    </tr>
                    <tr class="field_view_contenner row1">
                        <td class="title">
                            Kích thước
                        </td>
                        <td class="values">
                            {{ $chitietsanpham[0]->kichthuoc }}
                        </td>
                    </tr>
                    <tr class="field_view_contenner row0">
                        <td class="title">
                            Trọng lượng
                        </td>
                        <td class="values">
                            {{ $chitietsanpham[0]->trongluong }}g
                        </td>
                    </tr>
                    <tr class="field_view_contenner row1">
                        <td class="title">
                            Lượt xem
                        </td>
                        <td class="values">
                            51
                        </td>
                    </tr>
                    <tr class="field_view_contenner row0">
                        <td class="title">
                            Ngày phát hành
                        </td>
                        <td class="values">
                            {{ \Carbon\Carbon::parse($chitietsanpham[0]->ngayphathanh)->format('d/m/Y') }}
                        </td>
                    </tr>
                    <tr class="field_view_contenner row1">

                    </tr>
                </table>

            </div>
            <div id="content" class="block">
                <h2 class="title_pd"><a href="" class="title">Sách cùng thể loại </a><span
                        class="css"></span><a href="" class="more"></a></h2>
                <div class="list_pd">
                    @foreach ($sachCungTheLoai as $sach)
                        @if ($sach)
                            <div class="product mr-4">
                                <div class="image m-0">
                                    <div style="position: relative;">
                                        <a href="{{ route('chitietsanpham', $sach->id) }}"><img
                                                src="{{ asset('storage/uploads') . '/' . $sach->anh }}" alt=""
                                                class=""></a>

                                    </div>
                                    <a href="{{ route('chitietsanpham', $sach->id) }}">
                                        <div class="product_name m-2" title="{{ $sach->tensach }}">{{ $sach->tensach }}
                                        </div>
                                    </a>
                                    <div class="product_composer m-2"><a href="">Tác Giả: <span
                                                style="color:red">{{ $sach->tentacgia }}</a></div>
                                    <div class="rating">
                                        <span class="fa fa-star checked"></span>
                                        <span class="fa fa-star checked"></span>
                                        <span class="fa fa-star checked"></span>
                                        <span class="fa fa-star"></span>
                                        <span class="fa fa-star"></span>
                                        <span class="comment">(30 nhận xét)</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
            <br style="clear: both;">

        </div>
    </div>
    </div>
@endsection
@section('footer')
    @include('page.footer')
@endsection
