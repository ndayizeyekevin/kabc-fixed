  <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300" rel="stylesheet">
        <script src="../js/vendor/jquery-1.12.4.min.js"></script>
          <script src="../js/bootstrap.min.js"></script>
    <!-- wow JS
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
/>
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="../css/select2/css/select2.min.css">
    <!-- font awesome CSS
		============================================ -->
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <!-- owl.carousel CSS
		============================================ -->
    <link rel="stylesheet" href="../css/owl.carousel.css">
    <link rel="stylesheet" href="../css/owl.theme.css">
    <link rel="stylesheet" href="../css/owl.transitions.css">
      <!-- meanmenu CSS
		============================================ -->
    <link rel="stylesheet" href="../css/meanmenu/meanmenu.min.css">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="../css/animate.css">
    <!-- summernote CSS
		============================================ -->
    <link rel="stylesheet" href="../css/summernote/summernote.css">
    <!-- Range Slider CSS
		============================================ -->
    <link rel="stylesheet" href="../css/themesaller-forms.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="../css/normalize.css">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="../css/scrollbar/jquery.mCustomScrollbar.min.css">
    <!-- wave CSS
		============================================ -->
    <link rel="stylesheet" href="../css/wave/waves.min.css">
    <!-- Notika icon CSS
		============================================ -->
    <link rel="stylesheet" href="../css/notika-custom-icon.css">
     <!-- bootstrap select CSS
		============================================ -->
    <link rel="stylesheet" href="../css/bootstrap-select/bootstrap-select.css">
    <!-- datapicker CSS
		============================================ -->
    <link rel="stylesheet" href="../css/datapicker/datepicker3.css">
    <!-- Color Picker CSS
		============================================ -->
    <link rel="stylesheet" href="../css/color-picker/farbtastic.css">
    <!-- Data Table JS
		============================================ -->
    <!--<link rel="stylesheet" href="../css/jquery.dataTables.min.css">-->
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="../css/chosen/chosen.css">
    <!-- notification CSS
		============================================ -->
    <link rel="stylesheet" href="../css/notification/notification.css">
    <!-- dropzone CSS
		============================================ -->
    <link rel="stylesheet" href="../css/dropzone/dropzone.css">
    <!-- wave CSS
		============================================ -->
    <link rel="stylesheet" href="../css/wave/waves.min.css">
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/cropper/cropper.min.css">
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="../style.css">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="../css/responsive.css">
    <!-- modernizr JS
		============================================ -->
    <script src="../js/vendor/modernizr-2.8.3.min.js"></script>
    
    
      <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../../../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../../../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../../../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="../../../assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="../../../assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../../../assets/js/config.js"></script>
    
    
 <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

  <link
    rel="stylesheet"
    href="../assets/vendor/css/core.css"
    class="template-customizer-core-css" />
  <link
    rel="stylesheet"
    href="../assets/vendor/css/theme-default.css"
    class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />
  <link rel="stylesheet" href="../assets/css/custom.css" />

  <!-- Vendors CSS -->
  <link
    rel="stylesheet"
    href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <link
    rel="stylesheet"
    href="../assets/vendor/libs/apex-charts/apex-charts.css" />


  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!--<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">-->

  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <script src="../assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>

<script>
function deleteFunction(id,name,key) {

    
    $.ajax({
  url: "../inc/deletefunction.php",
  type: "get", //send it through get method
  data: { 
    id: id, 
    name: name, 
    key: key
  },
  success: function(response) {
      if(response==1){
      alert('Deleted');
   location.reload();
  }
  },
  error: function(xhr) {
    //Do Something to handle error
  }
});
    
}   </script>






<!-- <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> -->
<!--<script src="../assets/js/jquery.js"></script>-->

<script src="../assets/vendor/libs/popper/popper.js"></script>
<script src="../assets/vendor/js/bootstrap.js"></script>
<script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="../assets/vendor/js/menu.js"></script>

<script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

<script src="../assets/js/main.js"></script>

<script src="../assets/js/dashboards-analytics.js"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>-->

<script>


  document.addEventListener('DOMContentLoaded', function() {
    // Get the current URL path
    const currentPath = window.location.pathname;

    // Function to normalize the path
    const normalizePath = (path) => {
      const link = document.createElement('a');
      link.href = path;
      return link.pathname;
    };

    // Get all menu items
    const menuItems = document.querySelectorAll('.menu-item');

    // Loop through menu items to find matches
    menuItems.forEach(menuItem => {
      const menuLink = menuItem.querySelector('.menu-link');

      if (menuLink) {
        const normalizedHref = normalizePath(menuLink.getAttribute('href'));

        // Check if the link matches the current path
        if (normalizedHref === currentPath) {
          // Add active class to the child
          menuItem.classList.add('active');

          // Add open and active class to the parent if applicable
          let parentMenuItem = menuItem.closest('.menu-sub').closest('.menu-item');
          if (parentMenuItem) {
            parentMenuItem.classList.add('active', 'open');
          }
        }
      }
    });
  });
</script>
    
<style>
  .menu-box {
    border: 1px solid #c9c9c9;
    border-radius: 3px;
    margin-top: 10px;
    margin-bottom: 20px;
    background: #fafafa url(../img/cream.png) repeat;
}

.menu-box .menu-box-head, .modal-header {
    border-top-right-radius: 3px;
    border-top-left-radius: 3px;
    text-shadow: rgb(255, 255, 255) 0px 1px;
    color: rgb(102, 102, 102);
    font-size: 13px;
    font-weight: bold;
    background: -webkit-linear-gradient(top, rgb(248, 248, 248), rgb(242, 242, 242));
    border-top: 1px solid rgb(255, 255, 255);
    padding: 8px 15px;
}


.menu-box .menu-box-foot, .modal-footer{
  background-color: #f8f8f8;
  background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f2f2f2));
  background: -webkit-linear-gradient(top, #f8f8f8, #f2f2f2);
  background: -moz-linear-gradient(top, #f8f8f8, #f2f2f2);
  background: -ms-linear-gradient(top, #f8f8f8, #f2f2f2);
  background: -o-linear-gradient(top, #f8f8f8, #f2f2f2);
  background: linear-gradient(top, #f8f8f8, #f2f2f2);	
  border-bottom-right-radius: 3px;
  border-bottom-left-radius: 3px;
  text-shadow:0px 1px #fff;
  border-bottom: 1px solid #fff;
  border-top: 1px solid #ccc;
  padding: 8px 15px;
  font-size: 12px;
  color: #555;
  box-shadow: inset 0px 1px 1px #fff;
}

.budge{
    left: 130px;
}
.budge-spinner{
    left: 125px;
}

.scrn-budge{
    left: 85px;
}
.scrn-budge-spinner{
    left: 80px;
}
    @media screen and (max-width: 600px) {
      .btn-check{     
        margin-top: 20px; 
      }
    }
    
    @media screen and (max-width: 400px) {
         .btn-check{     
        margin-top: 20px; 
      }
    }
     @media screen and (max-width: 360px) {
          .btn-check{     
        margin-top: 20px; 
      }
       }
        
        @media screen and (max-width: 400px) {
             .btn-check{     
        margin-top: 20px; 
      }
     }
        
        @media screen and (max-width: 415px) {
             .btn-check{     
        margin-top: 20px; 
      }
    }
        
        @media screen and (max-width: 565px) {
             .btn-check{     
        margin-top: 20px; 
      }
     }
</style>

</head>

<body>
   