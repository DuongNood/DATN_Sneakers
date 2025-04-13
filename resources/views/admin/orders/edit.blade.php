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
                    <div class="card">
                        <div class="card-header fw-bold">Thông Tin Người Đặt</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Khách Hàng</label>
                                <input type="text" class="form-control" value="{{ $order->user->name }}" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" value="{{ $order->user->email }}" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" value="{{ $order->user->phone }}" disabled>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" value="{{ $order->user->address }}" disabled>
                            </div>
                            <div class="mb-3">
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

                {{-- Card Thông tin người nhận --}}
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                            Thông Tin Người Nhận
                            <a type="button" class="" id="editRecipientButton"><i class="mdi mdi-pencil"></i> Sửa
                                </aa>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Tên Người Nhận</label>
                                <input type="text" class="form-control" name="recipient_name"
                                    value="{{ $order->recipient_name }}" disabled id="recipient_name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại người nhận</label>
                                <input type="text" class="form-control" name="recipient_phone"
                                    value="{{ $order->recipient_phone }}" disabled id="recipient_phone">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ người nhận</label>
                                <input type="text" class="form-control" name="recipient_address"
                                    value="{{ $order->recipient_address }}" disabled id="recipient_address">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phương Thức Thanh Toán</label>
                                <input type="text" class="form-control" value="{{ $order->payment_method }}" disabled>
                            </div>
                            {{-- Trạng thái đơn hàng --}}
                            <div class="mb-3">
                                <label class="form-label">Trạng Thái Đơn Hàng</label>
                                <select name="status" class="form-select"
                                    {{ $order->status === 'huy_don_hang' ? 'disabled' : '' }}>
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
            const recipientName = document.getElementById('recipient_name');
            const recipientPhone = document.getElementById('recipient_phone');
            const recipientAddress = document.getElementById('recipient_address');

            editButton.addEventListener('click', function() {
                recipientName.disabled = !recipientName.disabled;
                recipientPhone.disabled = !recipientPhone.disabled;
                recipientAddress.disabled = !recipientAddress.disabled;

                if (recipientName.disabled) {
                    editButton.innerHTML = '<i class="mdi mdi-pencil"></i> Sửa';
                } else {
                    editButton.innerHTML = '<i class="mdi mdi-delete"></i> Hủy';
                }
            });
        });
    </script>
@endsection
