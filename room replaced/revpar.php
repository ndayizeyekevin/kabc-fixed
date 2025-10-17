<?php 
session_start();
// Default chart type
if(isset($_SESSION['chartType'])){
    $chartType = $_SESSION['chartType'];
} else {
    $chartType = 'bar';  
}

// Handle month selection
if(isset($_POST['checkMonth'])){
    $month = $_POST['selectedmonth'];
    $date = date('Y-'.$month.'-')."1";
    $end = date('Y-'.$month.'-') . date('t', strtotime($date)); //get end date of month
    $mm = date('M-Y', strtotime($date));  
} else {
    $date = date('Y-m-')."1";
    $end = date('Y-m-') . date('t', strtotime($date)); //get end date of month
    $mm = date('M-Y', strtotime($date));     
}

// Get RevPAR data for a specific month
function monthlyRevpar($month){
    include '../inc/conn.php';
    $date = date('Y-'.$month.'-')."1";
    $end = date('Y-'.$month.'-') . date('t', strtotime($date)); //get end date of month
    $mm = date('M-Y', strtotime($date)); 
    
    $sql = "SELECT * FROM tbl_acc_room";
    $result = $conn->query($sql);
    $rooms = $result->num_rows;

    $total = 0;
    
    // Calculate total RevPAR for the month
    while(strtotime($date) <= strtotime($end)) {
        $day_num = date('d', strtotime($date));
        $day_name = date('l', strtotime($date));
        $month = date('m', strtotime($date));
        $year = date('Y', strtotime($date));
        
        // Get RevPAR for this day
        $total += getDailyRevpar($year."-".$month."-".$day_num);
        
        // Move to next day
        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
    }
    
    // Calculate average RevPAR for the month
    $days = date('t', strtotime($end));
    return $total > 0 ? round($total / $days, 2) : 0;
}

// Get RevPAR for a specific date
function getDailyRevpar($date){
    include '../inc/conn.php';
    
    // Get total rooms
    $sql = "SELECT * FROM tbl_acc_room";
    $result = $conn->query($sql);
    $rooms = $result->num_rows;
    
    if($rooms == 0) return 0; // Avoid division by zero
    
    // Get bookings for the date
    $sql = "SELECT * FROM tbl_acc_booking WHERE (booking_status_id = 6 OR booking_status_id = 5)";
    $result = $conn->query($sql);
    
    $sale = 0;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Check if booking is active on the specified date
            if(strtotime($date) >= strtotime($row['checkin_date']) && 
               strtotime($date) <= strtotime($row['checkout_date']) && 
               time() >= strtotime($date)) {
                $sale += $row['room_price'];
            }
        }
    }
    
    // Calculate RevPAR (Revenue per Available Room)
    return $sale > 0 ? round($sale / $rooms, 2) : 0;
}

// Function to get all months' RevPAR data
function getAllMonthsRevpar() {
    $data = [];
    // Get data for all 12 months
    for($i = 1; $i <= 12; $i++) {
        $data[] = monthlyRevpar($i);
    }
    return $data;
}

// Get all RevPAR data
$allMonthsData = getAllMonthsRevpar();

// Get current month's RevPAR for highlighting
$currentMonth = date('n');
$currentMonthRevpar = monthlyRevpar($currentMonth);
$currentMonthName = date('F');
?>

<div class="colr-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background-color:white">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h3>RevPAR Report</h3>
                        <p class="text-muted">Revenue Per Available Room Analysis</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="form-group">
                            <select id="mySelect" class="form-control" onchange="updateChartType()">
                                <option value="">Change Chart Type</option>
                                <option value="line" <?php echo $chartType == 'line' ? 'selected' : ''; ?>>Line Chart</option>
                                <option value="bar" <?php echo $chartType == 'bar' ? 'selected' : ''; ?>>Bar Chart</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h4>Monthly RevPAR Overview</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="position: relative; height:50vh; width:100%">
                            <canvas id="revparChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5>Current Month RevPAR</h5>
                            </div>
                            <div class="card-body text-center">
                                <h2><?php echo number_format($currentMonthRevpar, 2); ?></h2>
                                <p class="text-muted"><?php echo $currentMonthName; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5>Year Average RevPAR</h5>
                            </div>
                            <div class="card-body text-center">
                                <h2><?php 
                                    $yearAvg = array_sum($allMonthsData) / count(array_filter($allMonthsData));
                                    echo number_format($yearAvg, 2); 
                                ?></h2>
                                <p class="text-muted">Year <?php echo date('Y'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>RevPAR by Date Range</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" id="start_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" id="end_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button class="btn btn-primary form-control" onclick="getRevparData()">Generate Report</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>RevPAR Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="bg-warning">
                                    <tr>
                                        <th>Date</th>
                                        <th>RevPAR Value</th>
                                    </tr>
                                </thead>
                                <tbody id="result">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
// Define months array
const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

// Get the RevPAR data from PHP
const revparData = <?php echo json_encode($allMonthsData); ?>;

// Get current chart type
let chartType = '<?php echo $chartType; ?>';

// Initialize chart
let revparChart;
let chartColors = {
    primary: '#4e73df',
    success: '#1cc88a',
    info: '#36b9cc',
    warning: '#f6c23e',
    danger: '#e74a3b',
    secondary: '#858796',
    light: '#f8f9fc',
    dark: '#5a5c69'
};

function initChart() {
    const ctx = document.getElementById('revparChart').getContext('2d');
    
    revparChart = new Chart(ctx, {
        type: chartType,
        data: {
            labels: months,
            datasets: [{
                label: 'RevPAR',
                data: revparData,
                backgroundColor: chartType === 'bar' ? Array(12).fill(chartColors.primary) : 'rgba(78, 115, 223, 0.05)',
                borderColor: chartColors.primary,
                borderWidth: 2,
                pointBackgroundColor: chartColors.primary,
                pointBorderColor: '#fff',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: chartColors.primary,
                pointHitRadius: 10,
                pointBorderWidth: 2,
                lineTension: 0.3
            }]
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        unit: 'month'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 12
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) {
                            return '$' + value;
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Revenue Per Available Room ($)'
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        return 'RevPAR: $' + tooltipItem.yLabel;
                    }
                }
            }
        }
    });
}

// Update chart type
function updateChartType() {
    const newChartType = document.getElementById('mySelect').value;
    if (newChartType) {
        chartType = newChartType;
        
        // Save chart type in session using AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'save_chart_type.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('chartType=' + chartType);
        
        // Destroy current chart
        if (revparChart) {
            revparChart.destroy();
        }
        
        // Create new chart with updated type
        initChart();
    }
}

// Get RevPAR data for specified date range
function getRevparData() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }
    
    // Show loading message
    document.getElementById('result').innerHTML = '<tr><td colspan="2" class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p>Loading data, please wait...</p></td></tr>';
    
    // Create AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'getReportbyrange.php?t=0', true);
    xhr.onreadystatechange = function() {
        if (this.readyState === 4) {
            document.getElementById('result').innerHTML = this.responseText;
        }
    };
    
    // Prepare form data
    const formData = new FormData();
    formData.append('type', 'revpar');
    formData.append('start_date', startDate);
    formData.append('end_date', endDate);
    
    // Send request
    xhr.send(formData);
}

// Initialize chart on page load
document.addEventListener('DOMContentLoaded', function() {
    initChart();
});
</script>

<?php
// Add this to save chart type in session
if(isset($_GET['chartType'])) {
    $_SESSION['chartType'] = $_GET['chartType'];
    echo "Chart type saved!";
    exit;
}
?>
