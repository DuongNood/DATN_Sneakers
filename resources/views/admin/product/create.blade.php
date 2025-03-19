@extends('admin.layouts.master')
@section('content')
    <!-- Start Content-->
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">{{$title}}</h4>
            </div>
        </div>

        <!-- General Form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('products.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6">                              
                                        <div class="mb-3">
                                            <label for="simpleinput" class="form-label">Product code</label>
                                            <input type="text" id="simpleinput" class="form-control" name="product_code">
                                        </div>
                                        <div class="mb-3">
                                            <label for="example-email" class="form-label">Product name</label>
                                            <input type="text" id="example-email"  class="form-control" name="product_name">
                                        </div>

                                        <div class="mb-3">
                                            <label for="example-password" class="form-label">Category</label>
                                            <select class="form-select" aria-label="Default select example" name="category_id">                                               
                                                @foreach ($listCategories as $item)
                                                    <option value="{{$item->id}}">{{$item->category_name}}</option> 
                                                @endforeach                                                                                    
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="example-password" class="form-label">Is show home</label>
                                            <select class="form-select" aria-label="Default select example" name="is_show_home">
                                                <option value="1">Display</option>
                                                <option value="0">Hide</option>
                                            </select>
                                        </div>                                                               
                                </div>

                                <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="mo_ta_ngan" class="form-label">Mô Tả</label> <br>
                                            <div id="quill-editor" style="height: 400px;">

                                            </div>
                                            <textarea name="description" id="editor_content" class="d-none"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="image" class="form-label">hinh anh</label>
                                            <input type="file" id="image" name="image" class="form-control" onchange="showIamge(event)">
                                            <img id="img_product" alt="hinh anh" style="width:150px; display: none">
                                        </div>   
                                        <div class="mb-3">
                                            <label for="hinh_anh" class="form-label">Album hinh anh</label>
                                            <i id="add-row" class="mdi mdi-plus text-muted fs-18 rounded-2 border ms-3 p-1" style="cursor: pointer"></i>
                                            <table class="table align-middle table-nowrap mb-0">
                                                <tbody id="image-table-body">
                                                    <tr>
                                                        <td class="d-flex align-items-center">
                                                            <img id="preview_0"
                                                                src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS0Wr3oWsq6KobkPqznhl09Wum9ujEihaUT4Q&s"
                                                                alt="hinh anh" style="width:50px" class="me-3">
                                                            <input type="file" id="hinh_anh" name="list_image[id_0]" class="form-control"
                                                                onchange="previewImage(this,0)">
                                                        </td>
                                                        <td class="">
                                                            <i class="mdi mdi-delete text-muted fs-18 rounded-2 border p-1" style="cursor: pointer"
                                                                onclick="removeRow(this)"></i>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>                                    
                              </div>
                            </div>
                            <div class="d-flex"><button type="submit" class="btn btn-primary">Thêm moi</button>
                        </form>        
                    </div>

                </div>
            </div>
        </div>       
    </div> <!-- container-fluid -->
@endsection
@section('js')
    <script src="{{asset('admins/libs/quill/quill.core.js')}}"></script>
    <script src="{{asset('admins/libs/quill/quill.min.js')}}"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                    var quill = new Quill("#quill-editor", {
                        theme: "snow",
                    })

                    // Hiển thị nội dung cũ 
                    var old_content = `{!! old('description') !!}`;
                    quill.root.innerHTML = old_content

                    // Cập nhật lại textarea ẩn khi nội dung của  quill-editor thay đổi
                    quill.on('text-change', function () {
                        var html = quill.root.innerHTML;
                        document.getElementById('editor_content').value = html
                    })
                })

        </script>

        <script>
            function showIamge(event) {
                const img_product = document.getElementById('img_product');
                const file = event.target.files[0];
                const reader = new FileReader();
                reader.onload = function () {
                    img_product.src = reader.result;
                    img_product.style.display = 'block';
                }
                if (file) {
                    reader.readAsDataURL(file)
                }
            }
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var rowCount = 1;
                document.getElementById('add-row').addEventListener('click', function () {
                    var tableBody = document.getElementById('image-table-body')
                    var newRow = document.createElement('tr');
                    newRow.innerHTML = ` 
                            <td class="d-flex align-items-center">
                                <img id="preview_${rowCount}" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS0Wr3oWsq6KobkPqznhl09Wum9ujEihaUT4Q&s" alt="hinh anh"
                                    style="width:50px" class="me-3">
                                <input type="file" id="hinh_anh" name="list_image[id_${rowCount}]"
                                    class="form-control" onchange="previewImage(this,${rowCount})">                                                            
                            </td>
                            <td class="">
                                <i class="mdi mdi-delete text-muted fs-18 rounded-2 border p-1" 
                                style="cursor: pointer" onclick="removeRow(this)"></i>
                            </td>
                            `;
                    tableBody.appendChild(newRow);
                    rowCount++;
                });
            })

            function previewImage(input, rowIndex) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById(`preview_${rowIndex}`).setAttribute('src', e.target.result)
                    }
                    reader.readAsDataURL(input.files[0])
                }
            }
            function removeRow(item) {
                var row = item.closest('tr');
                row.remove();
            }
        </script>
@endsection