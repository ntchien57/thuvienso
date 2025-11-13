@section('header')
    <div id="header">
        <div id="top" style="width: 1140px;margin: auto;height: 55px;">
            <div id="logo" style="width: 342px;height: 55px;"><a href="{{ url('/') }}"><span
                        style="font-weight: bold;font-size: 300%;line-height: 30px;"><img src="{{ asset('image/logo.png') }}"
                            alt="" style="width: 180px;height: 55px;"></span></a></div>
            <div style="width: 440px;height: 30px;margin-top:13px;position: relative;bottom: 57px;left: 342px;">
                <form action="{{ route('timkiem_key') }}" method="get">
                    <input type="text" name="key" style="width: 394px; height: 30px; padding: 5px" id="searching"
                        placeholder="Nhập tên sách cần tìm">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <button type="submit"
                        style="width: 44px;float: right;height: 30px;text-align: center;font-weight: bold;background: #fff"><i
                            class="fa fa-search" aria-hidden="true"></i></button>
                </form>
                <div style="padding: 10px" id="producrslist"></div>
            </div>
            <div style="float: right;bottom:87px;position: relative;height: 30px;" id="login">
                <lable style="margin-right: 30px;">
                    @if (session()->has('user_id'))
                        <label id="users">
                            <div class="text-light"
                                style="width: 150px; border-radius: 10px;
                    height: 70px;line-height: 30px;text-align: center;overflow: hidden;"><i class="fa fa-user-circle mr-2" aria-hidden="true"></i>

                                {{ session('user_name') }}
                            </div>
                                <div id="logout">
                                    <div class=" py-2" style="border-bottom: 1px solid #ccc">
                                        <a href="{{ route('userProfile') }}" style="color: #000; font-weight:bold;">Thông tin cá nhân</a>
                                    </div>
                                    <div class="py-2">
                                        <a href="{{ route('userLogout') }}" style="color: #000; font-weight:bold;">Đăng xuất</a>
                                    </div>
                                </div>                               
                        </label>
                    @else
                        <label>
                            <div
                                style="background: #0052CC;width: 180px; border-radius: 10px;
                    height: 30px;line-height: 30px;text-align: center;overflow: hidden;">
                                <a href="{{ route('userLogin') }}" style="color: #fff; font-weight:bold;">Đăng nhập/Đăng
                                    ký</a>
                            </div>
                        </label>
                    @endif

            </div>
        </div>
    </div>
    <style>
        ul li:hover {
            background: lavender;
        }

        a:hover {
            text-decoration: none;
        }
    </style>
    <script>
        var row, qty;

        function changeqty(editButton, id) {
            row = $(editButton).parent();
            qty = $("#qty", row).val();
            location.assign('/laravel/sachhay/update-cart/' + id + '-' + qty);
        }
        $(document).ready(function() {
            $('#searching').keyup(function() {
                var key = $('#searching').val();
                url = "{{ route('timkiem') }}";
                if (key.length == 0) {
                    $('#producrslist').fadeIn();
                    $('#producrslist').html(key);
                } else {
                    $.ajax({
                        type: "POST",
                        url: url,
                        cache: false,
                        data: {
                            _token: $('#token').val(),
                            query: key
                        },
                        success: function(data) {
                            if (data.success) {
                                $('#producrslist').fadeIn();
                                $('#producrslist').html(data.success);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection()
