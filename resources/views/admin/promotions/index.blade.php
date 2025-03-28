@extends('admin.layouts.master')

@section('title')
    Danh sách mã giảm giá
@endsection

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold">Danh Sách Mã Giảm Giá</h4>
            <a href="{{ route('admin.promotions.create') }}" class="btn btn-success">
                <i class="bi bi-plus-lg"></i> Thêm mới
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Tên mã</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Giảm giá tối đa</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $promotion)
                            <tr>
                                <td>{{ $promotion->id }}</td>
                                <td>{{ $promotion->promotion_name }}</td>
                                <td>{{ $promotion->discount_type }}</td>
                                <td>
                                    @if ($promotion->discount_type === 'Giảm theo %')
                                        {{ $promotion->discount_value }}%
                                    @else
                                        {{ number_format($promotion->discount_value, 0, ',', '.') }} VND
                                    @endif
                                </td>
                                <td>{{ date('d/m/Y', strtotime($promotion->start_date)) }}</td>
                                <td>{{ date('d/m/Y', strtotime($promotion->end_date)) }}</td>
                                <td>{{ number_format($promotion->max_discount_value, 0, ',', '.') }} VND</td>
                                <td>
                                    <span class="badge {{ $promotion->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $promotion->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.promotions.edit', $promotion->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-pencil"></i> Sửa
                                        </a>

                                        <form action="{{ route('admin.promotions.destroy', $promotion->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                <i class="mdi mdi-delete"></i> Xóa
                                            </button>
                                        </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $data->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
