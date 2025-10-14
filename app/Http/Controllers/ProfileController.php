<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\Models\Order;

class ProfileController extends Controller
{
    /**
     * Dashboard user
     */
    public function dashboard()
    {
        $user = Auth::user();
        $orderCount = $user->orders()->count();
        $pendingOrders = $user->orders()->where('status', 'pending')->count();
        $completedOrders = $user->orders()->where('status', 'delivered')->count();
        $totalSpent = $user->orders()->where('payment_status', 'paid')->sum('total_amount');

        return view('dashboard', compact('user', 'orderCount', 'pendingOrders', 'completedOrders', 'totalSpent'));
    }

    /**
     * Hiển thị trang quản lý thông tin cá nhân
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date|before:today',
        ], [
            'name.required' => 'Tên là bắt buộc.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự.',
            'date_of_birth.date' => 'Ngày sinh không hợp lệ.',
            'date_of_birth.before' => 'Ngày sinh phải trước ngày hôm nay.',
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')
                        ->with('success', 'Cập nhật thông tin cá nhân thành công!');
    }

    /**
     * Đổi mật khẩu
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ]);

        $user = Auth::user();

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Mật khẩu hiện tại không đúng.'
            ]);
        }

        // Cập nhật mật khẩu mới
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('profile.show')
                        ->with('success', 'Đổi mật khẩu thành công!');
    }

    /**
     * Xóa tài khoản (tùy chọn)
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu để xác nhận xóa tài khoản.',
        ]);

        $user = Auth::user();

        // Kiểm tra mật khẩu
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Mật khẩu không đúng.'
            ]);
        }

        // Đăng xuất và xóa tài khoản
        Auth::logout();
        $user->delete();

        return redirect()->route('home')
                        ->with('success', 'Tài khoản đã được xóa thành công.');
    }
}
