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
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Image</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Address</th>
                                <th scope="col">Role</th>
                                <th scope="col">Created at</th>
                                <th scope="col">Updated at</th>
                                <th scope="col">Action</th>
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
                                    <td>{{ $user->updated_at->format('d-m-Y H:i') }}</td>
                                    <td class="d-flex flex-column gap-2">
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="post" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa không?')">
                                                <i class="mdi mdi-delete"></i> XM
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.forceDestroy', $user) }}" method="post" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-dark"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn không?')">
                                                <i class="mdi mdi-delete"></i> XC
                                            </button>
                                        </form>
                                    </td>
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
