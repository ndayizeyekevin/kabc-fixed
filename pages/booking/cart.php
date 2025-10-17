
<section>
 <div class="container">
    <div class="row">
        <div class="column">
            <?php include('temp/sidebar.php')?>
        </div>
	    <div class="col-sm-9 padding-right">
			<div class="features_items">	
    		  <h2 class="title text-center">Room and Rates</h2>
    		  
    		  <nav aria-label="breadcrumb">
			    <ol class="breadcrumb">
				    <li class="breadcrumb-item"><a href="#">Step 1: Select Dates</a></li>
				    <li class="breadcrumb-item">Step 2: Select Rooms</li>
				    <li class="breadcrumb-item" active aria-current="page" >Step 3: Booking Cart</li>
			    </ol>
			  </nav>
				<section id="cart_items">
        <div class="review-payment">
				<h2>Your Booking Cart</h2>
			</div>

			<div class="table-responsive cart_info">
            <form action="" method="POST">
				<table class="table table-condensed">
					<thead>
						<tr class="cart_menu">
						    <th>Room Image</th>
							<th>Room Type</th>
                            <th>Check In</th>
							<th>Check Out</th>
							<th>Nights</th>
							<th>Adults</th>
							<th>Chidren</th>
							<th>Price($)</th>
							<th>Price(FBu)</th>
                            <!--<th>Action</th>-->
						</tr>
					</thead>
					<tbody>
					    <tr>
					    <td><img src="../room_gallery/<?php echo $_SESSION['img_name']; ?>" alt="Ougami" style="width:70px;height:70px;"> </td>
					    <td><?php echo $_SESSION['type_name'];?> </td>
					    <td><?php echo $_SESSION['arrival'];?> </td>
					    <td><?php echo $_SESSION['departure'];?> </td>
					    <td><?php echo $_SESSION['nights'];?> </td>
					     <td><?php echo $_SESSION['Adults'];?> </td>
					    <td><?php echo $_SESSION['Children'];?> </td>
					    <td><?php echo number_format($_SESSION['price']);?> </td>
					    <td><?php echo number_format($_SESSION['price_fbu']);?> </td>
					    <!--<td><button class="btn btn-danger">Remove</button></td>-->
					    </tr>
					    <tr>
					        <td>Order Total In $:  <?php echo number_format($_SESSION['price']);?> </td>
					        <td colspan="7">Order Total In FBu: <?php echo number_format($_SESSION['price_fbu']);?> </td>
					        
					        <td><a href="?page=user_info" class="btn btn-primary">Continue Booking</a></td>
					    </tr>
					</tbody>
				</table>
</form>
			</div>
<section>
			
		 </div>
		</div>
	  </div>
	</div>
  </section>