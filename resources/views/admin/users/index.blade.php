@extends('admin.layouts.master')
@section('title')
    Danh sách người dùng
@endsection
@section('content')
    <div class="container">
        <div class="py-3 d-flex align-items-center justify-content-between">
            <h4 class="fs-18 fw-semibold m-0">Danh Sách Người Dùng</h4>
        </div>

        @if (session()->has('success') && !session()->get('success'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
        @endif

        @if (session()->has('success') && session()->get('success'))
            <div class="alert alert-info">
                Thao tác thành công
            </div>
        @endif

        {{-- Tìm kiếm và lọc --}}
        <form method="GET" action="{{ request()->url() }}" class="row g-2 align-items-center mb-3">
            <div class="col-lg-4 col-md-6">
                <div class="input-group shadow-sm">
                    <input type="text" name="search" class="form-control" placeholder="Nhập từ khóa..."
                        value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <div class="input-group shadow-sm">
                    <select name="role_id" class="form-select">
                        <option value="">-- Chọn vai trò --</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 text-end">
                <button type="submit" class="btn btn-success shadow-sm"><i class="bi bi-funnel"></i> Lọc</button>
                <a href="{{ request()->url() }}" class="btn btn-secondary shadow-sm"><i class="bi bi-arrow-clockwise"></i>
                    Reset</a>
            </div>
        </form>

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered text-center align-middle small">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Tên</th>
                                <th scope="col">Avatar</th>
                                <th scope="col">Email</th>
                                <th scope="col">SĐT</th>
                                <th scope="col">Địa chỉ</th>
                                <th scope="col">Vai trò</th>
                                <th scope="col">Thời gian tạo</th>
                                {{-- <th scope="col">Hành động</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $user)
                                <tr>
                                    <th scope="row">{{ $user->id }}</th>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        <img src="{{ $user->image_user }}" alt="User Image"
                                            class="rounded-circle object-fit-cover" width="60px" height="60px">
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->address }}</td>
                                    <td>{{ $user->role->name }}</td>
                                    <td>{{ $user->created_at->format('d-m-Y H:i') }}</td>
                                    {{-- <td class="">
                                        @if (Auth::user()->role->id === 1 && $user->role->id !== 1)
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="post"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa không?')">
                                                    <i class="mdi mdi-delete"></i> Xóa
                                                </button>
                                            </form>
                                        @elseif (Auth::user()->role->id === 2 && $user->role->id === 3)
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="post"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa không?')">
                                                    <i class="mdi mdi-delete"></i> Xóa
                                                </button>
                                            </form>
                                        @endif
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $data->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
