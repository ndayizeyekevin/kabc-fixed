<?php
// session_start();
$chartType = 'bar';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daily Occupancy Rate</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1"></script>
    <style>
        .formula-box {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .formula-box h5 {
            color: #007bff;
            margin-bottom: 10px;
        }
        .formula {
            font-family: 'Courier New', monospace;
            background: white;
            padding: 10px;
            border-radius: 3px;
            margin: 5px 0;
        }
        .example {
            color: #666;
            font-style: italic;
            margin-top: 5px;
        }
        .chart-container {
            position: relative;
            height: 400px;
            margin: 30px 0;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 10px 0;
            text-align: center;
        }
        .stats-card h4 {
            margin: 0;
            font-size: 2em;
        }
        .stats-card p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h3 class="text-center">📊 Daily Occupancy Rate Analysis</h3>

    <!-- Chart Type Selector -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="mySelect"><strong>📈 Chart Type:</strong></label>
                <select id="mySelect" class="form-control" onchange="updateChartType(this.value)">
                    <option value="bar" selected>Bar Chart</option>
                    <option value="line">Line Chart</option>
                    <option value="pie">Pie Chart</option>
                    <option value="doughnut">Doughnut Chart</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Monthly Chart -->
    <h4 class="mt-4">📊 Monthly Occupancy Rate - <?php echo date('Y'); ?></h4>
    <div class="chart-container">
        <canvas id="myChart"></canvas>
    </div>

    <hr>

    <!-- Date Range Selector -->
    <h4 class="mt-4">🔍 Check Occupancy by Date Range</h4>
    <div class="row mt-3">
        <div class="col-md-4">
            <label><strong>Start Date:</strong></label>
            <input type="date" id="start_date" class="form-control">
        </div>
        <div class="col-md-4">
            <label><strong>End Date:</strong></label>
            <input type="date" id="end_date" class="form-control">
        </div>
        <div class="col-md-4">
            <label>&nbsp;</label>
            <button onclick="getData()" class="btn btn-primary btn-block">📊 Calculate Occupancy</button>
        </div>
    </div>

    <!-- Period Summary Stats -->
    <div id="period-stats" style="display:none;" class="row mt-4">
        <div class="col-md-4">
            <div class="stats-card">
                <h4 id="total-occupied">0</h4>
                <p>Total Rooms Occupied</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h4 id="avg-occupancy">0%</h4>
                <p>Average Occupancy Rate</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h4 id="total-days">0</h4>
                <p>Days in Period</p>
            </div>
        </div>
    </div>

    <!-- Date Range Chart -->
    <div id="dateRangeChartContainer" style="display:none;" class="mt-4">
        <h4>📊 Daily Occupancy for Selected Period</h4>
        <div class="chart-container">
            <canvas id="dateRangeChart"></canvas>
        </div>
    </div>

    <!-- Results Table -->
    <br><br>
    <div id="results-container" style="display:none;">
        <h4>📋 Detailed Daily Breakdown</h4>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Date</th>
                    <th>Rooms Occupied</th>
                    <th>Occupancy Rate (%)</th>
                </tr>
            </thead>
            <tbody id="result"></tbody>
        </table>
    </div>
</div>

<script>
    let chart;
    let dateRangeChartInstance = null;
    let dateRangeData = [];

    // Color palette
    const colors = [
        'rgba(102, 126, 234, 0.8)',
        'rgba(118, 75, 162, 0.8)',
        'rgba(237, 100, 166, 0.8)',
        'rgba(255, 154, 158, 0.8)',
        'rgba(250, 208, 196, 0.8)',
        'rgba(154, 236, 219, 0.8)',
        'rgba(75, 192, 192, 0.8)',
        'rgba(54, 162, 235, 0.8)',
        'rgba(153, 102, 255, 0.8)',
        'rgba(255, 159, 64, 0.8)',
        'rgba(255, 99, 132, 0.8)',
        'rgba(255, 205, 86, 0.8)'
    ];

    function buildChart(data, type = 'bar') {
        const ctx = document.getElementById('myChart').getContext('2d');
        if (chart) chart.destroy();

        // Adjust data presentation based on chart type
        let backgroundColor = type === 'pie' || type === 'doughnut' ? colors : 'rgba(102, 126, 234, 0.6)';
        let borderColor = type === 'pie' || type === 'doughnut' ? colors.map(c => c.replace('0.8', '1')) : 'rgba(102, 126, 234, 1)';

        chart = new Chart(ctx, {
            type: type,
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June',
                         'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: [{
                    label: 'Occupancy Rate (%)',
                    backgroundColor: backgroundColor,
                    borderColor: borderColor,
                    borderWidth: 2,
                    data: data,
                    fill: type === 'line' ? false : true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: type === 'pie' || type === 'doughnut' ? 'right' : 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed.y.toFixed(2) + '%';
                            }
                        }
                    }
                },
                scales: type === 'pie' || type === 'doughnut' ? {} : {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Occupancy Rate (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                }
            }
        });
    }

    function getMonthData(month, callback) {
        const data = new FormData();
        data.append("month", month);
        data.append("type", "daily_occupancy_rate");

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "getReportbyrange.php?t=1", true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                callback(parseFloat(xhr.responseText));
            } else {
                callback(0);
            }
        };
        xhr.send(data);
    }

    function loadAllMonths(type = 'bar') {
        const months = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
        const results = [];
        let completed = 0;

        months.forEach((month, i) => {
            getMonthData("2025-" + month, function (value) {
                results[i] = value;
                completed++;
                if (completed === 12) {
                    buildChart(results, type);
                }
            });
        });
    }

    function updateChartType(type) {
        const defaultType = document.getElementById("mySelect").value;
        loadAllMonths(defaultType);
    }

    function getData() {
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;

        if (!start_date || !end_date) {
            alert("Please select both start and end dates.");
            return;
        }

        if (new Date(start_date) > new Date(end_date)) {
            alert("Start date cannot be later than end date.");
            return;
        }

        // Show loading state
        document.getElementById('results-container').style.display = 'block';
        document.getElementById('result').innerHTML = '<tr><td colspan="3" class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div> Loading data...</td></tr>';

        var data = new FormData();
        data.append("type", "daily_occupancy_rate");
        data.append("start_date", start_date);
        data.append("end_date", end_date);

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "getReportbyrange.php?t=0");
        xhr.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                // Parse the response to extract data
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = this.responseText;

                // Extract table rows
                var rows = tempDiv.querySelectorAll('tbody tr');
                dateRangeData = [];
                var totalOccupied = 0;
                var dayCount = 0;

                rows.forEach(function(row) {
                    var cells = row.querySelectorAll('td');
                    if (cells.length >= 2 && cells[0].textContent !== 'Total') {
                        var date = cells[0].textContent;
                        var occupancy = parseFloat(cells[1].textContent);
                        dateRangeData.push({
                            date: date,
                            occupancy: occupancy
                        });
                        totalOccupied += occupancy;
                        dayCount++;
                    }
                });

                // Update stats
                if (dayCount > 0) {
                    var avgOccupancy = totalOccupied / dayCount;
                    document.getElementById('total-occupied').textContent = totalOccupied.toFixed(0);
                    document.getElementById('avg-occupancy').textContent = avgOccupancy.toFixed(2) + '%';
                    document.getElementById('total-days').textContent = dayCount;
                    document.getElementById('period-stats').style.display = 'flex';
                }

                // Display the table
                document.getElementById('result').innerHTML = this.responseText;

                // Automatically show the chart
                if (dateRangeData.length > 0) {
                    showDateRangeChart();
                }
            }
        };
        xhr.send(data);
    }

    function showDateRangeChart() {
        if (dateRangeData.length === 0) {
            return;
        }

        document.getElementById('dateRangeChartContainer').style.display = 'block';

        // Destroy existing chart if it exists
        if (dateRangeChartInstance) {
            dateRangeChartInstance.destroy();
        }

        var labels = dateRangeData.map(d => d.date);
        var data = dateRangeData.map(d => d.occupancy);

        var ctx2 = document.getElementById('dateRangeChart').getContext('2d');
        dateRangeChartInstance = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Occupancy Rate (%)',
                    data: data,
                    backgroundColor: 'rgba(102, 126, 234, 0.6)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Occupancy: ' + context.parsed.y.toFixed(2) + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Occupancy Rate (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });

        // Scroll to chart
        document.getElementById('dateRangeChartContainer').scrollIntoView({ behavior: 'smooth' });
    }

    // Load monthly chart on page load
    window.onload = function () {
        const defaultType = document.getElementById("mySelect").value;
        loadAllMonths(defaultType);
    };
</script>
