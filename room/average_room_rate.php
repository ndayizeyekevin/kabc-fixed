<?php 
// session_start();
$chartType = $_SESSION['chartType'] ?? 'bar';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Average Room Rate</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1"></script>
    <style>
        .formula-box {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .formula-box h5 {
            color: #28a745;
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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
<div class="container mt-4">
    <h3 class="text-center">💰 Average Room Rate Analysis</h3>

    <!-- Chart Type Selector -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="mySelect"><strong>📈 Chart Type:</strong></label>
                <select id="mySelect" class="form-control" onchange="myFunction()">
                    <option value="bar" selected>Bar Chart</option>
                    <option value="line">Line Chart</option>
                    <option value="pie">Pie Chart</option>
                    <option value="doughnut">Doughnut Chart</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Monthly Chart -->
    <h4 class="mt-4">📊 Monthly Average Room Rate - <?php echo date('Y'); ?></h4>
    <div class="chart-container">
        <canvas id="myChart"></canvas>
    </div>

    <hr>

    <!-- Date Range Selector -->
    <h4 class="mt-4">🔍 Check Average Room Rate by Date Range</h4>
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
            <button onclick="getData()" class="btn btn-success btn-block">💰 Calculate Average Rate</button>
        </div>
    </div>

    <!-- Period Summary Stats -->
    <div id="period-stats" style="display:none;" class="row mt-4">
        <div class="col-md-4">
            <div class="stats-card">
                <h4 id="total-income">0</h4>
                <p>Total Income</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h4 id="avg-rate">0</h4>
                <p>Average Room Rate</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h4 id="total-rooms">0</h4>
                <p>Total Room-Nights</p>
            </div>
        </div>
    </div>

    <!-- Date Range Chart -->
    <div id="dateRangeChartContainer" style="display:none;" class="mt-4">
        <h4>📊 Daily Average Room Rate for Selected Period</h4>
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
                    <th>Daily Income</th>
                    <th>Rooms Occupied</th>
                    <th>Avg Room Rate</th>
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
    'rgba(17, 153, 142, 0.8)',
    'rgba(56, 239, 125, 0.8)',
    'rgba(240, 147, 251, 0.8)',
    'rgba(245, 87, 108, 0.8)',
    'rgba(79, 172, 254, 0.8)',
    'rgba(0, 242, 254, 0.8)',
    'rgba(102, 126, 234, 0.8)',
    'rgba(118, 75, 162, 0.8)',
    'rgba(255, 159, 64, 0.8)',
    'rgba(255, 99, 132, 0.8)',
    'rgba(255, 205, 86, 0.8)',
    'rgba(75, 192, 192, 0.8)'
];

function buildChart(data, type = '<?php echo $chartType; ?>') {
    const ctx = document.getElementById('myChart').getContext('2d');
    if (chart) chart.destroy();

    // Adjust data presentation based on chart type
    let backgroundColor = type === 'pie' || type === 'doughnut' ? colors : 'rgba(17, 153, 142, 0.6)';
    let borderColor = type === 'pie' || type === 'doughnut' ? colors.map(c => c.replace('0.8', '1')) : 'rgba(17, 153, 142, 1)';

    chart = new Chart(ctx, {
        type: type,
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June',
                     'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [{
                label: 'Average Room Rate',
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
                            return context.label + ': ' + new Intl.NumberFormat().format(context.parsed.y);
                        }
                    }
                }
            },
            scales: type === 'pie' || type === 'doughnut' ? {} : {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat().format(value);
                        }
                    },
                    title: {
                        display: true,
                        text: 'Average Room Rate'
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
    data.append("type", "average_room_rate");

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

function loadAllMonths(type = '<?php echo $chartType; ?>') {
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

function myFunction() {
    const type = document.getElementById("mySelect").value;
    loadAllMonths(type);
}

function getData() {
    const start_date = document.getElementById('start_date').value;
    const end_date = document.getElementById('end_date').value;

    if (!start_date || !end_date) {
        alert('Please select both start and end dates');
        return;
    }

    if (new Date(start_date) > new Date(end_date)) {
        alert('Start date cannot be later than end date');
        return;
    }

    // Show loading state
    document.getElementById('results-container').style.display = 'block';
    document.getElementById('result').innerHTML = '<tr><td colspan="4" class="text-center"><div class="spinner-border text-success" role="status"><span class="sr-only">Loading...</span></div> Loading data...</td></tr>';

    const data = new FormData();
    data.append("type", "average_room_rate");
    data.append("start_date", start_date);
    data.append("end_date", end_date);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "getReportbyrange.php?t=0", true);

    xhr.onload = function () {
        // Parse the response to extract data
        var tempDiv = document.createElement('div');
        tempDiv.innerHTML = xhr.responseText;

        // Extract table rows
        var rows = tempDiv.querySelectorAll('tbody tr');
        dateRangeData = [];
        var totalIncome = 0;
        var totalRooms = 0;

        rows.forEach(function(row) {
            var cells = row.querySelectorAll('td');
            if (cells.length >= 4 && cells[0].textContent !== 'Period Average') {
                var date = cells[0].textContent;
                var income = parseFloat(cells[1].textContent.replace(/,/g, ''));
                var rooms = parseInt(cells[2].textContent);
                var avgRate = parseFloat(cells[3].textContent.replace(/,/g, ''));

                dateRangeData.push({
                    date: date,
                    income: income,
                    rooms: rooms,
                    avgRate: avgRate
                });

                totalIncome += income;
                totalRooms += rooms;
            }
        });

        // Update stats
        if (totalRooms > 0) {
            var avgRate = totalIncome / totalRooms;
            document.getElementById('total-income').textContent = new Intl.NumberFormat().format(totalIncome);
            document.getElementById('avg-rate').textContent = new Intl.NumberFormat().format(Math.round(avgRate));
            document.getElementById('total-rooms').textContent = totalRooms;
            document.getElementById('period-stats').style.display = 'flex';
        }

        // Display the table
        document.getElementById('result').innerHTML = xhr.responseText;

        // Automatically show the chart
        if (dateRangeData.length > 0) {
            showDateRangeChart();
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
    var data = dateRangeData.map(d => d.avgRate);

    var ctx2 = document.getElementById('dateRangeChart').getContext('2d');
    dateRangeChartInstance = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Average Room Rate',
                data: data,
                backgroundColor: 'rgba(17, 153, 142, 0.6)',
                borderColor: 'rgba(17, 153, 142, 1)',
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
                            return 'Avg Rate: ' + new Intl.NumberFormat().format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat().format(value);
                        }
                    },
                    title: {
                        display: true,
                        text: 'Average Room Rate'
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

// window.onload = loadAllMonths;
window.onload = function () {
    const defaultType = document.getElementById("mySelect").value;
    loadAllMonths(defaultType);
};
</script>
</body>
</html>
