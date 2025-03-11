@extends('admin.layouts.master')
@section('title')
    Danh sách người dùng
@endsection
@section('content')
    <div class="container-xxl">

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

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Danh sách người dùng</h4>
            </div>
            <a href="{{ route('users.create') }}" class="btn btn-success col-1">Create</a>
        </div>

        <div class="row">

            <div class="col-xl-12 ">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Name</th>
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
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{{ $user->address }}</td>
                                            <td>{{ $user->role_id }}</td>
                                            <td>{{ $user->created_at }}</td>
                                            <td>{{ $user->updated_at }}</td>
                                            <td>
                                                <a class="btn btn-info"
                                                    href="{{ route('users.show', $user) }}">SHOW</a>
                                                <a class="btn btn-warning"
                                                    href="{{ route('users.edit', $user) }}">EDIT</a>
                                                <form action="{{ route('users.destroy', $user) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa không?')">XM</button>
                                                </form>

                                                <form action="{{ route('users.forceDestroy', $user) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-dark"
                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa không?')">XC</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $data->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
