@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Thống kê bán hàng</h2>

    <p><strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue, 0, ',', '.') }} VNĐ</p>
    <p><strong>Tổng số đơn hàng:</strong> {{ $totalOrders }}</p>

    <h3>Sản phẩm bán chạy</h3>
    <ul>
        @foreach ($bestSellingProducts as $product)
            <li>{{ $product->product->name }} - {{ $product->total_quantity }} sản phẩm</li>
        @endforeach
    </ul>

    <h3>Khách hàng mua nhiều nhất</h3>
    <ul>
        @foreach ($topCustomers as $customer)
            <li>{{ $customer->customer->name }} - {{ $customer->total_orders }} đơn hàng</li>
        @endforeach
    </ul>
</div>
@endsection
