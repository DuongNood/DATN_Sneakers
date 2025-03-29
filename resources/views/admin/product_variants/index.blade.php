@extends('admin.layouts.master')
@section('content')
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">{{ $title }}</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 ">
                <div class="card shadow">
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
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th scope="col">STT</th>
                                        <th scope="col">Product</th>
                                        <th scope="col">Size</th>
                                         <th scope="col">Quantity</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Act</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($productVariant as $item)
                                        <tr>
                                            <th scope="row">{{ $item->id }}</th>>
                                             <td>{{ $item->product->product_name }}</td>
                                             <td>{{ $item->productSize->name }}</td>>
                                             <td>{{ $item->quantity }}</td>
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
                    {{ $productVariant->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
