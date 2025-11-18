<?php

namespace App\Http\Controllers;

use App\Like;
use App\Qltv_Khoa;
use Carbon\Carbon;
use App\Qltv_Nganh;
use App\Qltv_Docgia;
use App\Qltv_Theloai;
use App\Qltv_Muonsach;
use App\Comment;
use App\Mail\BorrowBookMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function getlogin()
    {
        return view('user.login');
    }

    public function getRegister()
    {
        return view('user.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'tendocgia' => 'required|string|max:255',
            'chucvu'    => 'required|string|max:255',
            'email'     => 'required|email|unique:qltv_docgia,email',
            'password'  => 'required|min:6|confirmed',
        ], [
            'email.unique' => 'Email này đã được sử dụng.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        // Tạo mã độc giả tự động
        $madocgia = 'DG_' . time();

        Qltv_Docgia::create([
            'madocgia'  => $madocgia,
            'tendocgia' => $request->tendocgia,
            'chucvu'    => $request->chucvu,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        return redirect()->route('userRegister')->with('success', 'Đăng ký thành công! Bạn có thể đăng nhập ngay.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $user = Qltv_Docgia::where('email', $email)->first();

        if ($user && Hash::check($password, $user->password)) {
            $request->session()->put('user_id', $user->id);
            $request->session()->put('user_name', $user->tendocgia);
            $request->session()->put('user_email', $user->email);
            $request->session()->put('user_role', $user->chucvu);

            return redirect('/')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->withInput($request->except('password'));
    }


    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('userLogin');
    }

    public function profile(Request $request)
    {
        // Kiểm tra session đăng nhập
        if (!$request->session()->has('user_id')) {
            return redirect()->route('userLogin')->with('error', 'Vui lòng đăng nhập trước');
        }

        $userId = $request->session()->get('user_id');

        // Lấy user kèm quan hệ nếu cần
        $docgia = Qltv_Docgia::with(['khoa', 'nganh'])->find($userId);

        if (!$docgia) {
            return redirect()->route('userLogin')->with('error', 'Không tìm thấy người dùng');
        }

        // Lấy danh sách khoa & ngành để hiển thị dropdown
        $khoas  = Qltv_Khoa::orderBy('tenkhoa')->get();   // hoặc ->all()
        $nganhs = Qltv_Nganh::orderBy('tennganh')->get(); // hoặc ->all()

        // Nếu request POST => xử lý cập nhật
        if ($request->isMethod('post')) {
            $rules = [
                'tendocgia' => 'required|string|max:255',
                'chucvu'    => 'nullable|string|max:255',
                'gioitinh'  => 'nullable|string|max:50',
                'namsinh'   => 'nullable|integer|min:1900|max:' . date('Y'),
                'diachi'    => 'nullable|string|max:500',
                'sdt'       => 'nullable|string|max:20',
                'email'     => 'required|email|unique:qltv_docgia,email,' . $docgia->id,
                'quequan'   => 'nullable|string|max:255',
                'anh'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'khoa_id'   => 'nullable|integer|exists:qltv_khoa,id',
                'nganh_id'  => 'nullable|integer|exists:qltv_nganh,id',
                'password'  => 'nullable|string|min:6|confirmed',
            ];

            $validated = $request->validate($rules);

            // Cập nhật các trường cơ bản
            $docgia->tendocgia = $validated['tendocgia'];
            $docgia->chucvu    = $validated['chucvu'] ?? $docgia->chucvu;
            $docgia->gioitinh  = $validated['gioitinh'] ?? $docgia->gioitinh;
            $docgia->namsinh   = $validated['namsinh'] ?? $docgia->namsinh;
            $docgia->diachi    = $validated['diachi'] ?? $docgia->diachi;
            $docgia->sdt       = $validated['sdt'] ?? $docgia->sdt;
            $docgia->email     = $validated['email'];
            $docgia->quequan   = $validated['quequan'] ?? $docgia->quequan;

            // Lưu khoa & ngành (nếu có, hoặc null để giữ như cũ)
            if (array_key_exists('khoa_id', $validated)) {
                $docgia->khoa_id = $validated['khoa_id'] ?: null;
            }
            if (array_key_exists('nganh_id', $validated)) {
                $docgia->nganh_id = $validated['nganh_id'] ?: null;
            }

            // Xử lý upload ảnh (nếu có)
            if ($request->hasFile('anh')) {
                $file = $request->file('anh');
                $path = $file->move('storage/uploads'); // lưu storage/app/storage/uploads
                $filename = basename($path);

                // Xoá file cũ nếu có
                if ($docgia->anh && Storage::exists('storage/uploads/' . $docgia->anh)) {
                    Storage::delete('storage/uploads/' . $docgia->anh);
                }

                $docgia->anh = $filename;
            }

            // Xử lý mật khẩu (nếu người dùng nhập)
            if (!empty($validated['password'])) {
                $docgia->password = Hash::make($validated['password']);
            }

            $docgia->save();

            // Cập nhật session (nếu đổi tên/email)
            $request->session()->put('user_name', $docgia->tendocgia);
            $request->session()->put('user_email', $docgia->email);

            return redirect()->route('userProfile')->with('success', 'Cập nhật hồ sơ thành công');
        }

        // Nếu GET => hiển thị form, truyền thêm $khoas, $nganhs
        return view('user.profile', compact('docgia', 'khoas', 'nganhs'));
    }

    public function like(Request $request)
    {
        if (!$request->session()->has('user_id')) {
            return back()->with('error', 'Bạn cần đăng nhập để thích');
        }

        $userId = $request->session()->get('user_id');
        $sachId = $request->id; // hoặc $request->sach_id tùy bạn truyền lên

        if (!$sachId) {
            return back()->with('error', 'Không xác định được sách');
        }

        $exists = \App\Like::where('user_id', $userId)
            ->where('sach_id', $sachId)
            ->first();

        if ($exists) {
            return back()->with('error', 'Sách này đã có trong danh sách yêu thích');
        }

        // Thêm vào bảng like
        \App\Like::create([
            'user_id' => $userId,
            'sach_id' => $sachId
        ]);

        return back()->with('message', 'Đã thêm vào yêu thích');
    }

    public function likeList(Request $request)
    {
        // Kiểm tra đăng nhập
        if (!$request->session()->has('user_id')) {
            return redirect()->route('userLogin')
                ->with('error', 'Vui lòng đăng nhập để xem danh sách yêu thích');
        }

        $userId = $request->session()->get('user_id');

        // Join bảng sách
        $theloai = Qltv_Theloai::paginate(12);
        $likes = Like::where('user_id', $userId)
            ->join('qltv_sach', 'yeuthich.sach_id', '=', 'qltv_sach.id')
            ->select('qltv_sach.*', 'yeuthich.id as like_id')
            ->get();

        return view('user.like_list', compact('likes', 'theloai'));
    }

    public function muonSach(Request $request)
    {
        // 1. Kiểm tra đăng nhập
        if (!session()->has('user_id')) {
            return redirect()->route('userLogin')->with('error', 'Vui lòng đăng nhập để mượn sách');
        }

        $userId = session('user_id');
        $userEmail = session('user_email');

        // 2. Kiểm tra xem sách đã được mượn bởi user chưa
        $check = Qltv_Muonsach::where('docgia_id', $userId)
            ->where('sach_id', $request->sach_id)
            ->where('tinhtrang', '0')   // 0 = đang mượn
            ->first();

        if ($check) {
            return back()->with('error', 'Bạn đã mượn sách này rồi, không thể mượn trùng.');
        }

        // 3. Tạo phiếu mượn
        $dt = Carbon::now('Asia/Ho_Chi_Minh');

        $muonsach = new Qltv_Muonsach();
        $muonsach->mamuon     = 'MM_' . time();
        $muonsach->soluong    = 1;
        $muonsach->ngaymuon   = $dt->toDateTimeString();
        $muonsach->hantra     = $request->hantra;
        $muonsach->ngaytra    = $dt->copy()->addDays($request->hantra);
        $muonsach->tinhtrang  = '0';           // đang mượn
        $muonsach->thuthu_id  = 1;
        $muonsach->docgia_id  = $userId;
        $muonsach->sach_id    = $request->sach_id;
        $muonsach->save();

        // Lấy thông tin sách để đưa vào email
        $sach = \App\Qltv_Sach::find($request->sach_id);

        // Gửi email
        if ($userEmail) {
            Mail::to($userEmail)->send(new BorrowBookMail($sach, $muonsach->ngaytra));
        }

        return redirect()->back()->with('message', 'Mượn sách thành công');
    }

    public function borrowHistory(Request $request)
    {
        // 1. Kiểm tra đăng nhập
        if (!$request->session()->has('user_id')) {
            return redirect()->route('userLogin')->with('error', 'Vui lòng đăng nhập để xem lịch sử mượn.');
        }

        $userId = $request->session()->get('user_id');
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        // 2. Lấy danh sách mượn đang active (tinhtrang = 0). 
        // Thử dùng relation 'sach' nếu model Qltv_Muonsach đã định nghĩa.
        $borrows = Qltv_Muonsach::where('docgia_id', $userId)
            ->where('tinhtrang',0)
            ->with(['sach']) // nếu relation tồn tại: public function sach() { return $this->belongsTo(Qltv_Sach::class,'sach_id'); }
            ->orderBy('ngaymuon', 'desc')
            ->paginate(10);

        // 3. Nếu relation 'sach' không tồn tại (an toàn): fallback join
        if ($borrows->isEmpty() && Qltv_Muonsach::where('docgia_id', $userId)->exists()) {
            // fallback: join lấy thông tin sách
            $borrows = Qltv_Muonsach::where('docgia_id', $userId)
                ->where('tinhtrang',0)
                ->join('qltv_sach', 'qltv_muonsach.sach_id', '=', 'qltv_sach.id')
                ->select('qltv_muonsach.*', 'qltv_sach.tensach', 'qltv_sach.anh')
                ->orderBy('ngaymuon', 'desc')
                ->paginate(10);
        }

        // 4. Thêm trường tính toán (days_left, is_overdue) cho mỗi item
        $borrows->getCollection()->transform(function ($item) use ($now) {
            // Access ngaytra từ model; nếu đã join thì ngaytra ở item->ngaytra
            $ngaytra = isset($item->ngaytra) ? Carbon::parse($item->ngaytra) : null;
            if ($ngaytra) {
                if ($now->lte($ngaytra)) {
                    $daysLeft = $now->diffInDays($ngaytra);
                    $isOverdue = false;
                } else {
                    $daysLeft = $now->diffInDays($ngaytra);
                    $isOverdue = true;
                }
            } else {
                $daysLeft = null;
                $isOverdue = false;
            }

            // gán thêm (màu hiển thị có thể dùng trong view)
            $item->days_left = $daysLeft;
            $item->is_overdue = $isOverdue;

            // nếu relation sach tồn tại, đảm bảo có các thuộc tính tensach, anh
            if (isset($item->sach) && $item->sach) {
                $item->tensach = $item->sach->tensach ?? ($item->tensach ?? '');
                $item->anh     = $item->sach->anh ?? ($item->anh ?? null);
            }

            return $item;
        });

        return view('user.borrow_history', compact('borrows'));
    }

    public function extendBorrow(Request $request, $id)
    {
        // 1. Kiểm tra đăng nhập
        if (!$request->session()->has('user_id')) {
            return redirect()->route('userLogin')->with('error', 'Vui lòng đăng nhập để thực hiện thao tác này.');
        }

        $userId = $request->session()->get('user_id');

        // 2. Lấy phiếu mượn của user (chỉ lấy các phiếu đang mượn)
        $muon = Qltv_Muonsach::where('id', $id)
            ->where('docgia_id', $userId)
            ->where('tinhtrang', '0') // 0 = đang mượn
            ->first();

        if (!$muon) {
            return back()->with('error', 'Không tìm thấy phiếu mượn hợp lệ.');
        }

        // 3. Kiểm tra hạn hiện tại
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $ngaytra = Carbon::parse($muon->ngaytra);

        if ($now->gt($ngaytra)) {
            return back()->with('error', 'Không thể gia hạn — sách đã quá hạn trả.');
        }

        // 4. Nhận số ngày gửi từ form
        $days = intval($request->days);

        if ($days <= 0) {
            return back()->with('error', 'Số ngày gia hạn không hợp lệ.');
        }

        // 5. Nếu có giới hạn số lần gia hạn (tùy DB có cột giahan_count)
        $maxExtensions = 2;

        if (array_key_exists('giahan_count', $muon->getAttributes())) {
            $current = intval($muon->giahan_count ?? 0);

            if ($current >= $maxExtensions) {
                return back()->with('error', "Bạn đã đạt giới hạn {$maxExtensions} lần gia hạn.");
            }

            $muon->giahan_count = $current + 1;
        }

        // 6. Cập nhật hạn trả mới
        $muon->ngaytra = $ngaytra->addDays($days)->format('Y-m-d H:i:s');
        $muon->save();

        return back()->with(
            'message',
            "Gia hạn thành công thêm {$days} ngày. Hạn trả mới: " . Carbon::parse($muon->ngaytra)->format('d/m/Y')
        );
    }

    public function postReview(Request $request, $sachId)
    {
        // kiểm tra đăng nhập
        if (!session()->has('user_id')) {
            return redirect()->route('userLogin')->with('error', 'Vui lòng đăng nhập để đánh giá');
        }

        $userId = session('user_id');

        // validate
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'nullable|string|max:1000',
        ]);

        // Kiểm tra nếu muốn: không cho đánh giá trùng (1 user chỉ 1 review / sách)
        $exists = Comment::where('user_id', $userId)->where('sach_id', $sachId)->first();
        if ($exists) {
            // cập nhật lại nếu muốn
            $exists->rating = $data['rating'];
            $exists->content = $data['content'] ?? $exists->content;
            $exists->save();
            return back()->with('message', 'Bạn đã cập nhật đánh giá thành công.');
        }

        // Lưu mới
        Comment::create([
            'user_id' => $userId,
            'sach_id' => $sachId,
            'rating'  => $data['rating'],
            'content' => $data['content'] ?? null,
            // 'created_at' nếu cần (model timestamps)
        ]);

        return back()->with('message', 'Cảm ơn bạn đã đánh giá sách!');
    }
}
