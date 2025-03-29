@extends('admin.layouts.master')

@section('content')
    <div class="container-xxl">
        <div class="py-3 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold">Chỉnh sửa Tin Tức</h4>
        </div>

        <!-- Form Chỉnh Sửa -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.news.update', $news) }}" enctype="multipart/form-data" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Tiêu đề -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Tiêu đề</label>
                                <input type="text" id="title"
                                    class="form-control @error('title') is-invalid @enderror" name="title"
                                    value="{{ old('title', $news->title) }}">
                                @error('title')
                                    <p class="text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Ảnh -->
                            <div class="mb-3">
                                <label for="image" class="form-label">Hình ảnh</label>
                                <input type="file" id="image" name="image" class="form-control"
                                    onchange="showImage(event)">
                                <img id="img_news" src="{{ $news->image ? Storage::url($news->image) : '' }}"
                                    class="border rounded mt-2 {{ $news->image ? '' : 'd-none' }}" style="width: 150px;">
                                @error('image')
                                    <p class="text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nội dung -->
                            <div class="mb-3">
                                <label for="content" class="form-label">Nội dung</label>
                                <div id="quill-editor" style="height: 400px;"></div>
                                <textarea name="content" id="editor_content" class="d-none">@php echo old('content', $news->content); @endphp</textarea>
                                @error('content')
                                    <p class="text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nút hành động -->
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-check"></i> Cập nhật
                            </button>
                            <a href="{{ route('admin.news.index') }}" class="btn btn-light">
                                <i class="mdi mdi-arrow-left"></i> Quay lại
                            </a>
                        </form>
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
            var oldContent = `{!! old('content', $news->content) !!}`;
            quill.root.innerHTML = oldContent;

            // Cập nhật lại textarea ẩn khi nội dung thay đổi
            quill.on('text-change', function() {
                document.getElementById('editor_content').value = quill.root.innerHTML;
            });
        });
    </script>
@endsection
