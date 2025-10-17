<style>
    /* Notification popup styles */
    #notification-popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px 50px;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        z-index: 10000;
        display: none;
        text-align: center;
        animation: popupBounce 0.5s ease-out;
    }

    #notification-popup.show {
        display: block;
    }

    #notification-popup h2 {
        margin: 0 0 15px 0;
        font-size: 28px;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }

    #notification-popup p {
        margin: 0;
        font-size: 18px;
        opacity: 0.95;
    }

    #notification-popup .icon {
        font-size: 50px;
        margin-bottom: 15px;
        animation: bellRing 0.5s ease-in-out infinite;
    }

    @keyframes popupBounce {
        0% {
            transform: translate(-50%, -50%) scale(0.5);
            opacity: 0;
        }
        50% {
            transform: translate(-50%, -50%) scale(1.1);
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
    }

    @keyframes bellRing {
        0%, 100% {
            transform: rotate(0deg);
        }
        25% {
            transform: rotate(-15deg);
        }
        75% {
            transform: rotate(15deg);
        }
    }

    /* Overlay background */
    #notification-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: none;
    }

    #notification-overlay.show {
        display: block;
    }
</style>

<!-- Notification Popup HTML -->
<div id="notification-overlay"></div>
<div id="notification-popup">
    <div class="icon">🔔</div>
    <h2>New Order Alert!</h2>
    <p id="notification-message">New orders have arrived in the kitchen</p>
</div>

<!-- Audio element for notification sound -->
<audio id="notification-sound" preload="auto">
    <source src="notify.ogg" type="audio/ogg">
    <source src="../kitchen/notify.ogg" type="audio/ogg">
</audio>

