@extends('admin.layouts.master')
@section('title')
    Danh sách đơn hàng
@endsection
@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Chỉnh Sửa Đơn Hàng</h2>

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" enctype="multipart/form-data" class="card p-4">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Mã Đơn Hàng</label>
                <input type="text" class="form-control" value="{{ $order->order_code }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Khách Hàng</label>
                <input type="text" class="form-control" value="{{ $order->recipient_name }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Phương Thức Thanh Toán</label>
                <input type="text" class="form-control" value="{{ $order->payment_method }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Trạng Thái Thanh Toán</label>
                <select name="payment_status" class="form-select" {{ $order->payment_status == 'Đã thanh toán' ? 'disabled' : '' }}>
                    <option value="Chưa thanh toán" {{ $order->payment_status == 'Chưa thanh toán' ? 'selected' : '' }}>Chưa thanh toán</option>
                    <option value="Đã thanh toán" {{ $order->payment_status == 'Đã thanh toán' ? 'selected' : '' }}>Đã thanh toán</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Trạng Thái Đơn Hàng</label>
                <select name="status" class="form-select">
                    @foreach ($statusOptions as $status)
                        <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <h4 class="mb-3">Chi Tiết Sản Phẩm</h4>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Mã SP</th>
                        <th>Tên SP</th>
                        <th>Ảnh</th>
                        <th>Size</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Giảm giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderDetails as $detail)
                        <tr>
                            <td>{{ $detail->productVariant->product->product_code }}</td>
                            <td>{{ $detail->productVariant->product->product_name }}</td>
                            <td><img src="{{ asset('images/' . $detail->productVariant->product->image) }}" width="50"></td>
                            <td>{{ $detail->productVariant->size }}</td>
                            <td>{{ $detail->quantity }}</td>
                            <td>{{ number_format($detail->price, 0, ',', '.') }} VND</td>
                            <td>{{ number_format($detail->discount, 0, ',', '.') }} VND</td>
                            <td>{{ number_format($detail->total_price, 0, ',', '.') }} VND</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Quay lại</a>
                <button type="submit" class="btn btn-success">Cập nhật</button>
            </div>
        </form>
    </div>
@endsection
