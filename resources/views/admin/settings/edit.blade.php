@extends('admin.layouts.master')

@section('title', 'Quản lý cài đặt')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Quản lý Cài Đặt</h2>

        <!-- Hiển thị thông báo thành công -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Hiển thị lỗi nếu có -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form cập nhật cài đặt -->
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%;">STT</th>
                            <th style="width: 30%;">Tên Đường Dẫn</th>
                            <th style="width: 65%;">Đường Liên Kết</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $value)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $value->key }}</td>
                                <td>
                                    <input type="text" name="settings[{{ $value->key }}]" class="form-control"
                                        value="{{ old("settings.$value->key", $value->value) }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Nút lưu -->
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-content-save"></i> Lưu Thay Đổi
                </button>
            </div>
        </form>
    </div>
@endsection