<script>
    // Wait for jQuery to be ready (it's loaded in template_styles.php)
    jQuery(document).ready(function($) {
        console.log('jQuery version:', $.fn.jquery);
        console.log('Kitchen notification system starting...');

        var notificationInterval; // Store interval ID for cleanup
        var isNotificationShowing = false; // Prevent overlapping notifications

        // Function to check for new orders
        function checkNewOrders() {
            console.log('Checking for new orders...');
            $.ajax({
                url: 'check_orders_ajax.php',
                type: 'GET',
                dataType: 'json',
                cache: false, // Prevent caching to ensure fresh data
                success: function(response) {
                    console.log('AJAX response:', response);
                    if (response.success && response.count > 0) {
                        // New order items detected
                        console.log('New orders detected:', response.count, 'items');
                        showNotification(response.count, response.orders);
                    } else {
                        console.log('No new orders found');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error checking for new orders:', error);
                    console.error('Response text:', xhr.responseText);
                    // Don't show error to user, just log it
                }
            });
        }

        // Function to show notification popup and play sound
        function showNotification(count, orders) {
            // Prevent overlapping notifications
            if (isNotificationShowing) {
                console.log('Notification already showing, skipping...');
                return;
            }

            isNotificationShowing = true;

            // Build detailed message with table numbers
            var message = '';
            if (count === 1) {
                message = 'New item added to Table ' + orders[0].table_no + '!';
            } else {
                // Group by table
                var tables = {};
                orders.forEach(function(order) {
                    if (!tables[order.table_no]) {
                        tables[order.table_no] = 0;
                    }
                    tables[order.table_no]++;
                });

                var tableList = Object.keys(tables).map(function(tableNo) {
                    return 'Table ' + tableNo + ' (' + tables[tableNo] + ' items)';
                }).join(', ');

                message = count + ' new items: ' + tableList;
            }

            $('#notification-message').text(message);

            // Show popup with overlay
            $('#notification-overlay').addClass('show');
            $('#notification-popup').addClass('show');

            // Play notification sound
            var sound = document.getElementById('notification-sound');
            if (sound) {
                // Reset sound to beginning in case it's already playing
                sound.currentTime = 0;
                sound.play().catch(function(error) {
                    console.log('Could not play sound:', error);
                    // Try to enable audio on user interaction
                    $(document).one('click', function() {
                        sound.play().catch(function(e) {
                            console.log('Still could not play sound:', e);
                        });
                    });
                });
            }

            // Hide notification after 3 seconds
            setTimeout(function() {
                $('#notification-overlay').removeClass('show');
                $('#notification-popup').removeClass('show');
                isNotificationShowing = false;
            }, 3000);
        }

        // Check immediately on page load
        console.log('Checking for orders immediately...');
        checkNewOrders();

        // Set up continuous polling every 3 seconds
        console.log('Setting up 3-second interval...');
        notificationInterval = setInterval(function() {
            checkNewOrders();
        }, 3000); // Check every 3 seconds

        // Clean up interval when page is about to unload
        $(window).on('beforeunload', function() {
            console.log('Page unloading, clearing interval...');
            if (notificationInterval) {
                clearInterval(notificationInterval);
            }
        });

        // Keep the 10-second page refresh functionality
        setTimeout(function() {
            console.log('10 seconds elapsed, reloading page...');
            // Clear the notification interval before reload
            if (notificationInterval) {
                clearInterval(notificationInterval);
            }
            window.location.reload();
        }, 10000);
    }); // End of jQuery ready
</script>
<!-- Data Table area Start-->
    <div class="data-table-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="data-table-list">
                    <div class="row">
                        <?php
                        $sql = $db->prepare("SELECT * FROM `tbl_cmd_qty`
                            INNER JOIN menu ON menu.menu_id = tbl_cmd_qty.cmd_item
                            INNER JOIN category ON menu.cat_id=category.cat_id
                            INNER JOIN tbl_tables ON tbl_cmd_qty.cmd_table_id = tbl_tables.table_id
                            WHERE tbl_cmd_qty.cmd_status = '13' AND category.cat_id != '2'
                            GROUP BY tbl_cmd_qty.cmd_table_id");
                        $sql->execute();
                        $rowcount = $sql->rowCount();

                        if($rowcount > 0){
                            $counter = 0;
                            while($fetch = $sql->fetch()){
                                $reservation_id = $fetch['cmd_table_id'];
                                $code = $fetch['cmd_code'];
                                $status_id = $fetch['cmd_status'];
                                $Serv_id = $fetch['Serv_id'];

                                $GetServ = $db->prepare("SELECT * FROM tbl_users WHERE user_id = '".$Serv_id."'");
                                $GetServ->execute();
                                $fServ = $GetServ->fetch();

                                $stmtss = $db->prepare("SELECT * FROM tbl_tables WHERE table_id = '".$reservation_id."'");
                                $stmtss->execute();
                                $rooms = $stmtss->fetch();
                                $room_no = $rooms['table_no'];

                                $badge = '<span class="badge" style="background-color: #dd5252; color: white; padding: 4px 8px; border-radius: 12px; font-size: 10px;">New</span>';
                                $service = $fServ['f_name']." ".$fServ['l_name'];

                                $counter++;
                        ?>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" style="margin-bottom: 20px;">
                                <a href="?resto=prcsOrder_list&m=<?php echo $reservation_id; ?>&c=<?php echo $code; ?>"
                                   onclick="return confirm('Do you really want to View this order?');"
                                   style="text-decoration: none; color: inherit;">

                                    <div class="menu-box" id="alert-<?php echo $counter; ?>"
                                         style="border: 1px solid #e0e0e0; border-radius: 8px; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); height: 180px; transition: all 0.3s ease;"
                                         onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.2)'; this.style.borderColor='#dd5252';"
                                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'; this.style.borderColor='#e0e0e0';">

                                        <!-- Order timestamp -->
                                        <div class="menu-box-head" style="background: #f8f9fa; padding: 10px 15px; border-bottom: 1px solid #e0e0e0; border-radius: 8px 8px 0 0;">
                                            <div style="text-align: left;">
                                                <small style="color: #666;"><?php echo date('H:i', strtotime($fetch['created_at'])); ?></small>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>

                                        <!-- Main content -->
                                        <div class="menu-box-content" style="padding: 20px; text-align: center; display: flex; flex-direction: column; justify-content: center; height: calc(100% - 45px);">
                                            <!-- Table number and badge -->
                                            <div style="margin-bottom: 10px;">
                                                <h4 style="margin-bottom: 5px; color: #333;">Table <?php echo $room_no; ?></h4>
                                                <?php echo $badge; ?>
                                            </div>

                                            <!-- Service info -->
                                            <div class="menu-box-foot">
                                                <h6><small style="color: #666;"><?php echo $service; ?></small></h6>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php
                            }
                        } else {
                            echo '<div class="col-12">
                                    <div class="alert alert-info" style="text-align: center; margin: 20px 0;">
                                        <strong>Info!</strong> No Orders Found!
                                    </div>
                                  </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
