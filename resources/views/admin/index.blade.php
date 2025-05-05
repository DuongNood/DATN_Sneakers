@extends('admin.layouts.master')
@section('title')
    Dashboard
@endsection
@section('content')
    <div class="container-fluid mt-4">
        <h2 class="mb-4 text-center">Thống kê</h2>

        <div class="mb-3 text-center">
            <button class="btn btn-sm btn-outline-primary mr-2" onclick="loadStatistics('daily')">Hôm nay</button>
            <button class="btn btn-sm btn-outline-primary mr-2" onclick="loadStatistics('weekly')">Tuần này</button>
            <button class="btn btn-sm btn-outline-primary mr-2" onclick="loadStatistics('monthly')">Tháng này</button>
            <button class="btn btn-sm btn-outline-primary" onclick="loadStatistics('yearly')">Năm nay</button>
        </div>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card h-100 bg-primary text-white shadow">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="text-white-50 small">Tổng doanh thu</div>
                            <div class="text-lg font-weight-bold" id="totalSales"></div>
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
                            <div class="text-lg font-weight-bold" id="totalOrders"></div>
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
                            <div class="text-white-50 small">Đơn hàng hủy</div>
                            <div class="text-lg font-weight-bold" id="cancelledOrders"></div>
                        </div>
                        <i class="fas fa-ban fa-2x text-white-50 align-self-end"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Khách hàng mới</h5>
                        <ul id="newCustomers" class="list-group list-group-flush">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng gần đây</h5>
                        <ul id="recentOrders" class="list-group list-group-flush">
                        </ul>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="growth-card">
                    <h5 class="growth-title">Tốc độ tăng trưởng của cửa hàng</h5>
                    <div class="mb-3">
                        <h6 class="card-subtitle mb-2 text-danger">Doanh thu</h6>
                        <div class="growth-row">
                            <div class="growth-label">
                                <i class="fas fa-chart-line growth-icon"></i>
                                <span>Ngày</span>
                            </div>
                            <div class="growth-value" id="dailySalesCurrent">-</div>
                            <div class="growth-rate" id="dailySalesGrowth">-</div>
                        </div>
                        <div class="growth-row">
                            <div class="growth-label">
                                <i class="fas fa-chart-line growth-icon"></i>
                                <span>Tuần</span>
                            </div>
                            <div class="growth-value" id="weeklySalesCurrent">-</div>
                            <div class="growth-rate" id="weeklySalesGrowth">-</div>
                        </div>
                        <div class="growth-row">
                            <div class="growth-label">
                                <i class="fas fa-chart-line growth-icon"></i>
                                <span>Tháng</span>
                            </div>
                            <div class="growth-value" id="monthlySalesCurrent">-</div>
                            <div class="growth-rate" id="monthlySalesGrowth">-</div>
                        </div>
                        <div class="growth-row">
                            <div class="growth-label">
                                <i class="fas fa-chart-line growth-icon"></i>
                                <span>Năm</span>
                            </div>
                            <div class="growth-value" id="yearlySalesCurrent">-</div>
                            <div class="growth-rate" id="yearlySalesGrowth">-</div>
                        </div>
                    </div>
                    <div>
                        <h6 class="card-subtitle mb-2 text-danger">Đơn hàng</h6>
                        <div class="growth-row">
                            <div class="growth-label">
                                <i class="fas fa-shopping-cart growth-icon"></i>
                                <span>Ngày</span>
                            </div>
                            <div class="growth-value" id="dailyOrdersCurrent">-</div>
                            <div class="growth-rate" id="dailyOrderGrowth">-</div>
                        </div>
                        <div class="growth-row">
                            <div class="growth-label">
                                <i class="fas fa-shopping-cart growth-icon"></i>
                                <span>Tuần</span>
                            </div>
                            <div class="growth-value" id="weeklyOrdersCurrent">-</div>
                            <div class="growth-rate" id="weeklyOrderGrowth">-</div>
                        </div>
                        <div class="growth-row">
                            <div class="growth-label">
                                <i class="fas fa-shopping-cart growth-icon"></i>
                                <span>Tháng</span>
                            </div>
                            <div class="growth-value" id="monthlyOrdersCurrent">-</div>
                            <div class="growth-rate" id="monthlyOrderGrowth">-</div>
                        </div>
                        <div class="growth-row">
                            <div class="growth-label">
                                <i class="fas fa-shopping-cart growth-icon"></i>
                                <span>Năm</span>
                            </div>
                            <div class="growth-value" id="yearlyOrdersCurrent">-</div>
                            <div class="growth-rate" id="yearlyOrderGrowth">-</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Doanh thu 30 ngày gần nhất</h5>
                        <canvas id="revenueLast30DaysChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let timeBasedSalesChartInstance = null;
        // let combinedStatusChartInstance = null;
        let revenueLast30DaysChartInstance = null;

        function loadStatistics(period = 'daily') {
            fetch(`{{ route('admin.dashboard.daily_statistics') }}?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalSales').textContent = formatCurrency(data.total_sales);
                    document.getElementById('totalOrders').textContent = data.total_orders;
                    document.getElementById('bestSellingProduct').textContent = data.best_selling_product ? data
                        .best_selling_product : 'Không có';
                    document.getElementById('cancelledOrders').textContent = data.cancelled_orders;

                    // Hiển thị giá trị hiện tại của doanh thu
                    document.getElementById('dailySalesCurrent').textContent = formatCurrency(data.daily_sales.current);
                    document.getElementById('weeklySalesCurrent').textContent = formatCurrency(data.weekly_sales
                        .current);
                    document.getElementById('monthlySalesCurrent').textContent = formatCurrency(data.monthly_sales
                        .current);
                    document.getElementById('yearlySalesCurrent').textContent = formatCurrency(data.yearly_sales
                        .current);

                    // Hiển thị tốc độ tăng trưởng doanh thu
                    displayGrowthRate('dailySalesGrowth', data.daily_sales.growth_rate);
                    displayGrowthRate('weeklySalesGrowth', data.weekly_sales.growth_rate);
                    displayGrowthRate('monthlySalesGrowth', data.monthly_sales.growth_rate);
                    displayGrowthRate('yearlySalesGrowth', data.yearly_sales.growth_rate);

                    // Hiển thị giá trị hiện tại của đơn hàng
                    document.getElementById('dailyOrdersCurrent').textContent = data.daily_orders.current;
                    document.getElementById('weeklyOrdersCurrent').textContent = data.weekly_orders.current;
                    document.getElementById('monthlyOrdersCurrent').textContent = data.monthly_orders.current;
                    document.getElementById('yearlyOrdersCurrent').textContent = data.yearly_orders.current;

                    // Hiển thị tốc độ tăng trưởng đơn hàng
                    displayGrowthRate('dailyOrderGrowth', data.daily_orders.growth_rate);
                    displayGrowthRate('weeklyOrderGrowth', data.weekly_orders.growth_rate);
                    displayGrowthRate('monthlyOrderGrowth', data.monthly_orders.growth_rate);
                    displayGrowthRate('yearlyOrderGrowth', data.yearly_orders.growth_rate);
                });
        }

        function displayGrowthRate(elementId, growthRate) {
            const element = document.getElementById(elementId);
            element.textContent = `${growthRate}%`;
            element.classList.remove('text-danger', 'text-success');
            if (growthRate > 0) {
                element.classList.add('text-success');
            } else if (growthRate < 0) {
                element.classList.add('text-danger');
            }
        }

        function formatDate(dateString) {
            let date = new Date(dateString);
            return date.toLocaleString();
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }

        // Hàm vẽ biểu đồ doanh thu 30 ngày gần nhất
        function renderRevenueLast30DaysChart(revenueData) {
            const ctx = document.getElementById('revenueLast30DaysChart').getContext('2d');
            const labels = Object.keys(revenueData);
            const revenues = Object.values(revenueData);

            if (revenueLast30DaysChartInstance) {
                revenueLast30DaysChartInstance.destroy();
            }

            revenueLast30DaysChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Doanh thu',
                        data: revenues,
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
        }

        // Hàm vẽ biểu đồ trạng thái thanh toán và đơn hàng kết hợp
        // function renderCombinedStatusChart(paymentStatuses, orderStatuses) {
        //     const ctx = document.getElementById('combinedStatusChart').getContext('2d');
        //     const rawLabels = Object.keys(paymentStatuses).concat(Object.keys(orderStatuses));
        //     const data = Object.values(paymentStatuses).concat(Object.values(orderStatuses));

        //     // Mapping màu cho từng trạng thái
        //     const statusColors = {
        //         'chua_thanh_toan': 'rgba(255, 99, 132, 0.6)', // Đỏ nhạt
        //         'da_thanh_toan': 'rgba(75, 192, 192, 0.6)', // Xanh lá nhạt
        //         'cho_xac_nhan': 'rgba(255, 206, 86, 0.6)', // Vàng nhạt
        //         'dang_chuan_bi': 'rgba(54, 162, 235, 0.6)', // Xanh dương nhạt
        //         'dang_van_chuyen': 'rgba(153, 102, 255, 0.6)', // Tím nhạt
        //         'da_giao_hang': 'rgba(201, 203, 207, 0.6)', // Xám nhạt
        //         'huy_don_hang': 'rgba(255, 159, 64, 0.6)' // Cam nhạt
        //     };

        //     // Mapping tên trạng thái sang tiếng Việt
        //     const statusLabels = {
        //         'chua_thanh_toan': 'Chưa thanh toán',
        //         'da_thanh_toan': 'Đã thanh toán',
        //         'cho_xac_nhan': 'Chờ xác nhận',
        //         'dang_chuan_bi': 'Đang chuẩn bị',
        //         'dang_van_chuyen': 'Đang vận chuyển',
        //         'da_giao_hang': 'Đã giao hàng',
        //         'huy_don_hang': 'Hủy đơn hàng'
        //     };

        //     const backgroundColors = rawLabels.map(label => statusColors[label] || 'rgba(0, 0, 0, 0.2)');
        //     const borderColors = backgroundColors.map(color => color.replace('0.6', '1'));
        //     const labels = rawLabels.map(label => statusLabels[label] || label);

        //     if (combinedStatusChartInstance) {
        //         combinedStatusChartInstance.destroy();
        //     }

        //     combinedStatusChartInstance = new Chart(ctx, {
        //         type: 'pie',
        //         data: {
        //             labels: labels,
        //             datasets: [{
        //                 label: 'Trạng thái',
        //                 data: data,
        //                 backgroundColor: backgroundColors,
        //                 borderColor: borderColors,
        //                 borderWidth: 1
        //             }]
        //         },
        //         options: {
        //             responsive: true,
        //             maintainAspectRatio: true,
        //             plugins: {
        //                 legend: {
        //                     position: 'bottom',
        //                 },
        //                 title: {
        //                     display: true,
        //                     text: 'Thống kê Trạng thái Thanh toán & Đơn hàng'
        //                 }
        //             }
        //         }
        //     });
        // }

        // // Hàm tạo màu ngẫu nhiên (bạn có thể bỏ hàm này vì chúng ta đang dùng màu cố định)
        // function generateColors(numColors) {
        //     const colors = [];
        //     for (let i = 0; i < numColors; i++) {
        //         const r = Math.floor(Math.random() * 255);
        //         const g = Math.floor(Math.random() * 255);
        //         const b = Math.floor(Math.random() * 255);
        //         colors.push(`rgba(${r}, ${g}, ${b}, 0.6)`);
        //     }
        //     return colors;
        // }

        // Lấy và vẽ biểu đồ doanh thu 30 ngày gần nhất khi trang tải
        fetch('{{ route('admin.dashboard.revenue_last_30_days') }}')
            .then(response => response.json())
            .then(data => {
                renderRevenueLast30DaysChart(data);
            });

        // Lấy và vẽ biểu đồ trạng thái thanh toán và đơn hàng kết hợp khi trang tải
        // fetch('{{ route('admin.dashboard.combined_statuses') }}')
        //     .then(response => response.json())
        //     .then(data => {
        //         renderCombinedStatusChart(data.payment_statuses, data.order_statuses);
        //     });

        // Tải danh sách khách hàng mới (tất cả) khi trang tải
        // fetch('{{ route('admin.dashboard.all_new_customers') }}')
        //     .then(response => response.json())
        //     .then(data => {
        //         let newCustomersList = document.getElementById('newCustomers');
        //         newCustomersList.innerHTML = '';
        //         data.forEach(customer => {
        //             let item = document.createElement('li');
        //             item.classList.add('list-group-item', 'd-flex', 'justify-content-between',
        //                 'align-items-center');
        //             item.innerHTML = `
        //             <span>${customer.name}</span>
        //             <span class="text-muted">${formatDate(customer.created_at)}</span>
        //         `;
        //             newCustomersList.appendChild(item);
        //         });
        //     });

        // Tải danh sách đơn hàng gần đây (tất cả) khi trang tải
        // fetch('{{ route('admin.dashboard.all_recent_orders') }}')
        //     .then(response => response.json())
        //     .then(data => {
        //         let recentOrdersList = document.getElementById('recentOrders');
        //         recentOrdersList.innerHTML = '';
        //         data.forEach(order => {
        //             let item = document.createElement('li');
        //             item.classList.add('list-group-item', 'd-flex', 'justify-content-between',
        //                 'align-items-center');
        //             item.innerHTML = `
        //             <div>
        //                 <span>Đơn hàng #${order.order_code}</span> -
        //                 <span class="text-muted">${formatDate(order.created_at)}</span>
        //             </div>
        //             <span>${formatCurrency(order.total_price)}</span>
        //         `;
        //             recentOrdersList.appendChild(item);
        //         });
        //     });

        // Tải thống kê ban đầu khi trang được tải (cho các số liệu phía trên)
        loadStatistics();
    </script>
@endsection

<style>
    .growth-card {
        background-color: #343a40;
        /* Màu nền tối tương tự */
        color: white;
        border-radius: 5px;
        padding: 15px;
        box-shadow: 0 0.15rem 0.5rem rgba(0, 0, 0, 0.15);
    }

    .growth-title {
        font-size: 1.25rem;
        margin-bottom: 15px;
        color: #f8f9fa;
        /* Màu chữ sáng */
    }

    .growth-row {
        display: flex;
        justify-content: space-between;
        /* Chia đều không gian giữa các cột */
        align-items: center;
        margin-bottom: 10px;
    }

    .growth-label {
        flex: 1;
        /* Chiếm một phần không gian */
        font-weight: bold;
        margin-right: 10px;
        /* Khoảng cách bên phải nhãn */
    }

    .growth-value {
        flex: 1;
        /* Chiếm một phần không gian */
        text-align: right;
    }

    .growth-rate {
        flex: 1;
        /* Chiếm một phần không gian */
        text-align: right;
        margin-left: 10px;
        /* Khoảng cách bên trái tỷ lệ */
    }

    .growth-icon {
        margin-right: 5px;
        color: #007bff;
        /* Màu biểu tượng */
    }

    .text-success {
        color: #28a745 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }
</style>
