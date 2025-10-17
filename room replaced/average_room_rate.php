<?php 
// session_start();
$chartType = $_SESSION['chartType'] ?? 'bar';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Average Room Rate Chart</title>
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
</head>
<body>
<div class="container mt-4">
    <h4 class="text-center">Average Room Rate Chart</h4>
    <div class="form-group">
        <label>Change Chart Type</label>
        <select id="mySelect" class="form-control" onchange="myFunction()">
            <option value="bar">Bar Chart</option>
            <option value="line">Line Chart</option>
        </select>
    </div>

    <canvas id="myChart" width="400" height="150"></canvas>

    <hr>
    <div class="row mt-4">
        <div class="col-md-4">Check by Dates</div>
        <div class="col-md-4"><input type="date" id="start_date" class="form-control"></div>
        <div class="col-md-4"><input type="date" id="end_date" class="form-control"></div>
        <div class="col-md-4 mt-2">
            <input type="button" value="Check" onclick="getData()" class="btn btn-outline-primary">
        </div>
    </div>

    <br><br>
    <table class="table table-bordered">
        <thead>
        <tr><th>Date</th><th>Value</th></tr>
        </thead>
        <tbody id="result"></tbody>
    </table>
</div>

<script>
let chart;

function buildChart(data, type = '<?php echo $chartType; ?>') {
    const ctx = document.getElementById('myChart').getContext('2d');
    if (chart) chart.destroy();

    chart = new Chart(ctx, {
        type: type,
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June',
                     'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [{
                label: 'Average Room Rate',
                backgroundColor: 'rgba(0, 123, 255, 0.5)',
                borderColor: 'blue',
                borderWidth: 1,
                data: data
            }]
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
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
    document.getElementById('result').innerHTML = '<tr><td colspan="2"><center><h6>Loading Please Wait</h6></center></td></tr>';

    const start_date = document.getElementById('start_date').value;
    const end_date = document.getElementById('end_date').value;

    if (start_date && end_date) {
        const data = new FormData();
        data.append("type", "average_room_rate");
        data.append("start_date", start_date);
        data.append("end_date", end_date);

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "getReportbyrange.php?t=0", true);

        xhr.onload = function () {
            document.getElementById('result').innerHTML = xhr.responseText;
        };

        xhr.send(data);
    } else {
        alert('Please select both start and end dates');
    }
}

// window.onload = loadAllMonths;
window.onload = function () {
    const defaultType = document.getElementById("mySelect").value;
    loadAllMonths(defaultType);
};
</script>
</body>
</html>
