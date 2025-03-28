@extends('admin.layouts.master')
@section('title', 'Chỉnh sửa mã giảm giá')

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Chỉnh sửa mã giảm giá</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.promotions.update', $promotion->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Tên mã giảm giá</label>
                        <input type="text" name="promotion_name" class="form-control"
                            value="{{ $promotion->promotion_name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Loại giảm giá</label>
                        <select name="discount_type" class="form-select">
                            <option value="Giảm số tiền" {{ $promotion->discount_type == 'Giảm số tiền' ? 'selected' : '' }}>Giảm số tiền</option>
                            <option value="Giảm theo %" {{ $promotion->discount_type == 'Giảm theo %' ? 'selected' : '' }}>Giảm theo %</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giá trị giảm giá</label>
                        <input type="number" name="discount_value" step="0.01" class="form-control"
                            value="{{ $promotion->discount_value }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giảm giá tối đa</label>
                        <input type="number" name="max_discount_value" step="0.01" class="form-control"
                            value="{{ $promotion->max_discount_value }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-control"
                            value="{{ $promotion->start_date }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ngày kết thúc</label>
                        <input type="date" name="end_date" class="form-control"
                            value="{{ $promotion->end_date }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control">{{ $promotion->description }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trạng Thái</label>
                        <select name="status" class="form-select">
                            <option value="1" {{ $promotion->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ $promotion->status == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
