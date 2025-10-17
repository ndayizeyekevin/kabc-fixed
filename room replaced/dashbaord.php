
        
<?php
        if(isset($_POST['close'])){



 include '../inc/conn.php';
 $date = $_POST['closedate'];
//  $day = date("d");
//  $month = date("m");
//  $year = date("Y");
 
//  $today = $year ."-".$month."-".$day;


 $sql = "SELECT * FROM tbl_acc_booking WHERE checkout_date = '$date'";
$result = $conn->query($sql);
$sale=0;
if ($result->num_rows > 0) {
  // output data of each row
  echo "<script>alert('Some Guest not Checked Out'); window.location='?resto=home'</script>";
  exit;
    
}

  if (isset($_POST['approve'])){


  $date = $_POST['closedate'];
  $time = time();
  $sql = "UPDATE tbl_acc_booking SET day_closed_at = '$date'";

if ($conn->query($sql) === TRUE) {
  echo "<script>alert('Day Successfull closed'); window.location='?resto=home' </script>";
  exit;
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
  exit;
}


} else {
echo "<script>alert('Comfirm before closing the day')</script>";
}

}
?>



<?php 
   
   if(isset($_SESSION['chartType'])){
   
    $chartType = $_SESSION['chartType'] ;
    
   }else{
       
     $chartType = 'line' ;  
       
   }
?>
<div class="colr-area">
        <div class="container">
            
            
                   <div class="row">
                <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12" style="background-color:white">
            <br><select id="mySelect" class="form-control" onchange="myFunction()">
  <option value="">Change Chart type</option>
  <option value="line">Line Chart</option>
  <option value="bar">Bar Chart</option>
</select>
<center><br>

                
                
                   <strong>Bookings :<?php echo date('d, M, Y')?></strong>
                    </p></center>

 <div class="card">
                    <div class="card-body">
                    <div class="chart" >
                     <canvas id="myChart" width="400" height="200"></canvas>
                    </div>
            
                </div> 
                
                 </div>  </div> 
                 <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="background-color:white">
                
                <br> <h4</h4>
                <div class="card" hidden>
                    <div class="card-header">
                         <h4>Today Summary</h4>
                        </div>
                        <div class="card-body">
               
<p>Total Booking:</p>
<p>Expected arrival:</p>  
<p>Expected DEPARTURE
:</p>
<p>Breakfast:</p> 

                </div> </div>
                </div> </div>
            
            <hr>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="color-wrap">
                        <div class="row">
                            <a href="?resto=allRoom" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink">
                                    <h1><i class="fa fa-user"></i></h1>
                                    <p>All Rooms</p>
                                </div>
                            </a>
                            <a href="?resto=guest" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa-solid fa-users"></i></h1>
                                    <p>Guests</p>
                                </div>
                            </a>
                            <a href="?resto=block" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa-solid fa-layer-group"></i></h1>
                                    <p>Room Block</p>
                                </div>
                            </a>
                            <a href="?resto=room_class_bed_type" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-table"></i></h1>
                                    <p>Room class type</p>
                                </div>
                            </a>
                        </div>
                        <br>
                        <div class="row">
                        <a href="?resto=roomstatus" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-info-circle"></i></h1>
                                    <p>Room status</p>
                                </div>
                            </a>
                            <a href="?resto=bedtype" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-bed"></i></h1>
                                    <p>Bed type</p>
                                </div>
                            </a>
                            <a href="?resto=room_class_feature" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-star"></i></h1>
                                    <p>Room features</p>
                                </div>
                            </a>
                            <a href="?resto=venue_list" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa fa-location-dot"></i></h1>
                                    <p>Venue List</p>
                                </div>
                            </a>
                        </div>
                        <br>
                        
                        
                         <div class="row">
                        <a href="?resto=venueRates" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa-solid fa-tags"></i></h1>
                                    <p>Venue Rates</p>
                                </div>
                            </a>
                            <a href="?resto=eventType" class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="color-single nk-pink sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <h1><i class="fa-solid fa-champagne-glasses"></i></h1>
                                    <p>Event Type</p>
                                </div>
                            </a>
                           
                           
                        
                            
                        </div>
                     

                        </div>
                    </div>
                </div>
                
<!--CLOSING THE DAY-->       
        <form method="POST">
                 <br>
                <input type="checkbox" id="approve" name="approve" required>   I confirm all today's information is correct<br>
                <br>
               <?php
                $today = date('Y-m-d');
                ?>
                
                <input type="date" value="<?php echo $today; ?>" max="<?php echo $today; ?>" class="form-control" name="closedate" required>
                <br>
                <button type="submit" value="Close day" name="close" class="btn btn-info"> Close day </button>
        </form>
            </div>
        </div>
    </div>
    
    
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<!-- OPTIONAL SCRIPTS -->
<script>
    var ctx = document.getElementById('myChart').getContext('2d');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: '<?php echo $chartType?>',

    // The data for our dataset
    data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'Semptember', 'October', 'November', 'December'],
        datasets: [{
            label: 'Bookings',
            backgroundColor: 'white',
            borderColor:'blue',
            data: [<?php echo getBookingTotalByMonth(date('Y'),1)?>, 
            <?php echo getBookingTotalByMonth(date('Y'),2)?>
            , <?php echo getBookingTotalByMonth(date('Y'),3)?>, 
            <?php echo getBookingTotalByMonth(date('Y'),4)?>, 
            <?php echo getBookingTotalByMonth(date('Y'),5)?>
            , <?php echo getBookingTotalByMonth(date('Y'),6)?>, 
            <?php echo getBookingTotalByMonth(date('Y'),7)?>, 
            <?php echo getBookingTotalByMonth(date('Y'),8)?>,
            <?php echo getBookingTotalByMonth(date('Y'),9)?>,
            <?php echo getBookingTotalByMonth(date('Y'),10)?>,
            <?php echo getBookingTotalByMonth(date('Y'),11)?>,
            <?php echo getBookingTotalByMonth(date('Y'),12)?>]
        }]
    },

    // Configuration options go here
    options: {}
});
    </script>
    
    <script>
function myFunction() {
  var x = document.getElementById("mySelect").value;
  //alert(x);
  
      var ctx = document.getElementById('myChart').getContext('2d');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: x,

    // The data for our dataset
    data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'Semptember', 'October', 'November', 'December'],
        datasets: [{
            label: 'Bookings',
            backgroundColor: 'blue',
            borderColor:'blue',
            data: [<?php echo getBookingTotalByMonth(date('Y'),1)?>, 
            <?php echo getBookingTotalByMonth(date('Y'),2)?>
            , <?php echo getBookingTotalByMonth(date('Y'),3)?>, 
            <?php echo getBookingTotalByMonth(date('Y'),4)?>, 
            <?php echo getBookingTotalByMonth(date('Y'),5)?>
            , <?php echo getBookingTotalByMonth(date('Y'),6)?>, 
            <?php echo getBookingTotalByMonth(date('Y'),7)?>, 
            <?php echo getBookingTotalByMonth(date('Y'),8)?>,
            <?php echo getBookingTotalByMonth(date('Y'),9)?>,
            <?php echo getBookingTotalByMonth(date('Y'),10)?>,
            <?php echo getBookingTotalByMonth(date('Y'),11)?>,
            <?php echo getBookingTotalByMonth(date('Y'),12)?>]
        }]
    },

    // Configuration options go here
    options: {}
});
  
  
}
</script>
    