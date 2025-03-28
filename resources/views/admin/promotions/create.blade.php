@extends('admin.layouts.master')

@section('title')
    Thêm mã giảm giá
@endsection

@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Thêm mã giảm giá</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.promotions.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Tên Mã Giảm Giá</label>
                        <input type="text" name="promotion_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Loại Giảm Giá</label>
                        <select name="discount_type" class="form-select">
                            <option value="Giảm số tiền">Giảm số tiền</option>
                            <option value="Giảm theo %">Giảm theo %</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giá Trị Giảm Giá</label>
                        <input type="number" name="discount_value" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giảm giá tối đa</label>
                        <input type="number" name="max_discount_value" step="0.01" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ngày Bắt Đầu</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ngày Kết Thúc</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trạng Thái</label>
                        <select name="status" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Thêm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
