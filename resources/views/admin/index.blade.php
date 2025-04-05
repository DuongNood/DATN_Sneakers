@extends('admin.layouts.master')
@section('title')
    Dashboards
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

        <div class="card">
            <div class="card-body">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        function updateDateRange() {
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

            fetch(`{{ route('admin.statistics.data') }}?start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    let labels = data.sales.map(item => item.date);
                    let revenues = data.sales.map(item => item.revenue);

                    let ctx = document.getElementById('salesChart').getContext('2d');
                    if (window.salesChartInstance) {
                        window.salesChartInstance.destroy();
                    }
                    window.salesChartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Doanh thu',
                                data: revenues,
                                backgroundColor: 'blue'
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Ngày'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Doanh thu (VND)'
                                    }
                                }
                            }
                        }
                    });
                });
        }

        document.addEventListener("DOMContentLoaded", fetchData);
    </script>
@endsection
