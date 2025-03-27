@extends('admin.layouts.master')
@section('title')
    Danh sách đơn hàng
@endsection
@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Danh Sách Đơn Hàng</h2>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Mã Đơn Hàng</th>
                            <th>Khách Hàng</th>
                            <th class="text-center">Giảm Giá</th>
                            <th class="text-center">Phí Ship</th>
                            <th class="text-end">Tổng Tiền</th>
                            <th class="text-center">Phương thức TT</th>
                            <th class="text-center">Trạng Thái TT</th>
                            <th class="text-center">Trạng Thái Đơn Hàng</th>
                            <th class="text-center">Ngày Đặt</th>
                            <th class="text-center">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $index => $order)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $order->order_code }}</td>
                                <td>
                                    <strong>{{ $order->recipient_name }}</strong><br>
                                    <small class="text-muted">{{ $order->recipient_phone }}</small><br>
                                    <small class="text-muted">{{ $order->recipient_address }}</small>
                                </td>
                                <td class="text-end">
                                    {{ number_format($order->promotion, 0, ',', '.') }} VND
                                </td>
                                <td class="text-end">
                                    {{ number_format($order->shipping_fee, 0, ',', '.') }} VND
                                </td>
                                <td class="text-end text-primary fw-bold">
                                    {{ number_format(round($order->total_price, -3), 0, ',', '.') }} VND
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $order->payment_method == 'COD' ? 'bg-warning' : 'bg-info' }}">
                                        {{ $order->payment_method }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge {{ $order->payment_status == 'da_thanh_toan' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $order->payment_status == 'da_thanh_toan' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusColors = [
                                            'cho_xac_nhan' => 'bg-secondary',
                                            'dang_chuan_bi' => 'bg-warning',
                                            'dang_van_chuyen' => 'bg-primary',
                                            'da_giao_hang' => 'bg-success',
                                            'huy_don_hang' => 'bg-danger',
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusColors[$order->status] ?? 'bg-secondary' }}">
                                        {{ str_replace(['cho_xac_nhan', 'dang_chuan_bi', 'dang_van_chuyen', 'da_giao_hang', 'huy_don_hang'], ['Chờ xác nhận', 'Đang chuẩn bị', 'Đang vận chuyển', 'Đã giao hàng', 'Hủy đơn hàng'], $order->status) }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.orders.edit', $order->id) }}"
                                        class="btn btn-primary btn-sm">Sửa</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
