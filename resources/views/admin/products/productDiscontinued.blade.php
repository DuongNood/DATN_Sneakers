@extends('admin.layouts.master')
@section('content')
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">{{$title}}</h4>
            </div>
        </div>

        <!-- start row -->
        <div class="row">

            <div class="col-xl-12 ">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-babel="Close">
                                    </button>
                                </div>
                            @endif
                            <table class="table table-striped table-bordered table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Mã SP</th>
                                        <th scope="col">Tên sản phẩm</th>
                                        <th scope="col">Hình ảnh</th>
                                        <th scope="col">Giá gốc (VND)</th>
                                        <th scope="col">Giá khuyến mãi (VND)</th>
                                        <th scope="col">Danh mục</th>
                                        <th scope="col">Trạng thái</th>
                                        <th scope="col">Hiển thị</th>
                                        <th scope="col">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listProduct as $item)
                                        <tr>
                                            <th scope="row">{{$item->id}}</th>
                                            <td>{{$item->product_code}}</td>
                                            <td>{{$item->product_name}}</td>
                                            <td>
                                                <img src="{{ $item->image ?? asset('images/default-product.png') }}" alt="Product Image" width="100px">
                                            </td>
                                            <td>{{$item->original_price}}</td>
                                            <td>{{$item->discounted_price}}</td>                                        
                                            <td>{{$item->category->category_name}}</td>
                                            <td class="{{ $item->status == 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $item->status == 0 ? 'Inactive' : 'Activate' }}
                                            </td>
                                            <td class="{{ $item->is_show_home == 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $item->is_show_home == 0 ? 'Hide' : 'Display' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.products.edit', $item->id) }}"><i
                                                        class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i></a>     
                                                <a href="{{ route('admin.products.show', $item->id) }}" title="Xem chi tiết">
                                                    <i class="mdi mdi-plus text-success fs-18 border p-1"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end row -->



    </div>

@endsection