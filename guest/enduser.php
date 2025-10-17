<div class="inbox-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                    <div class="inbox-left-sd">
						<div class="compose-ml">
                            <a class="btn" href="#">Filter By Category</a>
                        </div>
                        <div class="inbox-status">
                            <ul class="inbox-st-nav inbox-ft">
                                <?php
                                $sql = $conn->prepare("SELECT * FROM `tbl_company_category`");
                        		$sql->execute();
                        		while($fetch = $sql->fetch()){
                                ?>
                                <li><a href="#myId=<?php echo $fetch['category_ID']?>"><i class="notika-icon notika-mail"></i><?php echo $fetch['categ_name']?></a></li>
                                <?php
                        		}
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                    <div class="inbox-text-list sm-res-mg-t-30">
                        <div class="form-group">
                            <div class="nk-int-st search-input search-overt">
                                <input type="text" class="form-control" placeholder="Search email..." />
                                <button class="btn search-ib">Search</button>
                            </div>
                        </div>
                    <div class="animation-area">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 sm-res-mg-t-30" style="margin:10px;">
                                        <div class="animation-single-int">
                                            <div class="animation-ctn-hd">
                                                <h2>Attention Seekers</h2>
                                                <p>Click on the buttons below to start the animation action in image.</p>
                                            </div>
                                            <div class="animation-img mg-b-15">
                                                <img class="animate-one" src="https://picsum.photos/300/300.jpg" alt="" />
                                            </div>
                                            <div class="animation-action">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="animation-btn">
                                                            <button class="btn ant-nk-st bounce-ac">View More</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="margin:10px;">
                                        <div class="animation-single-int">
                                            <div class="animation-ctn-hd">
                                                <h2>Attention Seekers</h2>
                                                <p>Click on the buttons below to start the animation action in image.</p>
                                            </div>
                                            <div class="animation-img mg-b-15">
                                                <img class="animate-one" src="https://picsum.photos/300/300.jpg" alt="" />
                                            </div>
                                            <div class="animation-action">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="animation-btn">
                                                            <button class="btn ant-nk-st bounce-ac">View More</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="margin:10px;">
                                        <div class="animation-single-int">
                                            <div class="animation-ctn-hd">
                                                <h2>Attention Seekers</h2>
                                                <p>Click on the buttons below to start the animation action in image.</p>
                                            </div>
                                            <div class="animation-img mg-b-15">
                                                <img class="animate-one" src="https://picsum.photos/300/300.jpg" alt="" />
                                            </div>
                                            <div class="animation-action">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="animation-btn">
                                                            <button class="btn ant-nk-st bounce-ac">View More</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="margin:10px;">
                                        <div class="animation-single-int">
                                            <div class="animation-ctn-hd">
                                                <h2>Attention Seekers</h2>
                                                <p>Click on the buttons below to start the animation action in image.</p>
                                            </div>
                                            <div class="animation-img mg-b-15">
                                                <img class="animate-one" src="https://picsum.photos/300/300.jpg" alt="" />
                                            </div>
                                            <div class="animation-action">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="animation-btn">
                                                            <button class="btn ant-nk-st bounce-ac">View More</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="margin:10px;">
                                        <div class="animation-single-int">
                                            <div class="animation-ctn-hd">
                                                <h2>Attention Seekers</h2>
                                                <p>Click on the buttons below to start the animation action in image.</p>
                                            </div>
                                            <div class="animation-img mg-b-15">
                                                <img class="animate-one" src="https://picsum.photos/300/300.jpg" alt="" />
                                            </div>
                                            <div class="animation-action">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="animation-btn">
                                                            <button class="btn ant-nk-st bounce-ac">View More</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" style="margin:10px;">
                                        <div class="animation-single-int">
                                            <div class="animation-ctn-hd">
                                                <h2>Attention Seekers</h2>
                                                <p>Click on the buttons below to start the animation action in image.</p>
                                            </div>
                                            <div class="animation-img mg-b-15">
                                                <img class="animate-one" src="https://picsum.photos/300/300.jpg" alt="" />
                                            </div>
                                            <div class="animation-action">
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="animation-btn">
                                                            <button class="btn ant-nk-st bounce-ac">View More</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>