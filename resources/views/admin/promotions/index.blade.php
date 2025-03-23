@extends('admin.layouts.master')
@section('title')
    Danh sách mã giảm giá
@endsection
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Danh sách mã giảm giá</h1>
    
    <div class="flex justify-end mb-4">
        <a href="{{ route('promotions.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Thêm mới</a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full table-auto border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">#</th>
                    <th class="border px-4 py-2">Tên mã</th>
                    <th class="border px-4 py-2">Loại</th>
                    <th class="border px-4 py-2">Giá trị</th>
                    <th class="border px-4 py-2">Ngày bắt đầu</th>
                    <th class="border px-4 py-2">Ngày kết thúc</th>
                    <th class="border px-4 py-2">Trạng thái</th>
                    <th class="border px-4 py-2">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($promotions as $promotion)
                <tr>
                    <td class="border px-4 py-2">{{ $loop->iteration }}</td>
                    <td class="border px-4 py-2">{{ $promotion->promotion_name }}</td>
                    <td class="border px-4 py-2">{{ $promotion->discount_type == 'amount' ? 'Số tiền' : 'Phần trăm' }}</td>
                    <td class="border px-4 py-2">{{ $promotion->discount_value }}</td>
                    <td class="border px-4 py-2">{{ $promotion->start_date }}</td>
                    <td class="border px-4 py-2">{{ $promotion->end_date }}</td>
                    <td class="border px-4 py-2">
                        <span class="{{ $promotion->status ? 'text-green-500' : 'text-red-500' }}">
                            {{ $promotion->status ? 'Đang hoạt động' : 'Hết hạn' }}
                        </span>
                    </td>
                    <td class="border px-4 py-2 flex space-x-2">
                        <a href="{{ route('promotions.edit', $promotion->id) }}" class="text-blue-500 hover:underline">Sửa</a>
                        <form action="{{ route('promotions.destroy', $promotion->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline">Xóa</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection