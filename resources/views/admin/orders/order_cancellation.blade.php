@extends('admin.layouts.master')

@section('title', 'Danh Sách Đơn Hàng Đã Hủy')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold">Danh Sách Đơn Hàng Đã Hủy</h4>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover table-sm small">
                        <thead class="table-dark text-center">
                            <tr>
                                <th class="text-center small">Mã Đơn Hàng</th>
                                <th class="text-center small">Khách Hàng</th>
                                <th class="text-center small">Ngày Hủy</th>
                                <th class="text-center small">Lý Do Hủy</th>
                                <th class="text-center small">Tổng Tiền</th>
                                <th class="text-center small">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $order)
                                <tr class="align-middle">
                                    <td>{{ $order->order_code }}</td>
                                    <td>{{ $order->recipient_name }}</td>
                                    <td class="text-center small">{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $order->cancellation_reason }}</td>
                                    <td class="text-end">{{ number_format($order->total_price, 0, ',', '.') }} VND</td>
                                    <td class="text-center small">
                                        <a href="{{ route('admin.orders.edit', $order->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-eye"></i> Chi tiết đơn hàng
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Không có đơn hàng nào đã hủy.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $data->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection