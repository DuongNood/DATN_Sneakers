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
                    'discount_type' => ['' => 'Loại', 'Giảm theo %' => 'Giảm theo %', 'Giảm số tiền' => 'Giảm số tiền'],
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

            <div class="col-lg-2 col-md-6">
                <div class="input-group shadow-sm">
                    <select name="status" class="form-select">
                        <option value="">Trạng thái</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            {{-- Nút lọc và reset --}}
            <div class="col-lg-4 col-md-6 text-end">
                <button type="submit" class="btn btn-success shadow-sm"><i class="bi bi-funnel"></i> Lọc</button>
                <a href="{{ request()->url() }}" class="btn btn-secondary shadow-sm"><i class="bi bi-arrow-clockwise"></i>
                    Reset</a>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped table-bordered text-center">
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
