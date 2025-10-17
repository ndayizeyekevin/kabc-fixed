<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

					<form class="form-inline">

					<!-- <input type="date" name="date"> -->
					<?php
					if(!isset($_GET['date'])){
					      ?>
					    					    <input type="text" id="myDatePicker" name="date" placeholder="filter by date here" class="form-control">

				<?php	}
foreach($_GET as $key => $val) {
					  
					    if('date' === $key){
					          ?>
					    <input type="text" id="myDatePicker" name="date" placeholder="filter by date here" class="form-control">
					   	<?php }else { ?>
					      <input type="hidden" name="<?=$key?>" value="<?=$val?>">
					   <?php }
					  
					}
					?>
					
						

					<button class="btn btn-primary">search</button>
					</form>
<?php 

$dateq = "SELECT DATE(opened_at) as date  FROM days  ORDER BY created_at DESC;";
        $stmt_date = $db->prepare($dateq);
        $stmt_date->execute();
        $row_date = $stmt_date->fetchAll(PDO::FETCH_ASSOC);

$dates = array_column($row_date, 'date');
$uniqueDates = array_unique($dates);

$uniqueDates = array_values($uniqueDates);

?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
const availableDates = <?= json_encode($uniqueDates) ?>;
  flatpickr("#myDatePicker", {
    enable: availableDates,
    dateFormat: "Y-m-d"
  });
</script>
