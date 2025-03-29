@extends('admin.layouts.master')
@section('content')
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                {{-- <h4 class="fs-18 fw-semibold m-0">{{ $title }}</h4> --}}
            </div>
        </div>

        <!-- start row -->
        <div class="row"> <!-- Basic Example -->
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title mb-0">Create news</h5>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="{{ route('admin.news.store') }}" enctype="multipart/form-data" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Title</label>
                                        <input type="text" id="simpleinput" class="form-control mb-1 @error ('title') is-invalid @enderror "
                                            value="{{ old('title') }}"name="title">
                                        @error('title')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="example-email" class="form-label">Image</label>
                                        <input type="file" id="example-email" name="image" class="form-control  @error ('title') is-invalid @enderror"
                                            onchange="showIamge(event)" >
                                        <img id="img_news" alt="hinh anh" style="width:150px; display: none">
                                        @error('image')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="mo_ta_ngan" class="form-label">Content</label> <br>
                                        <div id="quill-editor" style="height: 400px;">

                                        </div>
                                        <textarea name="content" id="editor_content" class="d-none @error ('title') is-invalid @enderror"></textarea>
                                        @error('content')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div><!-- end row -->
    </div>
@endsection
@section('js')
    <script>
        function showIamge(event) {
            const img_news = document.getElementById('img_news');
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = function() {
                img_news.src = reader.result;
                img_news.style.display = 'block';
            }
            if (file) {
                reader.readAsDataURL(file)
            }
        }
    </script>

    <script src="{{ asset('admins/libs/quill/quill.core.js') }}"></script>
    <script src="{{ asset('admins/libs/quill/quill.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var quill = new Quill("#quill-editor", {
                theme: "snow",
            })

            // Hiển thị nội dung cũ 
            var old_content = `{!! old('content') !!}`;
            quill.root.innerHTML = old_content

            // Cập nhật lại textarea ẩn khi nội dung của  quill-editor thay đổi
            quill.on('text-change', function() {
                var html = quill.root.innerHTML;
                document.getElementById('editor_content').value = html
            })
        })
    </script>
@endsection
