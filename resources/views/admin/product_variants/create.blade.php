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
                                            @foreach(old('product_variants', [0 => []]) as $key => $variant)
                                                <div class="variant-row row align-items-end mb-3">
                                                    <div class="col-md-2">
                                                        <label for="simpleinput" class="form-label">Variant name</label>
                                                        <input type="text" class="form-control" name="product_variants[{{ $key }}][sku]" placeholder="Variant name"
                                                            value="{{ old("product_variants.$key.sku") }}">
                                                        @error("product_variants.$key.sku")
                                                            <p class="text-danger position-absolute">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="simpleinput" class="form-label">Product name</label>
                                                        <select class="form-select" name="product_variants[{{ $key }}][product_id]">
                                                            @foreach ($product as $item)
                                                                <option value="{{ $item->id }}" {{ old("product_variants.$key.product_id") == $item->id ? 'selected' : '' }}>
                                                                    {{ $item->product_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="simpleinput" class="form-label">Price</label>
                                                        <input type="number" class="form-control" name="product_variants[{{ $key }}][price]"
                                                            value="{{ old("product_variants.$key.price") }}" placeholder="Price" step="0.01">
                                                        @error("product_variants.$key.price")
                                                            <p class="text-danger position-absolute">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="simpleinput" class="form-label">Promotional price</label>
                                                        <input type="number" class="form-control" name="product_variants[{{ $key }}][promotional_price]"
                                                            value="{{ old("product_variants.$key.promotional_price") }}" placeholder="Promotional price" step="0.01">
                                                        @error("product_variants.$key.promotional_price")
                                                            <p class="text-danger position-absolute">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label for="simpleinput" class="form-label">Quantity</label>
                                                        <input type="number" class="form-control" name="product_variants[{{ $key }}][quantity]"
                                                            value="{{ old("product_variants.$key.quantity") }}" placeholder="Quantity">
                                                        @error("product_variants.$key.quantity")
                                                            <p class="text-danger position-absolute">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="remove-row btn btn-danger">Xóa</button>
                                                    </div>
                                                </div>
                                            @endforeach

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
            let index = {{ count(old('product_variants', [0 => []])) - 1 }}; // Lấy số lượng biến thể đã có từ old()

            // Thêm biến thể mới
            $("#add-variant").click(function () {
                index++;
                let newRow = `
            <div class="variant-row row align-items-end mb-3">
                <div class="col-md-2">
                    <input type="text" class="form-control" name="product_variants[${index}][sku]" 
                           placeholder="SKU">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="product_variants[${index}][product_id]">
                        @foreach ($product as $item)
                            <option value="{{ $item->id }}">{{ $item->product_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="product_variants[${index}][price]" 
                           placeholder="Price" step="0.01">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="product_variants[${index}][promotional_price]" 
                           placeholder="Promotional price" step="0.01">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="product_variants[${index}][quantity]" 
                           placeholder="Quantity">
                </div>
                <div class="col-md-2">
                    <button type="button" class="remove-row btn btn-danger">Xóa</button>
                </div>
            </div>
        `;
                $("#variant-table").append(newRow);
            });

            // Xóa biến thể
            $(document).on("click", ".remove-row", function () {
                $(this).closest(".variant-row").remove();
            });
        });

    </script>
@endsection