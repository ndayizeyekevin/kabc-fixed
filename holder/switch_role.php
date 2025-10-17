<?php
if (isset($_GET['access_as'])) {
    // Store current admin session before switching
    if (!isset($_SESSION['real_admin_role'])) {
        $_SESSION['real_admin_role'] = $_SESSION['log_role'];
        $_SESSION['real_admin_name'] = $_SESSION['log_name'];
    }

    $role = $_GET['access_as'];

    // Switch to the selected role
    switch ($role) {
        case '3':
            $_SESSION['log_role'] = '3';
            $_SESSION['log_name'] = 'Receptionist';
            echo "<script>window.location='../room/index?resto=home'</script>";
            exit;
        case '4':
            $_SESSION['log_role'] = '4';
            $_SESSION['log_name'] = 'Kitchen';
            echo "<script>window.location='../kitchen/index?resto=home'</script>";
            exit;
        case '5':
            $_SESSION['log_role'] = '5';
            $_SESSION['log_name'] = 'Barman';
            echo "<script>window.location='../barman/index?resto=home'</script>";
            exit;
        case '7':
            $_SESSION['log_role'] = '7';
            $_SESSION['log_name'] = 'Store Keeper';
            echo "<script>window.location='../store/index?resto=home'</script>";
            exit;
        case '1':
            // Return to Admin
            if (isset($_SESSION['real_admin_role'])) {
                $_SESSION['log_role'] = $_SESSION['real_admin_role'];
                $_SESSION['log_name'] = $_SESSION['real_admin_name'];
                unset($_SESSION['real_admin_role'], $_SESSION['real_admin_name']);
            echo "<script>window.location='../inn/index?resto=home'</script>";
                exit;
            }
    }
}

if(isset($_POST['saveConfig'])){

    $stmt = $db->prepare("UPDATE `system_configuration` 
    SET 
    `system_name`='".$_POST['system_name']."',
     `Tin`='".$_POST['tin']."',
      `mrc`='".$_POST['mrc']."', 
     `receipt_msg`='".$_POST['receipt_msg']."',
      `version`='".$_POST['version']."',
      `ebm_version`='".$_POST['ebm_version']."',
      `printer`='".$_POST['printer']."'
    ");
    $stmt->execute();
    echo "<script>
            $(document).ready(function(){
            $('#modal').modal('show'); 
            });
            </script>";
}

$sqlseason = $db->prepare("SELECT * FROM system_configuration");
$sqlseason->execute();
$rows = $sqlseason->fetch();

$companyTin = $rows['Tin'];
$name = $rows['system_name'];
$mrc = $rows['mrc'];
$msg = $rows['receipt_msg'];
$version = $rows['version'];
$ebm_version = $rows['ebm_version'];
$printer = $rows['printer'];
