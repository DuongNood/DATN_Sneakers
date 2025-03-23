@extends('admin.layouts.master')
@section('title')
    Danh sách mã giảm giá
@endsection
@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Thêm mã giảm giá</h2>
        <form action="{{ route('promotions.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700">Tên Mã Giảm Giá</label>
                <input type="text" name="promotion_name" class="w-full p-2 border rounded-lg" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Loại Giảm Giá</label>
                <select name="discount_type" class="w-full p-2 border rounded-lg">
                    <option value="SO_TIEN">Số Tiền</option>
                    <option value="PHAN_TRAM">Phần Trăm</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Giá Trị Giảm Giá</label>
                <input type="number" name="discount_value" class="w-full p-2 border rounded-lg" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Giảm giá tối đa</label>
                <input type="number" name="max_discount_value" step="0.01" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Ngày Bắt Đầu</label>
                <input type="date" name="start_date" class="w-full p-2 border rounded-lg" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Ngày Kết Thúc</label>
                <input type="date" name="end_date" class="w-full p-2 border rounded-lg" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Mô tả</label>
                <textarea name="description" class="w-full border p-2 rounded"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Trạng Thái</label>
                <select name="status" class="w-full p-2 border rounded-lg">
                    <option value="1">Hoạt Động</option>
                    <option value="0">Ngừng Hoạt Động</option>
                </select>
            </div>

            <div class="mb-4">
                <a href="{{ route('promotions.index') }}" class="ml-4 text-blue-500">Quay lại</a>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Thêm</button>
            </div>
        </form>
    </div>
@endsection
