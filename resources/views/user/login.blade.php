@extends('index.index')
@section('title')
    Đăng nhập
@endsection
@section('header')
    @include('page.header')
@endsection
@section('content')
    <div id="content">

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="card auth-card shadow-lg border-0">
                        <div class="card-body p-4 p-sm-5">
                            <div class="text-center mb-4">

                                <h4 class="mb-1 font-weight-bold text-dark">Đăng nhập</h4>
                            </div>

                            {{-- Form --}}
                            <form method="POST" action="{{ route('postLogin')}}" novalidate>
                                @csrf

                                {{-- Email --}}
                                <div class="form-group">
                                    <label for="email" class="sr-only">Email</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1
               0-2-.9-2-2V6c0-1.1.9-2 2-2zm0 0l8 7 8-7" stroke="#BFA084" stroke-width="1.4" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>

                                            </span>
                                        </div>
                                        <input id="email" type="email"
                                            class="form-control border-left-0 @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email') }}" required autocomplete="email"
                                            autofocus placeholder="Email của bạn">
                                    </div>
                                    @error('email')
                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- Password --}}
                                <div class="form-group">
                                    <label for="password" class="sr-only">Mật khẩu</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                    <path d="M17 11V8a5 5 0 0 0-10 0v3" stroke="#BFA084" stroke-width="1.4"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <rect x="3" y="11" width="18" height="10" rx="2.5"
                                                        stroke="#BFA084" stroke-width="1.4" />
                                                </svg>
                                            </span>
                                        </div>
                                        <input id="password" type="password"
                                            class="form-control border-left-0 @error('password') is-invalid @enderror"
                                            name="password" required autocomplete="current-password" placeholder="Mật khẩu">

                                    </div>
                                    @error('password')
                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-row align-items-center mb-3">
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                                {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label small text-muted" for="remember">Ghi nhớ
                                                tôi</label>
                                        </div>
                                    </div>
                                    <div class="col text-right">
                                        <a href="" class="small text-muted">Quên mật khẩu?</a>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-block btn-primary btn-login mb-3">
                                    Đăng nhập
                                </button>

                                <p class="text-center text-muted small mt-3">Bằng việc đăng nhập bạn đồng ý với <a
                                        href="#" class="text-muted">Điều khoản & Chính sách</a></p>
                                <p class="text-center small text-muted mt-4 mb-0">Chưa có tài khoản?
                                    <a href="{{ route('userRegister')}}" class="font-weight-bold" style="color:#C8A27E">Đăng ký</a>
                                </p>
                            </form>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer')
    @include('page.footer')
@endsection
