@extends('index.index')

@section('title', 'Sách đang mượn')
@section('header') @include('page.header') @endsection

@section('content')
    <style>
        .star-rating {
            font-size: 26px;
            cursor: pointer;
        }

        .star {
            color: #ccc;
            /* sao trắng (xám nhạt) */
            transition: color 0.2s;
        }

        .star.selected {
            color: #f1c40f;
            /* sao vàng */
        }
    </style>
    <div class="container mt-4">
        <h4 class="mb-3">📚 Lịch sử mượn sách</h4>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($borrows->isEmpty())
            <div class="alert alert-info">Bạn hiện không mượn sách nào.</div>
        @else
            <div class="row">
                @foreach ($borrows as $b)
                    <div class="col-md-6 mb-4">
                        <div class="card" style="height: 200px">
                            <div class="row no-gutters">
                                <div class="col-4">
                                    @if (!empty($b->anh))
                                        <img src="{{ asset('storage/uploads/' . $b->anh) }}" class="img-fluid"
                                            style="height:198px; object-fit:cover;">
                                    @else
                                        <img src="{{ asset('images/default-book.png') }}" class="img-fluid"
                                            style="height:198px; object-fit:cover;">
                                    @endif
                                </div>
                                <div class="col-8">
                                    <div class="card-body p-2">
                                        <a
                                            href="{{ route('chitietsanpham', ['id' => $b->sach_id ?? ($b->sach->id ?? 0)]) }}">
                                            <h6 class="card-title mb-1">{{ $b->tensach ?? 'Không rõ tên sách' }}</h6>
                                        </a>
                                        <p class="mb-1 small text-muted">Số lượng: {{ $b->soluong ?? 1 }}</p>
                                        <p class="mb-1 small">Ngày mượn:
                                            {{ \Carbon\Carbon::parse($b->ngaymuon)->format('d/m/Y H:i') }}</p>
                                        <p class="mb-1 small">Hạn trả:
                                            <strong>{{ \Carbon\Carbon::parse($b->ngaytra)->format('d/m/Y') }}</strong>
                                        </p>

                                        @if ($b->is_overdue)
                                            @if ($b->tinhtrang == 0)
                                                <span class="badge badge-primary">Đang mượn sách</span>
                                            @elseif($b->tinhtrang == 2)
                                                <span class="badge badge-danger">Hết hạn mượn</span>
                                            @else
                                                <span class="badge badge-success">Đã trả sách</span>
                                            @endif
                                            <span class="badge badge-danger">Quá hạn {{ $b->days_left }} ngày</span>
                                        @else
                                            @if ($b->tinhtrang == 0)
                                                <span class="badge badge-primary">Đang mượn sách</span>
                                            @elseif($b->tinhtrang == 2)
                                                <span class="badge badge-danger">Hết hạn mượn</span>
                                            @else
                                                <span class="badge badge-success">Đã trả sách</span>
                                            @endif
                                            <span class="badge badge-success">Còn {{ $b->days_left }} ngày</span>
                                        @endif

                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-outline-warning ml-2 review-btn"
                                                data-toggle="modal" data-target="#reviewModal"
                                                data-sachid="{{ $b->sach_id ?? ($b->sach->id ?? 0) }}"
                                                data-title="{{ $b->tensach ?? ($b->sach->tensach ?? 'Không rõ') }}">
                                                Đánh giá
                                            </button>

                                            @php
                                                // tính lại trạng thái quá hạn nếu chưa có is_overdue
                                                $now = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
                                                $ngaytra = isset($b->ngaytra)
                                                    ? \Carbon\Carbon::parse($b->ngaytra)
                                                    : null;
                                                $overdue = $ngaytra ? $now->gt($ngaytra) : false;
                                            @endphp

                                            @if ($overdue || (isset($b->is_overdue) && $b->is_overdue))
                                                <span class="btn btn-sm btn-danger ml-2">Quá hạn mượn sách</span>
                                            @else
                                                <!-- Nút mở modal: truyền data-id (id phiếu mượn) và data-title (tên sách) -->
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-success ml-2 extend-btn"
                                                    data-toggle="modal" data-target="#extendModal"
                                                    data-id="{{ $b->id }}"
                                                    data-title="{{ $b->tensach ?? ($b->sach->tensach ?? 'Không rõ') }}"
                                                    data-default-days="7">
                                                    Gia hạn mượn sách
                                                </button>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">
                {{ $borrows->links() }}
            </div>
        @endif
    </div>
    <!-- Modal Gia hạn -->
    <div class="modal fade" id="extendModal" tabindex="-1" role="dialog" aria-labelledby="extendModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="extendForm" method="POST" action="" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="extendModalLabel">Gia hạn mượn sách</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p><strong id="extendBookTitle"></strong></p>

                    <div class="form-group">
                        <label for="extend_days">Số ngày muốn gia hạn</label>
                        <input type="number" id="extend_days" name="days" class="form-control" min="1"
                            value="7" required>
                        <small class="form-text text-muted">Nhập số ngày (ví dụ 7). Nếu bạn muốn thay đổi mặc định, hãy sửa
                            trước khi gửi.</small>
                    </div>

                    <!-- Hidden: id phiếu mượn sẽ dùng để gán action -->
                    <input type="hidden" id="extend_borrow_id" name="borrow_id" value="">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">Xác nhận gia hạn</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal Đánh giá -->
    <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="reviewForm" method="POST" action="" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Đánh giá sách</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p>Bạn đang đánh giá: <strong id="reviewBookTitle"></strong></p>

                    <!-- Star rating -->
                    <div class="form-group">
                        <label class="d-block mb-1">Đánh giá</label>
                        <div id="starRating" class="star-rating">
                            <i class="star fa fa-star-o" data-value="1"></i>
                            <i class="star fa fa-star-o" data-value="2"></i>
                            <i class="star fa fa-star-o" data-value="3"></i>
                            <i class="star fa fa-star-o" data-value="4"></i>
                            <i class="star fa fa-star-o" data-value="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="rating-value" value="0">
                    </div>

                    <div class="form-group">
                        <label>Nội dung bình luận</label>
                        <textarea name="content" id="review_content" class="form-control" rows="4"
                            placeholder="Viết cảm nghĩ của bạn... (tuỳ chọn)"></textarea>
                    </div>

                    <!-- hidden -->
                    <input type="hidden" id="review_sach_id" name="sach_id" value="">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khi modal được mở từ nút, lấy data-* từ nút và fill vào form
            $('#extendModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // nút đã click
                var borrowId = button.data('id'); // id phiếu mượn
                var title = button.data('title'); // tên sách
                var defaultDays = button.data('default-days') || 7;

                // điền nội dung modal
                $('#extendBookTitle').text(title);
                $('#extend_days').val(defaultDays);
                $('#extend_borrow_id').val(borrowId);

                // set action của form: /user/borrow/extend/{id}
                // Lưu ý: đổi baseUrl nếu route của bạn khác
                var base = '{{ url('') }}'; // root URL
                var actionUrl = base + '/user/muon-sach/gia-han/' + borrowId;
                $('#extendForm').attr('action', actionUrl);
            });

            // (tùy chọn) trên submit có thể show confirm; nhưng form sẽ gửi bình thường
            $('#extendForm').on('submit', function() {
                // optional: disable button to prevent double submit
                $(this).find('button[type="submit"]').attr('disabled', true).text('Đang xử lý...');
            });
        });
    </script>

    <script>
        $(document).ready(function() {

            function resetStars() {
                document.querySelectorAll('.star').forEach(s => {
                    s.classList.remove('selected');
                    s.classList.remove('fa-star');
                    s.classList.add('fa-star-o');
                });
            }

            document.querySelectorAll('.star').forEach(star => {
                star.addEventListener('click', function() {
                    let rating = this.getAttribute('data-value');

                    // Gán vào input hidden để gửi form
                    document.getElementById('rating-value').value = rating;

                    // Reset sao
                    resetStars();

                    // Tô vàng số sao đã chọn
                    for (let i = 1; i <= rating; i++) {
                        let s = document.querySelector('.star[data-value="' + i + '"]');
                        s.classList.add('selected');
                        s.classList.remove('fa-star-o');
                        s.classList.add('fa-star');
                    }
                });
            });

            $('#reviewModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var sachId = button.data('sachid');
                var title = button.data('title') || 'Không rõ';

                $('#reviewBookTitle').text(title);
                $('#review_sach_id').val(sachId);

                var base = '{{ url('') }}';
                $('#reviewForm').attr('action', base + '/user/review/' + sachId);

                // Reset form
                $('#reviewForm')[0].reset();
                $('#rating-value').val(0);

                // Reset màu sao
                resetStars();
            });

            $('#reviewForm').on('submit', function(e) {
                var rating = $('#rating-value').val();

                if (rating == 0 || rating === "") {
                    e.preventDefault();
                    alert('Vui lòng chọn số sao (1 - 5).');
                    return false;
                }

                $(this).find('button[type="submit"]').attr('disabled', true).text('Đang gửi...');
            });

        });
    </script>

@endsection

@section('footer') @include('page.footer') @endsection
