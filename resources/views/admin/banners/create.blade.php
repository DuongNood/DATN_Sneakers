@extends('admin.layouts.master')
@section('title')
    Thêm mới banner
@endsection
@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Thêm mới banner</h5>
            </div>

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

            <div class="card-body">
                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề</label>
                        <input type="text" name="title" id="title" class="form-control"
                            placeholder="Nhập tiêu đề banner">
                    </div>

                    <!-- Image Upload with Preview -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Hình ảnh</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*"
                            onchange="previewImage(event)">
                        <div class="mt-2">
                            <img id="imagePreview" src="" class="img-thumbnail d-none" style="max-width: 200px;">
                        </div>
                    </div>

                    <!-- Status (Dropdown) -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Thêm banner</button>
                    </div>
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
