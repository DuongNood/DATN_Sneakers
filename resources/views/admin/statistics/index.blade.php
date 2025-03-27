@extends('admin.layouts.master')
@section('title')
    Thống kê bán hàng
@endsection
@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Thống kê doanh thu</h2>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filter_type" class="form-label">Lọc theo:</label>
                <select id="filter_type" class="form-select" onchange="updateDateRange()">
                    <option value="custom">Tùy chọn</option>
                    <option value="month">Tháng này</option>
                    <option value="quarter">Quý này</option>
                    <option value="year">Năm nay</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label">Từ ngày:</label>
                <input type="date" id="start_date" class="form-control" value="{{ now()->subMonth()->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Đến ngày:</label>
                <input type="date" id="end_date" class="form-control" value="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100" onclick="fetchData()">Lọc</button>
            </div>
        </div>

        <div class="card p-3 mb-4">
            <canvas id="salesChart"></canvas>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card p-3 mb-4">
                    <h4>Sản phẩm bán chạy</h4>
                    <ul id="bestSellers" class="list-group"></ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3 mb-4">
                    <h4>Thống kê danh mục</h4>
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card p-3 mb-4">
            <h4>Top khách hàng mua hàng</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Hạng</th>
                        <th>Tên khách hàng</th>
                        <th>Email</th>
                        <th>Tổng tiền đã chi tiêu (VND)</th>
                    </tr>
                </thead>
                <tbody id="topCustomersTable">
                    <!-- Dữ liệu sẽ được cập nhật từ JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function updateDateRange() {
            let filterType = document.getElementById('filter_type').value;
            let startDateInput = document.getElementById('start_date');
            let endDateInput = document.getElementById('end_date');
            let now = new Date();

            if (filterType === 'month') {
                startDateInput.value = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
            } else if (filterType === 'quarter') {
                let quarterStartMonth = Math.floor(now.getMonth() / 3) * 3;
                startDateInput.value = new Date(now.getFullYear(), quarterStartMonth, 1).toISOString().split('T')[0];
            } else if (filterType === 'year') {
                startDateInput.value = new Date(now.getFullYear(), 0, 1).toISOString().split('T')[0];
            }
            endDateInput.value = new Date().toISOString().split('T')[0];
        }

        function fetchData() {
            let startDate = document.getElementById('start_date').value;
            let endDate = document.getElementById('end_date').value;

            fetch(`{{ route('admin.statistics.data') }}?start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    // Cập nhật biểu đồ doanh thu
                    let labels = data.sales.map(item => item.date);
                    let revenues = data.sales.map(item => item.revenue);

                    let ctx = document.getElementById('salesChart').getContext('2d');
                    if (window.salesChartInstance) window.salesChartInstance.destroy();
                    window.salesChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Doanh thu',
                                data: revenues,
                                borderColor: 'blue',
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true
                        }
                    });

                    // Hiển thị danh sách sản phẩm bán chạy
                    let bestSellersList = document.getElementById('bestSellers');
                    bestSellersList.innerHTML = '';
                    data.best_sellers.forEach(product => {
                        let li = document.createElement('li');
                        li.classList.add('list-group-item');
                        li.textContent = `${product.product_name} - ${product.total_sold} sản phẩm`;
                        bestSellersList.appendChild(li);
                    });

                    // Hiển thị thống kê danh mục
                    let categoryCtx = document.getElementById('categoryChart').getContext('2d');
                    if (window.categoryChartInstance) window.categoryChartInstance.destroy();
                    window.categoryChartInstance = new Chart(categoryCtx, {
                        type: 'pie',
                        data: {
                            labels: data.category_sales.map(item => item.category_name),
                            datasets: [{
                                data: data.category_sales.map(item => item.total_sold),
                                backgroundColor: ['red', 'blue', 'green', 'orange', 'purple']
                            }]
                        }
                    });

                    // Hiển thị danh sách top khách hàng
                    let topCustomersTable = document.getElementById('topCustomersTable');
                    topCustomersTable.innerHTML = "";
                    data.top_customers.forEach((customer, index) => {
                        let row = `<tr>
                            <td>${index + 1}</td>
                            <td>${customer.name}</td>
                            <td>${customer.email}</td>
                            <td>${new Intl.NumberFormat('vi-VN').format(customer.total_spent)} VND</td>
                        </tr>`;
                        topCustomersTable.innerHTML += row;
                    }); 
                });
        }

        document.addEventListener("DOMContentLoaded", fetchData);
    </script>
@endsection
