@extends('admin.layouts.master')

@section('content')
    <div class="container-xxl">
        <div class="py-3 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">Thêm Tin Tức</h4>
        </div>

        <!-- Form Thêm Tin Tức -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Tạo Tin Tức Mới</h5>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="{{ route('admin.news.store') }}" enctype="multipart/form-data" method="POST">
                                    @csrf

                                    <!-- Tiêu đề -->
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Tiêu đề</label>
                                        <input type="text" id="title" class="form-control @error('title') is-invalid @enderror"
                                            value="{{ old('title') }}" name="title">
                                        @error('title')
                                            <p class="text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Ảnh -->
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Hình ảnh</label>
                                        <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror"
                                            onchange="showImage(event)">
                                        <img id="img_news" class="border rounded mt-2 d-none" style="width: 150px;">
                                        @error('image')
                                            <p class="text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Nội dung -->
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Nội dung</label>
                                        <div id="quill-editor" style="height: 400px;"></div>
                                        <textarea name="content" id="editor_content" class="d-none @error('content') is-invalid @enderror"></textarea>
                                        @error('content')
                                            <p class="text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-success">
                                        <i class="mdi mdi-check"></i> Lưu tin tức
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function showImage(event) {
            const img_news = document.getElementById('img_news');
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img_news.src = e.target.result;
                    img_news.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        }
    </script>

    <script src="{{ asset('admins/libs/quill/quill.core.js') }}"></script>
    <script src="{{ asset('admins/libs/quill/quill.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var quill = new Quill("#quill-editor", {
                theme: "snow",
            });

            // Hiển thị nội dung cũ nếu có
            var oldContent = `{!! old('content') !!}`;
            quill.root.innerHTML = oldContent;

            // Cập nhật lại textarea ẩn khi nội dung thay đổi
            quill.on('text-change', function() {
                document.getElementById('editor_content').value = quill.root.innerHTML;
            });
        });
    </script>
@endsection
