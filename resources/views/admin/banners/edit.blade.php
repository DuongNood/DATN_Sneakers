@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa banner')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">Chỉnh sửa Banner</h5>
            </div>
            <div class="card-body">
                {{-- Hiển thị thông báo thành công --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Hiển thị lỗi --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề</label>
                        <input type="text" name="title" id="title" class="form-control"
                            value="{{ $banner->title }}">
                    </div>

                    <!-- Current Image -->
                    <div class="mb-3">
                        <label class="form-label">Ảnh hiện tại</label>
                        <br>
                        <img src="{{ asset($banner->image) }}" class="img-thumbnail" style="max-width: 200px;">
                    </div>

                    <!-- Upload New Image with Preview -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Chọn ảnh mới</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*"
                            onchange="previewImage(event)">
                        <div class="mt-2">
                            <img id="imagePreview" src="" class="img-thumbnail d-none" style="max-width: 200px;">
                        </div>
                    </div>

                    <!-- Status Dropdown -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="1" {{ $banner->status == 1 ? 'selected' : '' }}>Hiển thị</option>
                            <option value="0" {{ $banner->status == 0 ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>

                    <!-- Nút hành động -->
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-check"></i> Cập nhật
                    </button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-light">
                        <i class="mdi mdi-arrow-left"></i> Quay lại
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Preview Image -->
    <script>
        function previewImage(event) {
            const imagePreview = document.getElementById("imagePreview");
            imagePreview.src = URL.createObjectURL(event.target.files[0]);
            imagePreview.classList.remove("d-none");
        }
    </script>
@endsection
