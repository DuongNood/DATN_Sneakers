@extends('admin.layouts.master')
@section('content')
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">{{ $title }}</h4>
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
                                                <form id="variant-form" action="{{ route('admin.product_variants.store') }}"
                                                    method="POST">
                                                    @csrf
                                                    <div id="variant-table">
                                                        @foreach (old('product_variants', [0 => []]) as $key => $variant)
                                                            <div class="variant-row row align-items-end mb-3">
                                                                <input type="hidden" value="{{ $product->id }}"
                                                                    name="product_variants[{{ $key }}][product_id]">
                                                                <div class="col-md-2">
                                                                    <label for="simpleinput" class="form-label">size</label>
                                                                    <select class="form-select"
                                                                        name="product_variants[{{ $key }}][product_size_id]">
                                                                        @foreach ($size as $item)
                                                                            <option value="{{ $item->id }}"
                                                                                {{ old("product_variants.$key.product_size_id") == $item->id ? 'selected' : '' }}>
                                                                                {{ $item->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <label for="simpleinput"
                                                                        class="form-label">Quantity</label>
                                                                    <input type="number" class="form-control"
                                                                        name="product_variants[{{ $key }}][quantity]"
                                                                        value="{{ old("product_variants.$key.quantity") }}"
                                                                        placeholder="Quantity">
                                                                    @error("product_variants.$key.quantity")
                                                                        <p class="text-danger position-absolute">
                                                                            {{ $message }}</p>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button"
                                                                        class="remove-row btn btn-danger">Xóa</button>
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                    </div>

                                                    <button type="button" id="add-variant" class="btn btn-success mt-2">➕
                                                        Thêm Biến Thể</button>
                                                    <button type="submit" class="btn btn-primary mt-2">💾 Lưu Biến
                                                        Thể</button>
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
        $(document).ready(function() {
            let index =
            {{ count(old('product_variants', [0 => []])) - 1 }}; // Lấy số lượng biến thể đã có từ old()

            // Thêm biến thể mới
            $("#add-variant").click(function() {
                index++;
                let newRow = `
            <div class="variant-row row align-items-end mb-3">
                <input type="hidden" value="{{ $product->id }}"  name="product_variants[${index}][product_id]">
                 <div class="col-md-2">
                    <select class="form-select" name="product_variants[${index}][product_size_id]">
                        @foreach ($size as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
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
            $(document).on("click", ".remove-row", function() {
                $(this).closest(".variant-row").remove();
            });
        });
    </script>
@endsection
