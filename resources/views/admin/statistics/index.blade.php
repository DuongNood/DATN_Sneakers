@extends('admin.layouts.master')
@section('title', 'Thống kê bán hàng')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4 text-center">📊 Thống kê doanh thu</h2>

        <div class="row g-3">
            <div class="col-md-3">
                <label for="filter_type" class="form-label">📆 Lọc theo:</label>
                <select id="filter_type" class="form-select" onchange="setDateRange()">
                    <option value="custom">🔄 Tùy chọn</option>
                    <option value="month">📅 Tháng này</option>
                    <option value="quarter">📊 Quý này</option>
                    <option value="year">📆 Năm nay</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label">🗓 Từ ngày:</label>
                <input type="date" id="start_date" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">📆 Đến ngày:</label>
                <input type="date" id="end_date" class="form-control">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100" onclick="fetchData()">Lọc thống kê</button>
            </div>
        </div>

        <div class="card p-3 mt-4">
            <canvas id="salesChart" height="100"></canvas>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card p-3">
                    <h4>🔥 Sản phẩm bán chạy</h4>
                    <ul id="bestSellers" class="list-group"></ul>
                </div>
                <div class="card p-3">
                    <h4>👁️ Sản phẩm hot</h4>
                    <ul id="hotProducts" class="list-group"></ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3">
                    <h4>📊 Thống kê sản phẩm theo danh mục</h4>
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card p-3 mt-4">
            <h4>🏆 Top khách hàng mua hàng</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>🥇 Hạng</th>
                        <th>👤 Tên khách hàng</th>
                        <th>📧 Email</th>
                        <th>💰 Tổng tiền đã chi tiêu (VND)</th>
                    </tr>
                </thead>
                <tbody id="topUsers"></tbody>
            </table>
        </div>
    </div>

    <script>
        function setDateRange() {
            let filterType = document.getElementById('filter_type').value;
            let startDateInput = document.getElementById('start_date');
            let endDateInput = document.getElementById('end_date');
            let today = new Date();

            if (filterType === 'month') {
                startDateInput.value = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            } else if (filterType === 'quarter') {
                let quarterStartMonth = Math.floor(today.getMonth() / 3) * 3;
                startDateInput.value = new Date(today.getFullYear(), quarterStartMonth, 1).toISOString().split('T')[0];
            } else if (filterType === 'year') {
                startDateInput.value = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
            }

            endDateInput.value = today.toISOString().split('T')[0];
        }

        function fetchData() {
            let startDate = document.getElementById('start_date').value;
            let endDate = document.getElementById('end_date').value;

            let url = `{{ route('admin.statistics.data') }}`;
            if (startDate && endDate) {
                url += `?start_date=${startDate}&end_date=${endDate}`;
            }

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    updateSalesChart(data.sales);
                    updateBestSellers(data.best_sellers);
                    updateHotProducts(data.hot_products);
                    updateCategoryChart(data.category_sales);
                    updateTopUsers(data.top_users);
                });
        }

        function updateSalesChart(salesData) {
            let ctx = document.getElementById('salesChart').getContext('2d');
            let labels = salesData.map(item => item.date);
            let revenues = salesData.map(item => item.revenue);

            if (window.salesChartInstance) window.salesChartInstance.destroy();
            window.salesChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Doanh thu',
                        data: revenues,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        fill: true
                    }]
                }
            });
        }

        function updateBestSellers(products) {
            let list = document.getElementById('bestSellers');
            list.innerHTML = '';
            products.forEach(product => {
                let li = document.createElement('li');
                li.classList.add('list-group-item');
                li.innerHTML =
                    `🔥 <strong>${product.product_name}</strong> - <strong class="text-danger">${product.total_sold}</strong> sản phẩm`;
                list.appendChild(li);
            });
        }

        function updateHotProducts(products) {
            let list = document.getElementById('hotProducts');
            list.innerHTML = '';
            products.forEach(product => {
                let li = document.createElement('li');
                li.classList.add('list-group-item');
                li.innerHTML =
                    `👁️ <strong>${product.product_name}</strong> - <strong class="text-danger">${product.view}</strong> lượt xem`;
                list.appendChild(li);
            });
        }

        function updateCategoryChart(categoryData) {
            let ctx = document.getElementById('categoryChart').getContext('2d');

            if (window.categoryChartInstance) window.categoryChartInstance.destroy();
            window.categoryChartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: categoryData.map(item => item.category_name),
                    datasets: [{
                        data: categoryData.map(item => item.total_sold),
                        backgroundColor: ['red', 'blue', 'green', 'orange', 'purple']
                    }]
                }
            });
        }

        function updateTopUsers(users) {
            let table = document.getElementById('topUsers');
            table.innerHTML = '';
            users.forEach((user, index) => {
                let row = `<tr>
                    <td>${index + 1}</td>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${new Intl.NumberFormat('vi-VN').format(user.total_spent)} VND</td>
                </tr>`;
                table.innerHTML += row;
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            setDateRange();
            fetchData();
        });
    </script>
@endsection
