@extends('admin.layouts.master')

@section('title', 'Danh sách banner')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold">Danh Sách Banner</h4>
            <a href="{{ route('admin.banners.create') }}" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Thêm mới
            </a>
        </div>

        {{-- Hiển thị thông báo thành công --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Hiển thị thông báo lỗi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-hover table-bordered text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tiêu Đề</th>
                            <th>Hình Ảnh</th>
                            <th>Trạng Thái</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $banner)
                            <tr>
                                <td>{{ $banner->id }}</td>
                                <td class="text-start">{{ $banner->title }}</td>
                                <td>
                                    <img src="{{ $banner->image }}" alt="Banner" class="img-thumbnail" width="100">
                                </td>
                                <td>
                                    <span class="badge {{ $banner->status == 0 ? 'bg-danger' : 'bg-success' }}">
                                        {{ $banner->status == 0 ? 'Inactive' : 'Active' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="mdi mdi-pencil"></i> Sửa
                                    </a>

                                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                            <i class="mdi mdi-delete"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
