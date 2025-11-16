<h2>📚 Thông báo mượn sách</h2>

<p>Bạn vừa mượn sách: <strong>{{ $sach->tensach }}</strong></p>

<p>Hạn trả: <strong>{{ \Carbon\Carbon::parse($hantra)->format('d/m/Y') }}</strong></p>

<p>Chúc bạn đọc sách vui vẻ!</p>
