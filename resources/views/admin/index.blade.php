@extends('admin.layouts.master')
@section('title')
    Dashboard
@endsection
@section('content')
    <div class="container-fluid mt-4">
        <h2 class="mb-4 text-center">Thống kê hôm nay</h2>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card h-100 bg-primary text-white shadow">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-white-50 small">Tổng doanh thu</div>
                            <div class="text-lg font-weight-bold" id="dailySales"></div>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x text-white-50 align-self-end"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100 bg-success text-white shadow">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-white-50 small">Đơn hàng</div>
                            <div class="text-lg font-weight-bold" id="dailyOrderCount"></div>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x text-white-50 align-self-end"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100 bg-info text-white shadow">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-white-50 small">Sản phẩm bán chạy</div>
                            <div class="text-lg font-weight-bold" id="bestSellingProduct"></div>
                        </div>
                        <i class="fas fa-chart-line fa-2x text-white-50 align-self-end"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card h-100 bg-warning text-white shadow">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-white-50 small">Sản phẩm hot</div>
                            <div class="text-lg font-weight-bold" id="hotProduct"></div>
                        </div>
                        <i class="fas fa-fire fa-2x text-white-50 align-self-end"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Khách hàng mới</h5>
                        <p class="card-text">Danh sách khách hàng mới đăng ký trong ngày.</p>
                        <ul id="newCustomers" class="list-group list-group-flush">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng gần đây</h5>
                        <p class="card-text">Danh sách các đơn hàng mới nhất.</p>
                        <ul id="recentOrders" class="list-group list-group-flush">
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Doanh thu trong ngày</h5>
                        <canvas id="hourlySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        fetch('{{ route('admin.dashboard.daily_statistics') }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('dailySales').textContent = formatCurrency(data.daily_sales);
                document.getElementById('dailyOrderCount').textContent = data.daily_order_count;
                document.getElementById('bestSellingProduct').textContent = data.best_selling_product ? data
                    .best_selling_product.product_name : 'Không có';
                document.getElementById('hotProduct').textContent = data.hot_product ? data.hot_product.product_name :
                    'Không có';

                // Hiển thị danh sách khách hàng mới
                let newCustomersList = document.getElementById('newCustomers');
                newCustomersList.innerHTML = '';
                data.new_customers.forEach(customer => {
                    let item = document.createElement('div');
                    item.classList.add('list-group-item', 'd-flex', 'justify-content-between',
                        'align-items-center');
                    item.innerHTML = `
                <span>${customer.name}</span>
                <span class="text-muted">${formatDate(customer.created_at)}</span>
            `;
                    newCustomersList.appendChild(item);
                });

                // Hiển thị danh sách đơn hàng gần đây
                let recentOrdersList = document.getElementById('recentOrders');
                recentOrdersList.innerHTML = '';
                data.recent_orders.forEach(order => {
                    let item = document.createElement('div');
                    item.classList.add('list-group-item', 'd-flex', 'justify-content-between',
                        'align-items-center');
                    item.innerHTML = `
                <div>
                    <span>Đơn hàng #${order.order_code}</span> - 
                    <span class="text-muted">${formatDate(order.created_at)}</span>
                </div>
                <span>${formatCurrency(order.total_price)}</span>
            `;
                    recentOrdersList.appendChild(item);
                });

                // Hàm định dạng ngày giờ
                function formatDate(dateString) {
                    let date = new Date(dateString);
                    return date.toLocaleString(); // Định dạng ngày giờ theo locale hiện tại
                }

                // Hàm định dạng tiền tệ
                function formatCurrency(amount) {
                    return new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    }).format(amount);
                }

                // Vẽ biểu đồ doanh thu theo giờ
                let hourlyLabels = data.hourly_sales.map(item => item.hour + 'h');
                let hourlyRevenues = data.hourly_sales.map(item => item.revenue);

                let hourlyCtx = document.getElementById('hourlySalesChart').getContext('2d');
                new Chart(hourlyCtx, {
                    type: 'line',
                    data: {
                        labels: hourlyLabels,
                        datasets: [{
                            label: 'Doanh thu',
                            data: hourlyRevenues,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
    </script>
@endsection
