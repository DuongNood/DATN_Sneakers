@extends('admin.layouts.master')
@section('title')
    Danh sách mã giảm giá
@endsection
@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Chỉnh sửa mã giảm giá</h2>
        <form action="{{ route('promotions.update', $promotion->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700">Tên mã giảm giá</label>
                <input type="text" name="promotion_name" class="w-full border p-2 rounded"
                    value="{{ $promotion->promotion_name }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Loại giảm giá</label>
                <select name="discount_type" class="w-full border p-2 rounded">
                    <option value="so_tien" {{ $promotion->discount_type == 'so_tien' ? 'selected' : '' }}>Số tiền</option>
                    <option value="phan_tram" {{ $promotion->discount_type == 'phan_tram' ? 'selected' : '' }}>Phần trăm
                    </option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Giá trị giảm giá</label>
                <input type="number" name="discount_value" step="0.01" class="w-full border p-2 rounded"
                    value="{{ $promotion->discount_value }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Giảm giá tối đa</label>
                <input type="number" name="max_discount_value" step="0.01" class="w-full border p-2 rounded"
                    value="{{ $promotion->max_discount_value }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Ngày bắt đầu</label>
                <input type="date" name="start_date" class="w-full border p-2 rounded"
                    value="{{ $promotion->start_date }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Ngày kết thúc</label>
                <input type="date" name="end_date" class="w-full border p-2 rounded" value="{{ $promotion->end_date }}"
                    required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Mô tả</label>
                <textarea name="description" class="w-full border p-2 rounded">{{ $promotion->description }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Trạng Thái</label>
                <select name="status" class="w-full p-2 border rounded-lg">
                    <option value="1" {{ $promotion->status == 1 ? 'selected' : '' }}>Hoạt Động</option>
                    <option value="0" {{ $promotion->status == 0 ? 'selected' : '' }}>Ngừng Hoạt Động</option>
                </select>
            </div>

            <div class="mb-44">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Cập nhật</button>
                <a href="{{ route('promotions.index') }}" class="ml-4 text-blue-500">Quay lại</a>
            </div>
        </form>
    </div>
@endsection
