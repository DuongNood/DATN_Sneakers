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
                        <h5 class="mb-0">Tạo Thương Hiệu Mới</h5>
                    </div>

                    <div class="card-body">
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

                        {{-- Hiển thị thông báo thành công --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.brands.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="brand_name" class="form-label fw-semibold">Tên Danh Mục</label>
                                <input type="text" id="brand_name" name="brand_name"
                                    class="form-control @error('brand_name') is-invalid @enderror"
                                    value="{{ old('brand_name') }}" placeholder="Nhập tên thương hiệu">

                                @error('brand_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> Lưu thương hiệu
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
