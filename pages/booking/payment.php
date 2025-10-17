<?php require_once ("../inc/config.php"); ?>
	<div style="padding: 10px;border: 1px solid lightgray;width: 700px;margin-left: 20%;">
   <table>
       <tr>
           <td>
               <?php echo $cpny_ID; ?><br>
               OUGAMI TEAM<br>
               BURUNDI - BUJUMBURA<br>
           </td>
           <td>
               <img src="https://www.ougami.com/img/logo/logo_square.png" alt="" style="width: 100px;height: 100px;">
           </td>
       </tr>
       <tr>
           <td></td>
       </tr>
       <tr>
           <td>
               <? echo  $_SESSION['fname']; ?><br>
               <? echo  $_SESSION['address']; ?><br>
               <? echo  $_SESSION['phone']; ?><br>
           </td>
       </tr>
       <tr>
           <td></td>
       </tr>
       <tr>
           <td>Date:  <?php echo $_SESSION['today']; ?></td>
       </tr>
       <tr>
           <td></td>
       </tr>
       <tr>
          <td>Dear <? echo  $_SESSION['fname']; ?></td>
       </tr>
       <tr>
           <td>
            Thank you for choosing <?php echo $_SESSION['cpny']; ?>. We pleased to confirm your reservation on date from <?php echo $_SESSION['today']; ?> upto <?php echo $_SESSION['today']; ?>
            Thank you again for your reservation.<br><br>
           </td>
       </tr>
       <tr>
           <td></td>
       </tr>
       <tr>
           <td>We remain at your disposal for any questions in case of need.<br><br></td>
       </tr>
       <tr>
           <td></td>
       </tr>
       <tr>
           <td>The <?php echo $_SESSION['cpny']; ?> team welcomes you!<br><br></td>
       </tr>
       <tr>
           <td>Due to the COVID-19 pandemic, all measures barrier have been put in place to avoid all risks of contamination.</td>
        </tr>
        
   </table>
   </div>