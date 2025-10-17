<?php
// session_start();

if (isset($_SESSION['chartType'])) {
    $chartType = $_SESSION['chartType'];
} else {
    $chartType = 'bar';
}

function fetchMonthlyOccupancy() {
    $monthlyData = [];
    $year = date("Y");

    for ($month = 1; $month <= 12; $month++) {
        $formattedMonth = sprintf('%02d', $month); // ensure 2-digit month
        $dateString = "$year-$formattedMonth"; // e.g., "2025-01"

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'getReportbyrange.php?t=1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'month' => $dateString, // send full YYYY-MM
                'type' => 'daily_occupancy_rate'
            ]),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $monthlyData[] = floatval(trim($response)); // ensure numeric
    }

    return json_encode($monthlyData);
}

$monthlyChartData = fetchMonthlyOccupancy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Monthly Occupancy Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">-->
</head>
<body>

<div class="container mt-5">
    <h3 class="text-center">Monthly Occupancy Rate - <?php echo date('F Y'); ?></h3>

    <div class="form-group">
        <label for="mySelect">Change Chart Type:</label>
        <select id="mySelect" class="form-control" onchange="updateChartType(this.value)">
            <option value="bar" selected>Bar Chart</option>
            <option value="line">Line Chart</option>
        </select>
    </div>

    <canvas id="myChart" width="400" height="150"></canvas>

    <hr>

    <div class="row mt-4">
        <div class="col-md-4"><strong>Check by Dates</strong></div>
        <div class="col-md-4"><input type="date" id="start_date" class="form-control"></div>
        <div class="col-md-4"><input type="date" id="end_date" class="form-control"></div>
        <div class="col-md-4 mt-2"><button onclick="getData()" class="btn btn-primary">Check</button></div>
    </div>

    <br><br>
    <table class="table table-bordered">
        <thead><tr><th>Date</th><th>Occupancy (%)</th></tr></thead>
        <tbody id="result"></tbody>
    </table>
</div>

<script>
    let monthData = <?php echo $monthlyChartData; ?>;
    let currentChartType = '<?php echo $chartType; ?>';

    const ctx = document.getElementById('myChart').getContext('2d');
    let chart = new Chart(ctx, {
        type: currentChartType,
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June',
                     'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [{
                label: 'Occupancy (%)',
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'blue',
                borderWidth: 2,
                data: monthData
            }]
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        max: 100
                    }
                }]
            }
        }
    });

    function updateChartType(type) {
        chart.destroy(); // remove the current chart
        chart = new Chart(ctx, {
            type: type,
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June',
                         'July', 'August', 'September', 'October', 'November', 'December'],
                datasets: [{
                    label: 'Occupancy (%)',
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'blue',
                    borderWidth: 2,
                    data: monthData
                }]
            },
            options: chart.options
        });
    }

    function getData() {
        document.getElementById('result').innerHTML = '<tr><td colspan="2" class="text-center">Loading...</td></tr>';
        var start_date = document.getElementById('start_date').value;
        var end_date = document.getElementById('end_date').value;

        if (start_date && end_date) {
            var data = new FormData();
            data.append("type", "daily_occupancy_rate");
            data.append("start_date", start_date);
            data.append("end_date", end_date);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "getReportbyrange.php?t=0");
            xhr.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    document.getElementById('result').innerHTML = this.responseText;
                }
            };
            xhr.send(data);
        } else {
            alert("Please select both start and end dates.");
        }
    }
</script>
