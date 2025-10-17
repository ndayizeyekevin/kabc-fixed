<?php 
 ob_start();
 require_once "../inc/config.php";
    
    if(!empty($_POST["remember"])) {
	setcookie ("usn",$_POST["usn"],time()+ 3600);
	setcookie ("pwd",$_POST["pwd"],time()+ 3600);
    //  	echo "Cookies Set Successfuly";
    } else {
    	setcookie("usn","");
    	setcookie("pwd","");
    // 	echo "Cookies Not Set";
    }
    
    if (isset($_REQUEST['usn']) and isset($_REQUEST['pwd']))
    {
        $sql = $db->prepare("SELECT * FROM tbl_user_log WHERE usn='" . $_REQUEST['usn'] . "' AND pwd=ENCODE('" . htmlspecialchars($_REQUEST['pwd'], ENT_QUOTES) . "','ITEC_Team') ");
        $sql->execute();
        if ($sql->rowCount() != 0) {
            $res = $sql->fetch();
            $_SESSION['f_name'] = $res['f_name'];
            $_SESSION['l_name'] = $res['l_name'];
            $_SESSION['u_id'] = $res['u_id'];
            $_SESSION['usn'] = $res['usn'];
            $_SESSION['email'] = $res['email'];
            $_SESSION['log_role'] = $res['log_role'];
            $_SESSION['user_id'] = $res['user_id'];
            $_SESSION['access'] = true;
            $today = date("Y-m-d H:i:s");
    
            $login_time = $db->prepare("UPDATE tbl_user_log SET last_logged_in=? WHERE u_id=?");
            try {
                // time logged
                $login_time->execute(array($today, $_SESSION['u_id']));
            } catch (PDOException $ex) {
                //Something went wrong rollback!
                echo $ex->getMessage();
            }
            
             if($_SESSION['log_role']==0) //SuperAdmin
				{
			       header('Location:../team/index');
				}
				
				 elseif($_SESSION['log_role']==1) //Admin
				{
			       header('Location:../OnkeyManager/index');
				}
				
				
			elseif($_SESSION['log_role']==2) //Finance
				{
			       header('Location:../finance/index');
				}
				
			elseif($_SESSION['log_role']==3) //Room  Manager
				{
			       header('Location:../room/index');
				}
				
			elseif($_SESSION['log_role']==4) //Kitchen Manager
				{
			       header('Location:../kitchen/index');
				}
				
			elseif($_SESSION['log_role']==5) //Barman
				{
			       header('Location:../barman/index');
				}
				
			elseif($_SESSION['log_role']==6) //Services
				{
			       header('Location:../services/index');
				}
				
			elseif($_SESSION['log_role']==7) //Reception
				{
			       header('Location:../reception/index');
				}
			elseif($_SESSION['log_role']==11) 
				{
			       header('Location:../controller/index');
				}
				
			elseif($_SESSION['log_role']==8) //Client
				{
			       header('Location:../guest/index');
				}
				
			elseif($_SESSION['log_role']==9) //Managing Director
				{
			       header('Location:../md/index');
				}	
            
        } else {
    
            $msg = "Incorrect credentials!";
            header('Location:logging?msg=' . $msg);
        }
    } 
   
 ob_end_flush();
?>
