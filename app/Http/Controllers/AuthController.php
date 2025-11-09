<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Notifications\TemporaryPasswordNotification;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng ký
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký người dùng
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(10),
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng ký thành công!');
    }

    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'password.required' => 'Vui lòng nhập mật khẩu',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Ghi lại lịch sử đăng nhập
            $user = Auth::user();
            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'login_at' => now(),
            ]);
            
            // Kiểm tra nếu user là admin thì chuyển đến admin dashboard
            if ($user->is_admin) {
                return redirect()->route('admin.dashboard')->with('success', 'Chào mừng Admin đến với MixiShop!');
            }
            
            return redirect()->intended(route('dashboard'))->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->withInput();
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        // Cập nhật logout_at cho lần đăng nhập gần nhất
        if (Auth::check()) {
            $user = Auth::user();
            $lastLogin = $user->loginHistories()
                ->whereNull('logout_at')
                ->latest('login_at')
                ->first();
            
            if ($lastLogin) {
                $lastLogin->update(['logout_at' => now()]);
            }
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Đăng xuất thành công!');
    }

    /**
     * Hiển thị form quên mật khẩu
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Gửi mật khẩu tạm thời qua email (thay cho link reset)
     */
    public function sendResetLink(Request $request)
    {
        // Normalize email
        $email = trim(strtolower($request->input('email')));
        
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if user exists
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'Email không tồn tại trong hệ thống. Email bạn nhập: ' . $email
            ])->withInput();
        }

        // Tạo mật khẩu tạm
        $temporaryPassword = Str::password(12, symbols: true);

        // Cập nhật mật khẩu người dùng (đã băm)
        $user->forceFill([
            'password' => Hash::make($temporaryPassword),
            'remember_token' => Str::random(60),
        ])->save();

        // Gửi email thông báo mật khẩu tạm
        $user->notify(new TemporaryPasswordNotification($temporaryPassword));

        return back()->with(['status' => 'Mật khẩu tạm thời đã được gửi đến email của bạn. Vui lòng đăng nhập và đổi mật khẩu ngay.']);
    }

    /**
     * Hiển thị form reset mật khẩu
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Reset mật khẩu
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Mật khẩu đã được reset thành công!')
            : back()->withErrors(['email' => 'Không thể reset mật khẩu.']);
    }
}
