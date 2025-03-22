@extends('admin.layouts.master')
@section('title')
    Danh sách đơn hàng
@endsection
@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Danh Sách Đơn Hàng</h2>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Mã Đơn Hàng</th>
                        <th class="py-3 px-6 text-left">Khách Hàng</th>
                        <th class="py-3 px-6 text-right">Tổng Tiền</th>
                        <th class="py-3 px-6 text-center">Thanh Toán</th>
                        <th class="py-3 px-6 text-center">Trạng Thái</th>
                        <th class="py-3 px-6 text-center">Ngày Đặt</th>
                        <th class="py-3 px-6 text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    @foreach ($orders as $order)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6">{{ $order->order_code }}</td>
                            <td class="py-3 px-6">
                                <p class="font-semibold">{{ $order->recipient_name }}</p>
                                <p class="text-sm text-gray-500">{{ $order->recipient_phone }}</p>
                                <p class="text-sm text-gray-500">{{ $order->recipient_address }}</p>
                            </td>
                            <td class="py-3 px-6 text-right font-bold text-blue-600">
                                {{ number_format($order->total_price, 0, ',', '.') }} VND
                            </td>
                            <td class="py-3 px-6 text-center">{{ $order->payment_method }} - {{ $order->payment_status }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span
                                    class="px-3 py-1 text-sm font-semibold rounded-full 
                            {{ $order->status == 'Đã giao hàng'
                                ? 'bg-green-200 text-green-700'
                                : ($order->status == 'Hủy đơn hàng'
                                    ? 'bg-red-200 text-red-700'
                                    : 'bg-yellow-200 text-yellow-700') }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-6 text-center">
                                <a href="{{ route('orders.edit', $order->id) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded text-sm">Sửa</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
