@extends('admin.layouts.master')

@section('content')
    <div class="container-xxl mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold">{{ $title }}</h4>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Chỉnh Sửa Danh Mục</h5>
                    </div>

                    <div class="card-body">
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

                        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="category_name" class="form-label fw-semibold">Tên Danh Mục</label>
                                <input type="text" id="category_name" name="category_name"
                                    class="form-control @error('category_name') is-invalid @enderror"
                                    value="{{ old('category_name', $category->category_name) }}"
                                    placeholder="Nhập tên danh mục">

                                @error('category_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Trạng Thái</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="active"
                                            value="1" {{ old('status', $category->status) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="active">Active</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="inactive"
                                            value="0" {{ old('status', $category->status) == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label" for="inactive">Inactive</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Nút hành động -->
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-check"></i> Cập nhật
                            </button>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-light">
                                <i class="mdi mdi-arrow-left"></i> Quay lại
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
