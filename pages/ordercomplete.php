<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Orders Display</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .data-table-area {
            margin-top: 20px;
        }
        .menu-box {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: 180px;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .menu-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            border-color: #dd5252;
        }
        .menu-box-head {
            background: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #e0e0e0;
            border-radius: 8px 8px 0 0;
        }
        .menu-box-content {
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: calc(100% - 45px);
        }
        .badge {
            background-color: #dd5252;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
        }
        #audioControl {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            padding: 10px 15px;
            background-color: #dd5252;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .alert-info {
            text-align: center;
            margin: 20px 0;
        }
        .sound-looping #audioControl {
            animation: pulse 1s infinite, glow 2s infinite alternate;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        @keyframes glow {
            from { box-shadow: 0 0 5px #dd5252; }
            to { box-shadow: 0 0 20px #dd5252; }
        }
        #permissionBanner {
            position: fixed;
            bottom: 70px;
            right: 20px;
            background: #ffc107;
            color: #212529;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
            display: none;
            max-width: 300px;
            text-align: center;
        }
        
    </style>
</head>
<body>
    <!-- Audio control elements -->
    <div id="permissionBanner">
        <strong>Sound Notifications Blocked</strong><br>
        Click anywhere on the page to enable sound alerts
    </div>
    <button id="audioControl">Stop Sound Notification</button>
    <div class="page-header" style="text-align:center; margin: 10px 0 10px;">
        <a href="index?page=login" class="btn btn-success btn-lg">Click Here to Login</a>
    </div>

    <!-- Data Table area Start-->
    <div class="data-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <div class="row">
                            <?php
                            try {
                                $checkNew = $db->prepare("SELECT q.*
                                    FROM tbl_cmd_qty q
                                    INNER JOIN tbl_cmd c ON q.cmd_code = c.OrderCode
                                    INNER JOIN menu m ON m.menu_id = q.cmd_item
                                    INNER JOIN category cat ON cat.cat_id = m.cat_id
                                    WHERE q.created_at > NOW() - INTERVAL 600000 SECOND
                                    AND c.status_id = '5'
                                    AND q.cmd_status = '5'
                                    AND cat.cat_id <> '2'
                                    ORDER BY q.created_at DESC LIMIT 1");
                                $checkNew->execute();
                                $newOrder = $checkNew->fetch();
                                
                                $sql = $db->prepare("SELECT DISTINCT
                                        c.OrderCode AS cmd_code,
                                        q.cmd_table_id,
                                        q.cmd_status,
                                        q.Serv_id,
                                        q.created_at
                                    FROM tbl_cmd c
                                    INNER JOIN tbl_cmd_qty q ON q.cmd_code = c.OrderCode
                                    INNER JOIN menu m ON m.menu_id = q.cmd_item
                                    INNER JOIN category cat ON cat.cat_id = m.cat_id
                                    WHERE c.status_id = 5 AND q.cmd_status = 5 AND cat.cat_id <> 2
                                    GROUP BY q.cmd_table_id
                                    ORDER BY q.created_at DESC");
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
                                        
                                        $badge = $newOrder ? '<span class="badge">New</span>' : '';
                                        $service = $fServ['f_name']." ".$fServ['l_name'];
                                        
                                        $counter++;
                            ?>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6" style="margin-bottom: 20px;">
                                <div style="text-decoration: none; color: inherit;">
                                   
                                    <div class="menu-box" id="alert-<?php echo $counter; ?>">
                                        <div class="menu-box-head">
                                            <div style="text-align: left;">
                                                <small style="color: #666;"><?php echo date('M j, H:i', strtotime($fetch['created_at'])); ?></small>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="menu-box-content">
                                            <div style="margin-bottom: 10px;">
                                                <h4 style="margin-bottom: 5px; color: #333;">Table <?php echo $room_no; ?></h4>
                                                <?php echo $badge; ?>
                                            </div>
                                            
                                            <div class="menu-box-foot">
                                                <h6><small style="color: #666;"><?php echo $service; ?></small></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                    }
                                } else {
                                    echo '<div class="col-12">
                                            <div class="alert alert-info">
                                                <strong>Info!</strong> No completed Orders Found!
                                            </div>
                                          </div>';
                                }
                            } catch (PDOException $e) {
                                error_log("Database error: " . $e->getMessage());
                                echo '<div class="col-12">
                                        <div class="alert alert-danger">
                                            <strong>Error!</strong> Database connection problem.
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
    <!-- Data Table area End-->

    

    <script>
        // Audio Notification System
        let audioContext;
        let soundBuffer;
        let activeSoundSource = null;
        let soundInterval = null;
        let isSoundEnabled = false;
        const permissionBanner = document.getElementById('permissionBanner');
        const stopButton = document.getElementById('audioControl');
        
        
        // Initialize audio system
        async function initAudioSystem() {
            try {
                // Create audio context (will be suspended until user interaction)
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
                
                // Load sound file
                const response = await fetch('../assets/audio/bar.wav');
                const arrayBuffer = await response.arrayBuffer();
                soundBuffer = await audioContext.decodeAudioData(arrayBuffer);
                
                return true;
            } catch (error) {
                console.error("Audio initialization failed:", error);
                return false;
            }
        }
        
        // Play sound in a loop
        function playNotificationLoop() {
            if (!soundBuffer || !isSoundEnabled) return;
            
            // Stop any currently playing sound
            if (activeSoundSource) {
                activeSoundSource.stop();
            }
            
            // Create and play new sound
            activeSoundSource = audioContext.createBufferSource();
            activeSoundSource.buffer = soundBuffer;
            activeSoundSource.connect(audioContext.destination);
            activeSoundSource.loop = true; // Enable looping
            activeSoundSource.start(0);
            
            // Update UI
            document.body.classList.add('sound-looping');
            stopButton.textContent = "Sound Playing (Click to Stop)";
            
            // Set up the stop handler
            stopButton.onclick = stopSound;
        }
        
        // Stop sound function
        function stopSound() {
            if (activeSoundSource) {
                activeSoundSource.stop();
                activeSoundSource = null;
            }
            document.body.classList.remove('sound-looping');
            stopButton.textContent = "Sound Stopped";
            
            // Reset button after 3 seconds
            setTimeout(() => {
                stopButton.textContent = "Enable Sound Notifications";
                stopButton.onclick = enableSound;
            }, 3000);
        }
        
        // Enable sound function
        function enableSound() {
            isSoundEnabled = true;
            playNotificationLoop();
        }
        
        // Show permission banner
        function showPermissionBanner() {
            permissionBanner.style.display = 'block';
            
            // Set up click handler to resume audio context
            const handleInteraction = () => {
                if (audioContext.state === 'suspended') {
                    audioContext.resume().then(() => {
                        permissionBanner.style.display = 'none';
                        isSoundEnabled = true;
                        playNotificationLoop();
                    });
                }
                
                // Remove this after first interaction
                document.removeEventListener('click', handleInteraction);
                document.removeEventListener('touchstart', handleInteraction);
            };
            
            document.addEventListener('click', handleInteraction);
            document.addEventListener('touchstart', handleInteraction);
        }
        
        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', async function() {
            // Initialize audio system
            await initAudioSystem();
            
            // Set up initial button state
            stopButton.textContent = "Enable Sound Notifications";
            stopButton.onclick = enableSound;
            
            // Play sound if new order exists
            <?php if($newOrder): ?>
            if (audioContext.state === 'suspended') {
                showPermissionBanner();
            } else {
                isSoundEnabled = true;
                playNotificationLoop();
            }
            <?php endif; ?>
            
            // Auto-refresh the page every 60 seconds
            setTimeout(() => window.location.reload(), 60000);
        });
    </script>
</body>
</html>
