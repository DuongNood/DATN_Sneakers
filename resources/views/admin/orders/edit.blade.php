@extends('admin.layouts.master')
@section('title')
    Danh sách đơn hàng
@endsection
@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Chỉnh Sửa Đơn Hàng</h2>

        @if (session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('orders.update', $order->id) }}" method="POST" enctype="multipart/form-data"
            class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700">Mã Đơn Hàng</label>
                <input type="text" class="w-full p-2 border rounded-lg bg-gray-100" value="{{ $order->order_code }}"
                    disabled>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Khách Hàng</label>
                <input type="text" class="w-full p-2 border rounded-lg bg-gray-100" value="{{ $order->recipient_name }}"
                    disabled>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Phương Thức Thanh Toán</label>
                <input type="text" class="w-full p-2 border rounded-lg bg-gray-100" value="{{ $order->payment_method }}"
                    disabled>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Trạng Thái Thanh Toán</label>
                <select name="payment_status" class="w-full p-2 border rounded-lg"
                    {{ $order->payment_status == 'Đã thanh toán' ? 'disabled' : '' }}>
                    <option value="Chưa thanh toán" {{ $order->payment_status == 'Chưa thanh toán' ? 'selected' : '' }}>Chưa
                        thanh toán</option>
                    <option value="Đã thanh toán" {{ $order->payment_status == 'Đã thanh toán' ? 'selected' : '' }}>Đã thanh
                        toán</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Trạng Thái Đơn Hàng</label>
                <select name="status" class="w-full p-2 border rounded-lg">
                    @foreach ($statusOptions as $status)
                        <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                            {{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <h4 class="text-xl font-semibold mb-4">Chi Tiết Sản Phẩm</h4>
            <table class="w-full border-collapse border rounded-lg">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2">Mã SP</th>
                        <th class="p-2">Tên SP</th>
                        <th class="p-2">Ảnh</th>
                        <th class="p-2">Số lượng</th>
                        <th class="p-2">Giá</th>
                        <th class="p-2">Giảm giá</th>
                        <th class="p-2">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->details as $detail)
                        <tr class="border-b">
                            <td class="p-2">{{ $detail->productVariant->id }}</td>
                            <td class="p-2">{{ $detail->productVariant->product->name }}</td>
                            <td class="p-2"><img src="{{ asset('images/' . $detail->productVariant->product->image) }}"
                                    width="50"></td>
                            <td class="p-2">{{ $detail->quantity }}</td>
                            <td class="p-2">{{ number_format($detail->price, 0, ',', '.') }} VND</td>
                            <td class="p-2">{{ number_format($detail->discount, 0, ',', '.') }} VND</td>
                            <td class="p-2">{{ number_format($detail->total_price, 0, ',', '.') }} VND</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mb-4">
                <a class="btn btn-secondary" href="{{ route('orders.index') }}">Quay lại</a>
                <button type="submit" class="mt-4 bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded">Cập nhật</button>
            </div>
        </form>
    </div>
@endsection
