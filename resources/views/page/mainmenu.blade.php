@section('mainmenu')
    <div id="mainmenu">
        <div id="menucontact" style="width:1140px;margin: auto;position: relative;">
            <div style="width: 210px;">
                <p style="height:35px;background: #0052CC;width: 220px;cursor:default;">&nbsp;<span><i class="fa fa-bars m-2 text-light" aria-hidden="true"></i><b
                        style="line-height: 35px; font-size: 20px; color: #fff">DANH MỤC SÁCH</b></p>
                <div style="position: relative;float: left;z-index: 10;bottom: 16px;" id="div_dropmenu">
                    <ul id="ulmenu">

                        @foreach ($theloai as $loaisach)
                            <li class="child"><a href="/danhmuc/{{ $loaisach->id }}" class="first">
                                    {{ $loaisach->tentheloai }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div style="float:right;height: 32px;position: absolute;right: 1px;top: 5px;" class="d-flex">
                <a class="text-dark mr-5" style="font-weight: bold;" href="{{ route('home')}}">Trang chủ</a>
                <a class="text-dark mr-5" style="font-weight: bold;" href="{{route('likeList')}}">Sách yêu thích</a>
                <a class="text-dark mr-5" style="font-weight: bold;" href="">Đặt mượn sách</a>
                <a class="text-dark mr-5" style="font-weight: bold;" href="">Lịch sử mượn sách</a>
                <div>
                    <span><i class="fa fa-phone-square" aria-hidden="true"></i>
                </span><b>Hotline:<span style="font-weight: bold;color:#0052CC;"> 0243.3323.6714</span></b>
                </div>
                
            </div>
        </div>
    </div>
@endsection()
