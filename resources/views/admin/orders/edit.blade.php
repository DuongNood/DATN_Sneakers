@extends('admin.layouts.master')
@section('title')
    Chỉnh Sửa Đơn Hàng {{ $order->order_code }}
@endsection
@section('content')
    <div class="container mt-4">
        <h4 class="mb-4">Chỉnh Sửa Đơn Hàng: <strong>{{ $order->order_code }}</strong></h4>

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

            {{-- Thông tin khách hàng --}}
            <div class="card mb-3">
                <div class="card-header fw-bold">Thông Tin Đơn Hàng</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Mã Đơn Hàng</label>
                            <input type="text" class="form-control" value="{{ $order->order_code }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Khách Hàng</label>
                            <input type="text" class="form-control" value="{{ $order->recipient_name }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" value="{{ $order->recipient_phone }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" value="{{ $order->recipient_address }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phương Thức Thanh Toán</label>
                            <input type="text" class="form-control" value="{{ $order->payment_method }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng Thái Thanh Toán</label>
                            <select name="payment_status" class="form-select"
                                {{ $order->payment_status == 'da_thanh_toan' ? 'disabled' : '' }}>
                                <option value="chua_thanh_toan"
                                    {{ $order->payment_status == 'chua_thanh_toan' ? 'selected' : '' }}>Chưa thanh toán
                                </option>
                                <option value="da_thanh_toan"
                                    {{ $order->payment_status == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Trạng thái đơn hàng --}}
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

            {{-- Chi tiết sản phẩm --}}
            <h4 class="mb-3">Chi Tiết Sản Phẩm</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">Mã SP</th>
                            <th>Tên SP</th>
                            <th class="text-center">Ảnh</th>
                            <th class="text-center">Size</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Giá</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $detail)
                            <tr class="align-middle">
                                <td class="text-center">{{ $detail->productVariant->product->product_code }}</td>
                                <td>{{ $detail->productVariant->product->product_name }}</td>
                                <td class="text-center"><img src="{{ $detail->productVariant->product->image }}"
                                        width="100"></td>
                                <td class="text-center">{{ $detail->productVariant->productSize->name }}</td>
                                <td class="text-center">{{ $detail->quantity }}</td>
                                <td class="text-end">
                                    {{ number_format($detail->productVariant->product->discounted_price, 0, ',', '.') }}
                                    VND</td>
                                <td class="text-end">{{ number_format($detail->price, 0, ',', '.') }} VND</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Thông tin đơn hàng --}}
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="fw-bold">Thông Tin Thanh Toán</h5>

                    {{-- Tổng tiền hàng (chưa trừ giảm giá, phí ship) --}}
                    <div class="d-flex justify-content-between">
                        <span>Tổng tiền hàng:</span>
                        <strong>
                            {{ number_format($order->orderDetails->sum(fn($detail) => $detail->price), 0, ',', '.') }}
                            VND
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Giảm giá:</span>
                        <strong>-{{ number_format($order->promotion, 0, ',', '.') }} VND</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Phí vận chuyển:</span>
                        <strong>{{ number_format($order->shipping_fee, 0, ',', '.') }} VND</strong>
                    </div>

                    <div class="d-flex justify-content-between mt-2 border-top pt-2">
                        <span class="fw-bold">Tổng Thanh Toán:</span>
                        <strong class="text-danger">{{ number_format($order->total_price, 0, ',', '.') }} VND</strong>
                    </div>

                </div>
            </div>

            {{-- Nút hành động --}}
            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-light">
                    <i class="mdi mdi-arrow-left"></i> Quay lại
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="mdi mdi-check"></i> Cập nhật
                </button>
            </div>
        </form>
    </div>
@endsection
