@extends('admin.layouts.master')
@section('title')
    Danh sách đơn hàng
@endsection
@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold">Danh Sách Đơn Hàng</h4>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tìm kiếm và lọc --}}
        <form method="GET" action="{{ request()->url() }}" class="row g-2 align-items-center mb-3">
            {{-- Ô tìm kiếm --}}
            <div class="col-lg-4 col-md-6">
                <input type="text" name="search" class="form-control shadow-sm" placeholder="Nhập từ khóa..."
                    value="{{ request('search') }}">
            </div>

            {{-- Danh sách bộ lọc --}}
            @php
                $filters = [
                    'payment_method' => ['' => 'Phương thức TT', 'COD' => 'COD', 'Online' => 'Online'],
                    'payment_status' => [
                        '' => 'Trạng thái TT',
                        'chua_thanh_toan' => 'Chưa thanh toán',
                        'da_thanh_toan' => 'Đã thanh toán',
                    ],
                    'status' => [
                        '' => 'Trạng thái đơn hàng',
                        'cho_xac_nhan' => 'Chờ xác nhận',
                        'dang_chuan_bi' => 'Đang chuẩn bị',
                        'dang_van_chuyen' => 'Đang vận chuyển',
                        'da_giao_hang' => 'Đã giao hàng',
                        'huy_don_hang' => 'Hủy đơn hàng',
                    ],
                ];
            @endphp

            {{-- Duyệt danh sách bộ lọc --}}
            @foreach ($filters as $name => $options)
                <div class="col-lg-2 col-md-6">
                    <select name="{{ $name }}" class="form-select shadow-sm">
                        @foreach ($options as $value => $label)
                            <option value="{{ $value }}" {{ request($name) == $value ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach

            {{-- Nút lọc và reset --}}
            <div class="col-lg-2 col-md-6 text-end">
                <button type="submit" class="btn btn-success shadow-sm"><i class="bi bi-funnel"></i> Lọc</button>
                <a href="{{ request()->url() }}" class="btn btn-secondary shadow-sm"><i class="bi bi-arrow-clockwise"></i>
                    Reset</a>
            </div>
        </form>

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover table-sm small">
                        <thead class="table-dark text-center">
                            <tr>
                                <th class="text-center small ">STT</th>
                                <th class="text-center small">Mã Đơn Hàng</th>
                                <th class="text-center small">TT Người Đặt</th>
                                <th class="text-center small">TT Người Nhận</th>
                                <th class="text-center small">Giảm Giá</th>
                                <th class="text-center small">Phí Ship</th>
                                <th class="text-center small">Tổng Tiền</th>
                                <th class="text-center small">Phương Thức TT</th>
                                <th class="text-center small">Trạng Thái TT</th>
                                <th class="text-center small">Trạng Thái Đơn Hàng</th>
                                <th class="text-center small">Ngày Đặt</th>
                                <th class="text-center small">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr class="align-middle">
                                    <td class="text-center small">{{ $order->id }}</td>
                                    <td>{{ $order->order_code }}</td>
                                    <td>
                                        <strong>{{ $order->user->name }}</strong><br>
                                        <small class="text-muted">{{ $order->user->email }}</small><br>
                                        <small class="text-muted">{{ $order->user->phone }}</small><br>
                                        <small class="text-muted">{{ $order->user->address }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $order->recipient_name }}</strong><br>
                                        <small class="text-muted">{{ $order->recipient_phone }}</small><br>
                                        <small class="text-muted">{{ $order->recipient_address }}</small>
                                    </td>
                                    <td class="text-end">{{ number_format($order->promotion, 0, ',', '.') }} VND</td>
                                    <td class="text-end">{{ number_format($order->shipping_fee, 0, ',', '.') }} VND</td>
                                    <td class="text-end fw-bold text-primary">
                                        {{ number_format(round($order->total_price, -3), 0, ',', '.') }} VND
                                    </td>
                                    <td class="text-center small">
                                        <span class="badge bg-{{ $order->payment_method == 'COD' ? 'warning' : 'info' }}">
                                            {{ $order->payment_method }}
                                        </span>
                                    </td>
                                    <td class="text-center small">
                                        <span
                                            class="badge bg-{{ $order->payment_status == 'da_thanh_toan' ? 'success' : 'danger' }}">
                                            {{ $order->payment_status == 'da_thanh_toan' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                        </span>
                                    </td>
                                    <td class="text-center small">
                                        @php
                                            $statusColors = [
                                                'cho_xac_nhan' => 'secondary',
                                                'dang_chuan_bi' => 'warning',
                                                'dang_van_chuyen' => 'primary',
                                                'da_giao_hang' => 'success',
                                                'huy_don_hang' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                            {{ str_replace(['cho_xac_nhan', 'dang_chuan_bi', 'dang_van_chuyen', 'da_giao_hang', 'huy_don_hang'], ['Chờ xác nhận', 'Đang chuẩn bị', 'Đang vận chuyển', 'Đã giao hàng', 'Hủy đơn hàng'], $order->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center small">
                                        <a href="{{ route('admin.orders.edit', $order->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-pencil"></i> Sửa
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection