<div class="container">	
 <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="form-example-wrap mg-t-30">
                <div class="tab-hd">
                            <h2><small><strong><i class="fa fa-refresh"></i> General Report</strong></small></h2>
                        </div>
                     <hr>
            <form action="" method="POST">
                 <div class="row">
                   <label class="col-md-2 control-label" for=""><strong>Select Date </strong><span class="text-danger">*</span></label>
                    <div class="col-md-3">
                       <input type="date" id="date_from" name="date_from" onchange=AjaxFunction(); class="form-control">
                    </div>
                   </div>
            </form>
            <br>
            <br>
            <div name='display_res' id='display_res'>
                          
            </div>
        </div>
    </div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#date_from").change(function () {
			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           
            $.get('load_general_report.php?date_from=' + $(this).val() , function (data) {
             $("#display_res").html(data);
                $('#loader').slideUp(910, function () {
                    $(this).remove();
                });
            });
        });

    });
</script>