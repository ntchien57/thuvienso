@extends('index.index')
@section('title', 'Đăng ký tài khoản')
@section('header')
    @include('page.header')
@endsection

@section('content')
<div id="content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card auth-card shadow-lg border-0">
                    <div class="card-body p-4 p-sm-5">
                        <div class="text-center mb-4">
                            <h4 class="mb-1 font-weight-bold text-dark">Đăng ký tài khoản</h4>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form method="POST" action="{{ route('postRegister') }}">
                            @csrf

                            {{-- Họ tên --}}
                            <div class="form-group">
                                <label for="tendocgia" class="sr-only">Họ tên</label>
                                <input type="text" name="tendocgia" id="tendocgia"
                                       class="form-control @error('tendocgia') is-invalid @enderror"
                                       value="{{ old('tendocgia') }}" placeholder="Họ và tên" required>
                                @error('tendocgia') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Chức vụ --}}
                            <div class="form-group">
                                <label for="chucvu" class="sr-only">Chức vụ</label>
                                <input type="text" name="chucvu" id="chucvu"
                                       class="form-control @error('chucvu') is-invalid @enderror"
                                       value="{{ old('chucvu') }}" placeholder="Chức vụ (VD: Sinh viên, Giảng viên,...)">
                                @error('chucvu') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="form-group">
                                <label for="email" class="sr-only">Email</label>
                                <input type="email" name="email" id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" placeholder="Email" required>
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Mật khẩu --}}
                            <div class="form-group">
                                <label for="password" class="sr-only">Mật khẩu</label>
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Mật khẩu" required>
                                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Xác nhận mật khẩu --}}
                            <div class="form-group">
                                <label for="password_confirmation" class="sr-only">Xác nhận mật khẩu</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control" placeholder="Nhập lại mật khẩu" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block" style="background:#C8A27E;border:none">
                                Đăng ký
                            </button>

                            <p class="text-center small text-muted mt-3">
                                Đã có tài khoản? <a href="{{ route('userLogin') }}" style="color:#C8A27E">Đăng nhập</a>
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
