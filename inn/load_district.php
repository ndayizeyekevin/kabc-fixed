<?php
require_once ("../inc/config.php");
// require_once ("../holder/template_scripts.php");
// require_once ("../holder/template_scripts.php");
  $province = $_REQUEST['province'];
?>
<label class="col-md-2 control-label" for=""><strong>District</strong></label>
    <div class="col-md-3">
        <select name="district" id="district" class="select2" data-live-search="true" style="width:100%" required>
                 <option value="">Select Province</option>
                    <?php
                        $stmt = $db->query("SELECT * FROM tbl_district WHERE province_id = '".$province."' ");
                        try {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                if($res_cntr==$row['district_id'])
                                {
                                ?>
                                <option value="<?php echo $res_cntr; ?>" selected><?php echo $row['district_name']; ?></option>

                                <?php
                            }
                            else
                            {
                                ?>
                                <option value="<?php echo $row['district_id']; ?>"><?php echo $row['district_name']; ?></option>
                                <?php
                            }
                            }
                        }
                        catch (PDOException $ex) {
                            //Something went wrong rollback!
                            echo $ex->getMessage();
                        }
                    ?>   
            </select>
                      
                  </div>
                  </div>
                  <div class="form-group">
                  
                  <div class="row">
                      
                    <div class="col-md-3">
                        
										</div>
										</div>
										
                                        <div class="form-group">
        							    <div class="form-actions col-md-12">
        							        <br />
        							        <center>								
        								        <button type="submit" name="add" id="" class="btn btn-sm label-info margin" style="border-radius: 4px;"><i class="fa fa-fw fa-save"></i> Save</button>
        								        <button type="reset" class="btn btn-sm label-default margin"><i class="fa fa-fw fa-remove"></i> Reset</button>								
        							        </center>
        							    </div>
                                    </div>