@extends('admin.layouts.master')

@section('title', 'Đơn Hàng Chờ Xác Nhận Hủy')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold">Danh Sách Đơn Hàng Chờ Xác Nhận Hủy</h4>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover table-sm small">
                        <thead class="table-dark text-center">
                            <tr>
                                <th class="text-center small">Mã đơn hàng</th>
                                <th class="text-center small">Khách hàng</th>
                                <th class="text-center small">Ngày yêu cầu</th>
                                <th class="text-center small">Lý do hủy</th>
                                <th class="text-center small">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $order)
                                <tr class="align-middle">
                                    <td>{{ $order->order_code }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td class="text-center small">{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $order->cancellation_reason }}</td>
                                    <td class="text-center small">
                                        <form action="{{ route('admin.orders.confirm_cancellation', $order) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Xác nhận</button>
                                        </form>

                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                            data-target="#rejectModal{{ $order->id }}">
                                            Từ chối
                                        </button>

                                        <div class="modal fade" id="rejectModal{{ $order->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="rejectModalLabel">Từ chối yêu cầu
                                                            hủy đơn hàng</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form
                                                        action="{{ route('admin.orders.reject_cancellation', $order) }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="rejection_reason">Lý do từ chối (tùy
                                                                    chọn)</label>
                                                                <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Đóng</button>
                                                            <button type="submit" class="btn btn-primary">Xác nhận từ
                                                                chối</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Không có đơn hàng nào chờ xác nhận hủy.</td>
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