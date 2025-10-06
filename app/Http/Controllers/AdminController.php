<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Hiển thị trang admin dashboard
     */
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalAdmins = User::where('is_admin', 1)->count();
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();
        
        return view('admin.dashboard', compact('totalUsers', 'totalAdmins', 'recentUsers'));
    }

    /**
     * Hiển thị danh sách users với tìm kiếm và lọc
     */
    public function users(Request $request)
    {
        $query = User::query();
        
        // Tìm kiếm theo tên, email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Lọc theo quyền admin
        if ($request->filled('role')) {
            $role = $request->get('role');
            if ($role === 'admin') {
                $query->where('is_admin', 1);
            } elseif ($role === 'user') {
                $query->where('is_admin', 0);
            }
        }
        
        // Lọc theo ngày tạo
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }
        
        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['name', 'email', 'created_at', 'is_admin'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        // Số lượng hiển thị mỗi trang
        $perPage = $request->get('per_page', 10);
        $allowedPerPage = [5, 10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }
        
        $users = $query->paginate($perPage)->withQueryString();
        
        // Thống kê
        $stats = [
            'total' => User::count(),
            'admins' => User::where('is_admin', 1)->count(),
            'users' => User::where('is_admin', 0)->count(),
            'today' => User::whereDate('created_at', today())->count(),
            'this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];
        
        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Hiển thị form tạo user mới
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Lưu user mới
     */
    public function storeUser(Request $request)
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

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_admin' => $request->has('is_admin') ? 1 : 0,
        ]);

        return redirect()->route('admin.users')->with('success', 'Tạo user thành công!');
    }

    /**
     * Hiển thị form chỉnh sửa user
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật user
     */
    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_admin' => $request->has('is_admin') ? 1 : 0,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'Cập nhật user thành công!');
    }

    /**
     * Xóa user
     */
    public function deleteUser(User $user)
    {
        // Không cho phép xóa admin cuối cùng
        if ($user->is_admin && User::where('is_admin', 1)->count() <= 1) {
            return back()->with('error', 'Không thể xóa admin cuối cùng!');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'Xóa user thành công!');
    }

    /**
     * Toggle admin status
     */
    public function toggleAdmin(User $user)
    {
        // Không cho phép bỏ quyền admin của admin cuối cùng
        if ($user->is_admin && User::where('is_admin', 1)->count() <= 1) {
            return back()->with('error', 'Không thể bỏ quyền admin cuối cùng!');
        }

        $user->update(['is_admin' => !$user->is_admin]);
        
        $message = $user->is_admin ? 'Đã cấp quyền admin!' : 'Đã bỏ quyền admin!';
        return back()->with('success', $message);
    }

    /**
     * Bulk actions cho nhiều users
     */
    public function bulkAction(Request $request)
    {
        $action = $request->get('action');
        $userIds = $request->get('user_ids', []);
        
        if (empty($userIds)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một user!');
        }
        
        $users = User::whereIn('id', $userIds)->get();
        $currentUserId = auth()->id();
        
        switch ($action) {
            case 'delete':
                // Không cho phép xóa chính mình
                $users = $users->where('id', '!=', $currentUserId);
                
                // Kiểm tra không xóa hết admin
                $remainingAdmins = User::where('is_admin', 1)
                    ->whereNotIn('id', $users->pluck('id'))
                    ->count();
                
                if ($remainingAdmins < 1) {
                    return back()->with('error', 'Không thể xóa tất cả admin!');
                }
                
                $count = $users->count();
                User::whereIn('id', $users->pluck('id'))->delete();
                return back()->with('success', "Đã xóa {$count} user thành công!");
                
            case 'make_admin':
                $users = $users->where('id', '!=', $currentUserId)->where('is_admin', 0);
                $count = $users->count();
                User::whereIn('id', $users->pluck('id'))->update(['is_admin' => 1]);
                return back()->with('success', "Đã cấp quyền admin cho {$count} user!");
                
            case 'remove_admin':
                $users = $users->where('id', '!=', $currentUserId)->where('is_admin', 1);
                
                // Kiểm tra không bỏ hết quyền admin
                $remainingAdmins = User::where('is_admin', 1)
                    ->whereNotIn('id', $users->pluck('id'))
                    ->count();
                
                if ($remainingAdmins < 1) {
                    return back()->with('error', 'Phải có ít nhất một admin!');
                }
                
                $count = $users->count();
                User::whereIn('id', $users->pluck('id'))->update(['is_admin' => 0]);
                return back()->with('success', "Đã bỏ quyền admin của {$count} user!");
                
            default:
                return back()->with('error', 'Hành động không hợp lệ!');
        }
    }

    /**
     * Export users to CSV
     */
    public function exportUsers(Request $request)
    {
        $query = User::query();
        
        // Áp dụng các filter giống như trong users()
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('role')) {
            $role = $request->get('role');
            if ($role === 'admin') {
                $query->where('is_admin', 1);
            } elseif ($role === 'user') {
                $query->where('is_admin', 0);
            }
        }
        
        $users = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'users_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, ['ID', 'Tên', 'Email', 'Số điện thoại', 'Quyền', 'Ngày tạo']);
            
            // Data rows
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone ?: '',
                    $user->is_admin ? 'Admin' : 'User',
                    $user->created_at->format('d/m/Y H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}