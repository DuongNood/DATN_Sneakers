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
        
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" enctype="multipart/form-data"
            class="card p-4">
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
                <select name="payment_status" class="form-select"
                    {{ $order->payment_status == 'da_thanh_toan' ? 'disabled' : '' }}>
                    <option value="chua_thanh_toan" {{ $order->payment_status == 'chua_thanh_toan' ? 'selected' : '' }}>Chưa
                        thanh toán</option>
                    <option value="da_thanh_toan" {{ $order->payment_status == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh
                        toán</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Trạng Thái Đơn Hàng</label>
                <select name="status" class="form-select" {{ $order->status === 'huy_don_hang' ? 'disabled' : '' }}>
                    <option value="{{ $order->status }}" selected>
                        {{ str_replace(['cho_xac_nhan', 'dang_chuan_bi', 'dang_van_chuyen', 'da_giao_hang', 'huy_don_hang'], ['Chờ xác nhận', 'Đang chuẩn bị', 'Đang vận chuyển', 'Đã giao hàng', 'Hủy đơn hàng'], $order->status) }}
                    </option>

                    @php
                        $validNextStatuses = [
                            'cho_xac_nhan' => ['dang_chuan_bi', 'dang_van_chuyen', 'da_giao_hang', 'huy_don_hang'],
                            'dang_chuan_bi' => ['dang_van_chuyen', 'da_giao_hang'],
                            'dang_van_chuyen' => ['da_giao_hang'],
                            'da_giao_hang' => [],
                            'huy_don_hang' => [],
                        ];
                    @endphp

                    @foreach ($validNextStatuses[$order->status] ?? [] as $nextStatus)
                        <option value="{{ $nextStatus }}">
                            {{ str_replace(['cho_xac_nhan', 'dang_chuan_bi', 'dang_van_chuyen', 'da_giao_hang', 'huy_don_hang'], ['Chờ xác nhận', 'Đang chuẩn bị', 'Đang vận chuyển', 'Đã giao hàng', 'Hủy đơn hàng'], $nextStatus) }}
                        </option>
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
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderDetails as $detail)
                        <tr>
                            <td>{{ $detail->productVariant->product->product_code }}</td>
                            <td>{{ $detail->productVariant->product->product_name }}</td>
                            <td><img src="{{ $detail->productVariant->product->image }}" width="50"></td>
                            <td>{{ $detail->productVariant->productSize->name }}</td>
                            <td>{{ $detail->quantity }}</td>
                            <td>{{ number_format($detail->price, 0, ',', '.') }} VND</td>
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
