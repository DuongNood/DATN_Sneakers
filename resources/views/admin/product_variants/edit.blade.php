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
                                                <form action="{{ route('admin.product_variants.update', $productVariant->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div id="variant-table">
                                                        <div class="variant-row row align-items-end mb-3">
                                                            <div class="col-md-2">
                                                                <label for="simpleinput" class="form-label">Product name</label>
                                                                <select class="form-select" aria-label="Default select example"
                                                                    name="product_id">
                                                                    @foreach ($product as $item)
                                                                        <option value="{{ $item->id }}" {{ $productVariant->product_id == $item->id ? 'selected' : '' }}>
                                                                            {{ $item->product_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <label for="simpleinput" class="form-label">Size</label>
                                                                <input type="number" class="form-control @error('product_size_id') is-invalid @enderror" name="product_size_id" placeholder="quantity"
                                                                    value="{{$productVariant->product_size_id}}">
                                                                @error('product_size_id')
                                                                    <p class="text-danger">{{ $message }}</p>
                                                                @enderror
                                                            </div>

                                                            <div class="col-md-2">
                                                                <label for="simpleinput" class="form-label">quantity</label>
                                                                <input type="number" class="form-control @error('sku') is-invalid @enderror"
                                                                    name="quantity" 
                                                                    placeholder="quantity" value="{{$productVariant->quantity}}">
                                                                    @error('quantity')
                                                                        <p class="text-danger">{{ $message }}</p>
                                                                    @enderror
                                                            </div>

                                                            <div class="col-md-2">                                                                
                                                                    <label for="example-password" class="form-label">Status</label>
                                                                    <select class="form-select" aria-label="Default select example" name="status">
                                                                        <option value="1">active</option>
                                                                        <option value="0">inactive</option>
                                                                    </select>                                                             
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <button type="submit" class="btn btn-primary mt-2">Lưu</button>
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
    
@endsection
