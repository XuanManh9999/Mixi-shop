<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoginHistoryController extends Controller
{
    /**
     * Hiển thị danh sách lịch sử đăng nhập
     */
    public function index(Request $request)
    {
        $query = LoginHistory::with('user');

        // Tìm kiếm theo tên, email user
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Lọc theo user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        // Lọc theo IP
        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', "%{$request->get('ip_address')}%");
        }

        // Lọc theo khoảng thời gian
        if ($request->filled('date_from')) {
            $query->whereDate('login_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('login_at', '<=', $request->get('date_to'));
        }

        // Lọc theo trạng thái (đã đăng xuất hay chưa)
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->whereNull('logout_at');
            } elseif ($status === 'logged_out') {
                $query->whereNotNull('logout_at');
            }
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'login_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['login_at', 'logout_at', 'ip_address'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('login_at', 'desc');
        }

        // Số lượng hiển thị mỗi trang
        $perPage = $request->get('per_page', 20);
        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 20;
        }

        $loginHistories = $query->paginate($perPage)->withQueryString();

        // Thống kê
        $stats = [
            'total' => LoginHistory::count(),
            'today' => LoginHistory::whereDate('login_at', today())->count(),
            'this_week' => LoginHistory::whereBetween('login_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => LoginHistory::whereMonth('login_at', now()->month)->whereYear('login_at', now()->year)->count(),
            'active_sessions' => LoginHistory::whereNull('logout_at')->count(),
            'unique_users_today' => LoginHistory::whereDate('login_at', today())->distinct('user_id')->count('user_id'),
        ];

        // Lấy danh sách users để filter
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.login-history.index', compact('loginHistories', 'stats', 'users'));
    }

    /**
     * Xem chi tiết lịch sử đăng nhập của một user
     */
    public function userHistory(User $user, Request $request)
    {
        $query = $user->loginHistories();

        // Lọc theo khoảng thời gian
        if ($request->filled('date_from')) {
            $query->whereDate('login_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('login_at', '<=', $request->get('date_to'));
        }

        $loginHistories = $query->orderBy('login_at', 'desc')->paginate(20)->withQueryString();

        // Thống kê của user
        $stats = [
            'total_logins' => $user->loginHistories()->count(),
            'last_login' => $user->lastLogin(),
            'active_session' => $user->loginHistories()->whereNull('logout_at')->latest('login_at')->first(),
        ];

        return view('admin.login-history.user-history', compact('user', 'loginHistories', 'stats'));
    }
}
