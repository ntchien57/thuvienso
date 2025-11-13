<?php

namespace App\Http\Controllers;

use App\Qltv_Docgia;
use App\Qltv_Nganh;
use App\Qltv_Khoa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
}
