@extends('admin.layouts.master')
@section('content')

    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">{{$title}}</h4>
            </div>
        </div>

        <!-- start row -->
        <div class="row"> <!-- Basic Example -->
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title mb-0">Create category</h5>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form action="{{route('categories.update', $category->id)}}" enctype="multipart/form-data" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Category name</label>
                                        <input type="text" id="simpleinput" class="form-control" name="category_name" value="{{$category->category_name}}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="example-email" class="form-label">Image</label>
                                        <input type="file" id="example-email" name="image" class="form-control mb-1" onchange="showIamge(event)">
                                        <img id="img_category" src="{{ Storage::url($category->image) }}" alt="hinh anh" style="width:150px">
                                    </div>
                                    <div class="mb-3">
                                        <label for="status" class="form-label ">Trạng Thái</label>
                                        <div class="col-sm-10 mb-3 d-flex gap-2">
                                            <div class="form-check">
                                                <input class="status" type="radio" name="status" id="gridRadios1" value="1" {{ $category->status == 1 ? 'checked' : '' }}>
                                                <label class="form-check-label" for="gridRadios1" >
                                                    Hiển Thị
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="status" type="radio" name="status" id="gridRadios2" value="0" {{ $category->status == 0 ? 'checked' : '' }}>
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
                    </div>

                </div>
            </div>
        </div><!-- end row -->
    </div>
@endsection
@section('js')
    <script>
        function showIamge(event) {
            const img_category = document.getElementById('img_category');
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = function () {
                img_category.src = reader.result;
                img_category.style.display = 'block';
            }
            if (file) {
                reader.readAsDataURL(file)
            }
        }
    </script>
@endsection

