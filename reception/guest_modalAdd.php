<div class="modal fade" id="myModalone" role="dialog">
        <div class="modal-dialog modals-default">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="" enctype="multipart/form-data" method="POST">
                <div class="modal-body">
                    <h2>Add Guest</h2>
                    
                    <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <input type="text" class="form-control" name="fname" placeholder="First Name" required>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                               <input type="text" name="fam_name" id="fam_name" class="form-control" placeholder="Last Name" required>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <input type="number" name="phone" id="phone" class="form-control" placeholder="Phone Number" required>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                              <input  type="text" onfocus=(this.type='email') name="email" id="email" class="form-control" placeholder="Email" required>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                               <input  type="text" name="nid" id="nid" class="form-control" placeholder="ID/Passport" required>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                               <input  type="text" name="address" id="address" class="form-control" placeholder="Address">
                            </div>
                            </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                        <select id="country" name="country" class="form-control chosen" data-live-search="true" style="width: 100%;" required placeholder="Select Country" required>
                                         <option></option>
                                             <?php
                                            $stmt = $db->query('SELECT * FROM tbl_country');
                                            try {
                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                   
                                                    ?>
                                                    <option value="<?php echo $row['cntr_id']; ?>"><?php echo $row['cntr_name']; ?></option>
                                                    <?php
                                                }
                                               
                                            }
                                            catch (PDOException $ex) {
                                                //Something went wrong rollback!
                                                echo $ex->getMessage();
                                            }
                                            ?>   
                                        </select>
                                    
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                               <input type="text" name="city" id="city" class="form-control" placeholder="City">
                            </div>
                            </div>
                        </div>
                   </div>
                <div class="modal-footer">
                    <button type="submit" name="register" class="btn btn-info btn-sm">Save </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>