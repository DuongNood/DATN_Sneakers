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
            @if (session('error'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-babel="Close">
                    </button>
                </div>
            @endif
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title mb-0">Product Variants</h5>
                    </div><!-- end card header -->

                    <div class="card-body">
                    <div class="row"> <!-- Basic Example -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                        <form id="variant-form" action="{{ route('product_variants.store') }}" method="POST">
                                            @csrf
                                            <div id="variant-table">                                              
                                            <div class="variant-row row align-items-end mb-3">
                                                <div class="col-md-2">
                                                    <label for="simpleinput" class="form-label">variant name</label>
                                                    <input type="text" class="form-control" name="product_variants[0][sku]" required placeholder="variant name">
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="simpleinput" class="form-label">Product name</label>
                                                    <select class="form-select" aria-label="Default select example" name="product_variants[0][product_id]" >
                                                        @foreach ($product as $item)
                                                            <option value="{{ $item->id }}">{{ $item->product_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="simpleinput" class="form-label">Price</label>
                                                    <input type="number" class="form-control" name="product_variants[0][price]" step="0.01" required
                                                        placeholder="Price">
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="simpleinput" class="form-label">quantity</label>
                                                    <input type="number" class="form-control" name="product_variants[0][quantity]" required placeholder="quantity">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="remove-row btn btn-danger">Xóa</button>
                                                </div>
                                            </div>

                                            </div>

                                            <button type="button" id="add-variant" class="btn btn-success mt-2">➕ Thêm Biến Thể</button>
                                            <button type="submit" class="btn btn-primary mt-2">💾 Lưu Biến Thể</button>
                                        </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
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
     $(document).ready(function () {
            let index = 0; // Biến đếm số lượng biến thể

            $("#add-variant").click(function () {
                index++; // Tăng chỉ mục cho mỗi biến thể mới
                let newRow = `
            <div class="variant-row row align-items-end mb-3">
                <div class="col-md-2">
                    <input type="text" class="form-control" name="product_variants[${index}][sku]" required placeholder="SKU">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="product_variants[${index}][product_id]" required>
                        @foreach ($product as $item)
                            <option value="{{ $item->id }}">{{ $item->product_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="product_variants[${index}][price]" step="0.01" required placeholder="Giá">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="product_variants[${index}][quantity]" required placeholder="Số lượng">
                </div>
                <div class="col-md-2">
                    <button type="button" class="remove-row btn btn-danger">Xóa</button>
                </div>
            </div>`;
                $("#variant-table").append(newRow);
            });

            $(document).on("click", ".remove-row", function () {
                $(this).closest(".variant-row").remove();
            });
        });

    </script>
@endsection