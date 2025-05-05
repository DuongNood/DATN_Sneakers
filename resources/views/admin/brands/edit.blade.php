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
                        <h5 class="mb-0">Chỉnh Sửa Thương Hiệu</h5>
                        <div class="card mt-3">

                            

                            {{-- <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <form action="{{route('admin.brands.update', $brand->id)}}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="mb-3">
                                                <label for="simpleinput" class="form-label">Brand name</label>
                                                <input type="text" id="simpleinput" class="form-control @error('brand_name')
                                                is-invalid @enderror" name="brand_name"
                                                    value="{{$brand->brand_name}}">
                                                @error('brand_name')
                                                    <p>{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="status" class="form-label ">Trạng Thái</label>
                                                <div class="col-sm-10 mb-3 d-flex gap-2">
                                                    <div class="form-check">
                                                        <input class="status" type="radio" name="status" id="gridRadios1"
                                                            value="1" {{ $brand->status == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="gridRadios1">
                                                            Hiển Thị
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="status" type="radio" name="status" id="gridRadios2"
                                                            value="0" {{ $brand->status == 0 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="gridRadios2">
                                                            Ẩn
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div> --}}

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

                                <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3">
                                        <label for="brand_name" class="form-label fw-semibold">Tên thương hiệu</label>
                                        <input type="text" id="brand_name" name="brand_name"
                                            class="form-control @error('brand_name') is-invalid @enderror"
                                            value="{{ old('brand_name', $brand->brand_name) }}"
                                            placeholder="Nhập tên danh mục">

                                        @error('brand_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Trạng Thái</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="active"
                                                    value="1" {{ old('status', $brand->status) == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="active">Active</label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="status" id="inactive"
                                                    value="0" {{ old('status', $brand->status) == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="inactive">Inactive</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Nút hành động -->
                                    <button type="submit" class="btn btn-success">
                                        <i class="mdi mdi-check"></i> Cập nhật
                                    </button>
                                    <a href="{{ route('admin.brands.index') }}" class="btn btn-light">
                                        <i class="mdi mdi-arrow-left"></i> Quay lại
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection