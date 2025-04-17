@extends('admin.layouts.master')

@section('content')
    <div class="container-xxl mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold">Danh Sách đánh giá</h4>
            
        </div>

        {{-- Hiển thị thông báo thành công --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Tên người dùng</th>
                                <th>Sản phẩm</th>
                                <th>Xếp hạng</th>
                                <th>Đánh giá</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productReview as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td class="text-start">{{ $item->user->name }}</td>
                                    <td class="text-start">{{ $item->productVariant->product->product_name }}</td>
                                    <td class="text-start">{{ $item->rating }}</td>
                                    <td class="text-start">{{ $item->comment }}</td>                                                                       
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
