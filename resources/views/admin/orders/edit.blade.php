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

            <div class="row">
                {{-- Card Thông tin người đặt --}}
                <div class="col-md-6 mb-4">
                    <div class="card" style="box-shadow: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.05);">
                        <div class="card-header fw-bold">Thông Tin Người Đặt</div>
                        <div class="card-body" style="display: grid; grid-template-columns: auto 1fr; gap: 0.5rem;">
                            <label style="font-weight: bold;"><i class="mdi mdi-account me-1"></i> Khách Hàng:</label>
                            <p style="margin-bottom: 0;">{{ $order->user->name }}</p>

                            <label style="font-weight: bold;"><i class="mdi mdi-email me-1"></i> Email:</label>
                            <p style="margin-bottom: 0;">{{ $order->user->email }}</p>

                            <label style="font-weight: bold;"><i class="mdi mdi-phone me-1"></i> Số điện thoại:</label>
                            <p style="margin-bottom: 0;">{{ $order->user->phone }}</p>

                            <label style="font-weight: bold;"><i class="mdi mdi-home me-1"></i> Địa chỉ:</label>
                            <p style="margin-bottom: 0;">{{ $order->user->address }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card Thông tin người nhận --}}
                <div class="col-md-6 mb-4">
                    <div class="card" style="box-shadow: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.05);">
                        <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                            Thông Tin Người Nhận
                            <button type="button" class="btn btn-sm btn-outline-primary" id="editRecipientButton">
                                <i class="mdi mdi-pencil"></i> Sửa
                            </button>
                        </div>
                        <div class="card-body" style="display: grid; grid-template-columns: auto 1fr; gap: 0.5rem;">
                            <label for="recipient_name_readonly" style="font-weight: bold;"><i
                                    class="mdi mdi-account me-1"></i> Tên:</label>
                            <p id="recipient_name_readonly" style="margin-bottom: 0;">{{ $order->recipient_name }}</p>
                            <input type="text" class="form-control d-none form-control-sm" name="recipient_name"
                                value="{{ old('recipient_name', $order->recipient_name) }}" id="recipient_name_editable"
                                style="grid-column: 2;">

                            <label for="recipient_phone_readonly" style="font-weight: bold;"><i
                                    class="mdi mdi-phone me-1"></i> Điện thoại:</label>
                            <p id="recipient_phone_readonly" style="margin-bottom: 0;">{{ $order->recipient_phone }}</p>
                            <input type="text" class="form-control d-none form-control-sm" name="recipient_phone"
                                value="{{ old('recipient_phone', $order->recipient_phone) }}" id="recipient_phone_editable"
                                style="grid-column: 2;">

                            <label for="recipient_address_readonly" style="font-weight: bold;"><i
                                    class="mdi mdi-map-marker me-1"></i> Địa chỉ:</label>
                            <p id="recipient_address_readonly" style="margin-bottom: 0;">{{ $order->recipient_address }}
                            </p>
                            <input type="text" class="form-control d-none form-control-sm" name="recipient_address"
                                value="{{ old('recipient_address', $order->recipient_address) }}"
                                id="recipient_address_editable" style="grid-column: 2;">
                        </div>
                    </div>
                </div>

                {{-- Card Thông tin thanh toán và trạng thái --}}
                <div class="col mb-4">
                    <div class="card" style="box-shadow: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.05);">
                        <div class="card-header fw-bold">Thông Tin Thanh Toán & Trạng Thái</div>
                        <div class="card-body" style="display: grid; grid-template-columns: auto 1fr; gap: 0.5rem;">
                            <label for="payment_method" style="font-weight: bold;"><i class="mdi mdi-credit-card me-1"></i>
                                Thanh toán:</label>
                            <input type="text" class="form-control form-control-sm" value="{{ $order->payment_method }}"
                                disabled id="payment_method" style="grid-column: 2;">

                            <label for="payment_status" style="font-weight: bold;"><i class="mdi mdi-cash-check me-1"></i>
                                Trạng Thái Thanh Toán:</label>
                            <select name="payment_status" class="form-select form-select-sm" id="payment_status"
                                style="grid-column: 2;">
                                <option value="chua_thanh_toan"
                                    {{ $order->payment_status == 'chua_thanh_toan' ? 'selected' : '' }}>Chưa thanh toán
                                </option>
                                <option value="da_thanh_toan"
                                    {{ $order->payment_status == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán
                                </option>
                            </select>

                            <label for="status" style="font-weight: bold;"><i
                                    class="mdi mdi-package-variant-closed me-1"></i> Trạng Thái Đơn Hàng:</label>
                            <select name="status" class="form-select form-select-sm"
                                {{ $order->status === 'huy_don_hang' ? 'disabled' : '' }} style="grid-column: 2;">
                                <option value="{{ $order->status }}" selected>
                                    {{ str_replace(['cho_xac_nhan', 'dang_chuan_bi', 'dang_van_chuyen', 'da_giao_hang', 'huy_don_hang'], ['Chờ xác nhận', 'Đang chuẩn bị', 'Đang vận chuyển', 'Đã giao hàng', 'Hủy đơn hàng'], $order->status) }}
                                </option>

                                @php
                                    $validNextStatuses = [
                                        'cho_xac_nhan' => ['dang_chuan_bi', 'dang_van_chuyen', 'da_giao_hang'],
                                        'dang_chuan_bi' => ['dang_van_chuyen', 'da_giao_hang'],
                                        'dang_van_chuyen' => ['da_giao_hang'],
                                        'da_giao_hang' => [],
                                    ];
                                @endphp

                                @foreach ($validNextStatuses[$order->status] ?? [] as $nextStatus)
                                    <option value="{{ $nextStatus }}">
                                        {{ str_replace(['cho_xac_nhan', 'dang_chuan_bi', 'dang_van_chuyen', 'da_giao_hang'], ['Chờ xác nhận', 'Đang chuẩn bị', 'Đang vận chuyển', 'Đã giao hàng'], $nextStatus) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
                <div class="card mt-3" style="box-shadow: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.05);">
                    <div class="card-body">
                        <h5 class="fw-bold">Thông Tin Thanh Toán</h5>
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-weight: bold;">Tổng tiền hàng:</span>
                            <strong>
                                {{ number_format($order->orderDetails->sum(fn($detail) => $detail->price), 0, ',', '.') }}
                                VND
                            </strong>
                        </div>
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-weight: bold;">Giảm giá:</span>
                            <strong style="color: green;">-{{ number_format($order->promotion, 0, ',', '.') }}
                                VND</strong>
                        </div>
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-weight: bold;">Phí vận chuyển:</span>
                            <strong>{{ number_format($order->shipping_fee, 0, ',', '.') }} VND</strong>
                        </div>
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; border-top: 1px solid #eee; padding-top: 1rem;">
                            <span class="fw-bold" style="font-size: 1.1rem;">Tổng Thanh Toán:</span>
                            <strong class="text-danger"
                                style="font-size: 1.1rem;">{{ number_format($order->total_price, 0, ',', '.') }}
                                VND</strong>
                        </div>
                    </div>
                </div>

                {{-- Nút hành động --}}
                <div class="d-flex justify-content-between mt-3 w-100">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-light">
                        <i class="mdi mdi-arrow-left"></i> Quay lại
                    </a>
                    <div>
                        @if ($order->status !== 'huy_don_hang' && $order->status !== 'da_giao_hang')
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                data-bs-target="#cancelOrderModal">
                                <i class="mdi mdi-cancel"></i> Hủy đơn hàng
                            </button>
                        @endif
                        <button type="submit" class="btn btn-success">
                            <i class="mdi mdi-check"></i> Cập nhật
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Modal Hủy Đơn Hàng --}}
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Xác Nhận Hủy Đơn Hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.orders.cancel_direct', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Nhập lý do hủy đơn hàng:</p>
                        <textarea name="cancellation_reason" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButton = document.getElementById('editRecipientButton');
            const readonlyFields = document.querySelectorAll(
                '#recipient_name_readonly, #recipient_phone_readonly, #recipient_address_readonly');
            const editableFields = document.querySelectorAll(
                '#recipient_name_editable, #recipient_phone_editable, #recipient_address_editable');

            editButton.addEventListener('click', function() {
                readonlyFields.forEach(field => field.classList.toggle('d-none'));
                editableFields.forEach(field => field.classList.toggle('d-none'));

                if (editButton.querySelector('i').classList.contains('mdi-pencil')) {
                    editButton.innerHTML = '<i class="mdi mdi-check"></i> Lưu';
                    editButton.classList.remove('btn-outline-primary');
                    editButton.classList.add('btn-success');
                } else {
                    editButton.innerHTML = '<i class="mdi mdi-pencil"></i> Sửa';
                    editButton.classList.remove('btn-success');
                    editButton.classList.add('btn-outline-primary');
                    // Ở đây bạn có thể thêm logic để tự động submit form nếu muốn
                    // document.querySelector('form').submit();
                }
            });
        });
    </script>
@endsection
