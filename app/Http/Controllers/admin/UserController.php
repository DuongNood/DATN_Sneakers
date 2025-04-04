<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    const PATH_VIEW = 'admin.users.';

    public function index(Request $request)
    {
        $query = User::query();

        // Xử lý tìm kiếm
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('email', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('address', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Xử lý lọc theo vai trò (role)
        if ($request->has('role_id') && !empty($request->role_id)) {
            $query->where('role_id', $request->role_id);
        }

        $roles = Role::all();
        $data = $query->latest('id')->paginate(10);

        return view(self::PATH_VIEW . __FUNCTION__, compact('data', 'roles'));
    }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     $roles = Role::all();
    //     return view(self::PATH_VIEW . __FUNCTION__, compact('roles'));
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'name'      => 'required|max:255',
    //         'email'     => ['required', 'email', 'max:100', Rule::unique('users')],
    //         'phone'     => 'required|string|max:20',
    //         'address'   => 'required|max:255',
    //         'password'  => 'required|string|min:8|confirmed',
    //         'role_id'   => 'required|exists:roles,id',
    //     ]);

    //     try {
    //         $data['password'] = bcrypt($data['password']);

    //         User::query()->create($data);

    //         return redirect()
    //             ->route('admin/users.index')
    //             ->with('success', true);
    //     } catch (\Throwable $th) {
    //         return back()
    //             ->with('success', false)
    //             ->with('error', $th->getMessage());
    //     }
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(User $user)
    // {
    //     $roles = Role::all();
    //     return view(self::PATH_VIEW . __FUNCTION__, compact('user', 'roles'));
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(User $user)
    // {
    //     $roles = Role::all();
    //     return view(self::PATH_VIEW . __FUNCTION__, compact('user', 'roles'));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, User $user)
    // {
    //     $data = $request->validate([
    //         'name'      => 'required|max:255',
    //         'email'     => ['required', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
    //         'phone'     => 'required|string|max:20',
    //         'address'   => 'required|max:255',
    //         'password'  => 'required|string|min:8|confirmed',
    //         'role_id'   => 'required|exists:roles,id',
    //     ]);

    //     try {
    //         if(isset($data['password'])) {
    //             $data['password'] = bcrypt($data['password']);
    //         }

    //         $user->update($data);

    //         return back()
    //             ->with('success', true);
    //     } catch (\Throwable $th) {
    //         return back()
    //             ->with('success', false)
    //             ->with('error', $th->getMessage());
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $currentUser = Auth::user();

        // Kiểm tra quyền truy cập
        if (!$currentUser->hasPermission('manage_users')) {
            return response()->json(['message' => 'Bạn không có quyền xóa người dùng.'], 403);
        }

        // Kiểm tra vai trò của user cần xóa
        if ($currentUser->role->id === 1 && $user->role->id === 1) {
            return response()->json(['message' => 'Bạn không thể xóa tài khoản Admin.'], 403);
        }

        if ($currentUser->role->id === 2 && $user->role->id !== 3) {
            return response()->json(['message' => 'Bạn chỉ có thể xóa tài khoản User thường.'], 403);
        }

        // Xóa user (xóa mềm)
        $user->delete();

        return response()->json(['message' => 'Xóa người dùng thành công.']);
    }
}
