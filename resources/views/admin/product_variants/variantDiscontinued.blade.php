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
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Sku</th>
                                        <th scope="col">Product id</th>
                                        <th scope="col">price</th>
                                        <th scope="col">promotional_price</th>
                                        <th scope="col">quantity</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Act</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listVariant as $item)
                                        <tr>
                                            <th scope="row">{{$item->id}}</th>
                                            <td>{{$item->sku}}</td>
                                            <td>{{$item->product->product_name}}</td>
                                            <td>{{$item->price}}</td>
                                            <td>{{$item->promotional_price}}</td>
                                            <td>{{$item->quantity}}</td>
                                            <td class="{{ $item->status == 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $item->status == 0 ? 'Inactive' : 'Activate' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.product_variants.edit', $item->id) }}"><i
                                                        class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i></a>
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