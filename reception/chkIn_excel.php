<!-- Form Element area Start-->
    <div class="form-element-area">
        <div class="container">
            <div class="row">
                
                <?php if($msg){?>
                  <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Well Done!</strong> <?php echo htmlentities($msg); ?>
                  </div>
                <?php } 
                 else if($msge){?>
                     
                 <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                     <strong>Sorry!</strong> <?php echo htmlentities($msge); ?>
                  </div>
                <?php } ?>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-element-list">
                        <div class="tab-hd">
                            <h2><small><strong><i class="fa fa-refresh"></i> Checkin Report</strong></small></h2>
                        </div>
                     <hr>
                    <br>
                        <form action="" method="POST">
                          <div class="row">
                            <label class="col-md-2 control-label" for=""><strong> <small>Date From</small></strong><span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                    <input type="text" name="date_from" id="date_from" onfocus= (this.type='date') class="form-control input-sm" placeholder="Enter date from" required>
                            </div>
                            <label class="col-md-2 control-label" for=""><strong> <small>Date To</small></strong><span class="text-danger">*</span></label>
                            <div class="col-md-3">
                                    <input type="text" id="date_to" name="date_to" onfocus= (this.type='date') class="form-control input-sm" placeholder="Enter date to">
                            </div>
                            </div>
                        </form>
                        <br><br>
                        <div id="display_specs"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#date_to").change(function () {
            var date_from = $('#date_from').val();
            var date_to = $('#date_to').val();
			$(this).after('<div id="loader"><img src="../img/ajax_loader.gif" alt="loading...." width="30" height="30" /></div>');           
            $.get('load_chkin.php?ten=' + $(this).val() + '&date_from=' + date_from + '&date_to=' + date_to , function (data) {
             $("#display_specs").html(data);
                $('#loader').slideUp(910, function () {
                    $(this).remove();
                });
            });
        });

    });
</script>