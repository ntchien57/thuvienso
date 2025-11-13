@extends('index.index')

@section('title', 'Hồ sơ cá nhân')
@section('header') @include('page.header') @endsection

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-lg">
                    <div class="card-header d-flex justify-content-between align-items-center"
                        style="background:#0052CC; color:#fff;">
                        <h5 class="mb-0">Hồ sơ cá nhân</h5>
                        <small class="text-white-50">ID: {{ $docgia->madocgia ?? $docgia->id }}</small>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('userProfile') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="text-center mb-4">
                                <div class="mb-2">
                                    @if ($docgia->anh)
                                        <img id="previewAvatar" src="{{ asset('storage/uploads/' . $docgia->anh) }}"
                                            alt="avatar" class="rounded-circle" width="120" height="120">
                                    @else
                                        <img id="previewAvatar" src="{{ asset('image/Vista_icons_08.png') }}" alt="avatar"
                                            class="rounded-circle" width="120" height="120">
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="btn btn-sm btn-outline-secondary">
                                        Chọn ảnh <input type="file" name="anh" id="anh" class="d-none"
                                            accept="image/*">
                                    </label>
                                    @error('anh')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="tendocgia"
                                        value="{{ old('tendocgia', $docgia->tendocgia) }}"
                                        class="form-control @error('tendocgia') is-invalid @enderror" required>
                                    @error('tendocgia')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Chức vụ</label>
                                    <input type="text" name="chucvu" value="{{ old('chucvu', $docgia->chucvu) }}"
                                        class="form-control">
                                    @error('chucvu')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            {{-- NEW: Khoa + Ngành --}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="khoa_id">Khoa</label>
                                    <select name="khoa_id" id="khoa_id" class="form-control">
                                        <option value="">-- Chọn khoa --</option>
                                        @if (isset($khoas) && $khoas->count())
                                            @foreach ($khoas as $khoa)
                                                <option value="{{ $khoa->id }}"
                                                    {{ old('khoa_id', $docgia->khoa_id) == $khoa->id ? 'selected' : '' }}>
                                                    {{ $khoa->tenkhoa ?? ($khoa->name ?? 'Khoa #' . $khoa->id) }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('khoa_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="nganh_id">Ngành</label>
                                    <select name="nganh_id" id="nganh_id" class="form-control">
                                        <option value="">-- Chọn ngành --</option>
                                        @if (isset($nganhs) && $nganhs->count())
                                            @foreach ($nganhs as $nganh)
                                                <option value="{{ $nganh->id }}"
                                                    {{ old('nganh_id', $docgia->nganh_id) == $nganh->id ? 'selected' : '' }}>
                                                    {{ $nganh->tennganh ?? ($nganh->name ?? 'Ngành #' . $nganh->id) }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('nganh_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            {{-- end Khoa + Ngành --}}

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Giới tính</label>
                                    <select name="gioitinh" class="form-control">
                                        <option value="">-- Chọn --</option>
                                        <option value="Nam"
                                            {{ old('gioitinh', $docgia->gioitinh) == 'Nam' ? 'selected' : '' }}>Nam
                                        </option>
                                        <option value="Nữ"
                                            {{ old('gioitinh', $docgia->gioitinh) == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Năm sinh</label>
                                    <input type="number" name="namsinh" value="{{ old('namsinh', $docgia->namsinh) }}"
                                        class="form-control" min="1900" max="{{ date('Y') }}">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>SĐT</label>
                                    <input type="text" name="sdt" value="{{ old('sdt', $docgia->sdt) }}"
                                        class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $docgia->email) }}"
                                    class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Địa chỉ</label>
                                <input type="text" name="diachi" value="{{ old('diachi', $docgia->diachi) }}"
                                    class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Quê quán</label>
                                <input type="text" name="quequan" value="{{ old('quequan', $docgia->quequan) }}"
                                    class="form-control">
                            </div>



                            <div class="text-right mt-3">
                                <a href="{{ url('/') }}" class="btn btn-secondary">Hủy</a>
                                <button type="submit" class="btn"
                                    style="background:#C8A27E;border:none;color:#16202A">Lưu thay đổi</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer') @include('page.footer') @endsection

@push('styles')
    <style>
        /* một vài style nhỏ cho preview */
        #previewAvatar {
            object-fit: cover;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Preview avatar khi chọn file
        document.getElementById('anh').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                document.getElementById('previewAvatar').src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
    </script>
@endpush
