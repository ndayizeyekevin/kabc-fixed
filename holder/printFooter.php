<div id="printFooter"> <h4> Printed By: <?php echo $_SESSION['f_name']." ". $_SESSION['l_name']?></h4>
 <h6> Printed on: <?php echo date('Y-m-d h:i:s')?></h6>
</div>
 
 
 <script> function printInvoice() { 
$("#headerprint").show();
$("#printFooter").show();
//$('#data-table-basic').removeAttr('id');
var printContents = document.getElementById('content').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; window.print(); document.body.innerHTML = originalContents; } </script>

 
 
 
 