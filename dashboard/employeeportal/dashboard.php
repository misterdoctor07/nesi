<?php
date_default_timezone_set("Asia/Manila");
?>
<?php
  session_start();
  include('../config.php');
  if(!isset($_SESSION['idno'])){
    echo "<script>window.location='../employeeportal/';</script>";
  }
    $sqlEmployee=mysqli_query($con,"SELECT lastname,firstname FROM employee_profile WHERE idno='$_SESSION[idno]'");
    if(mysqli_num_rows($sqlEmployee)>0){
      $name=mysqli_fetch_array($sqlEmployee);
      $fullname=$name['lastname'].", ".$name['firstname'];
    }else{
      $fullname="";
    }
    $sqlDetails=mysqli_query($con,"SELECT ed.*,jt.* FROM employee_details ed INNER JOIN department d ON d.id=ed.department INNER JOIN jobtitle jt ON jt.id=ed.designation WHERE ed.idno='$_SESSION[idno]'");
    if(mysqli_num_rows($sqlDetails)>0){
      $det=mysqli_fetch_array($sqlDetails);
      $jobtitle=$det['jobtitle'];
      $jobtitleID=$det['designation'];
      $department=$det['department'];
      $company=$det['company'];
    }else{
      $jobtitle="";
      $jobtitleID="";
      $department="";
      $company="";
    }
?>
        <?php
          $count=0;
          $sqlProtocol=mysqli_query($con,"SELECT approvingofficer FROM leave_protocols GROUP BY approvingofficer");
          if(mysqli_num_rows($sqlProtocol)>0){
            while($pro=mysqli_fetch_array($sqlProtocol)){
              if($jobtitleID==$pro['approvingofficer']){
                $count++;
              }
            }
          }
          if($count > 0){
            $view="";
          }else{
            $view="style='display:none;'";
          }
          ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
  <title>HRIS - North East Solutions Inc.</title>

  <!-- Favicons -->
  <!-- <link href="img/favicon.png" rel="icon">
  <link href="img/apple-touch-icon.png" rel="apple-touch-icon"> -->
  <link rel="icon" type="image/x-icon" href="img/nesi.jpg">

  <!-- Bootstrap core CSS -->
  <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!--external css-->
  <link href="lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="css/zabuto_calendar.css">
  <link rel="stylesheet" type="text/css" href="lib/gritter/css/jquery.gritter.css" />
  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet">
  <script src="lib/chart-master/Chart.js"></script>

  <!-- =======================================================
    Template Name: Dashio
    Template URL: https://templatemag.com/dashio-bootstrap-admin-template/
    Author: TemplateMag.com
    License: https://templatemag.com/license/
  ======================================================= -->
</head>

<body>
  <section id="container">
    <!-- **********************************************************************************************************************************************************
        TOP BAR CONTENT & NOTIFICATIONS
        *********************************************************************************************************************************************************** -->
    <!--header start-->
    <header class="header black-bg">
  <div class="sidebar-toggle-box">
    <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
  </div>
  <!--logo start-->
  <a href="/hris/employeeportal/dashboard.php?main" class="logo"><b>EMPLOYEE PORTAL</b></a>
  <!--logo end-->
  <div class="nav notify-row" id="top_menu">
    <!--  notification start -->
    <!--  notification end -->
  </div>
  <div class="top-menu">
    <ul class="nav pull-right top-menu">
      
      <li><a class="logout" href="logout.php" onclick="return confirm('Do you wish to logout?');return false;">Logout</a></li>
    </ul>
    <li style="float: right; margin-right: 40px; margin-top: 20px; "><a class="attendance_out" href="/hris/attendance/" style=" background-color:#7BCCB5; padding: 5px 15px; font-size: 1px; color: white; border: 1px solid #007bff; border-radius: 15px 15px; border-color: #7BCCB5;">Attendance</a></li>
  </div>
</header>
    <!--header end-->
    <!-- **********************************************************************************************************************************************************
        MAIN SIDEBAR MENU
        *********************************************************************************************************************************************************** -->
    <!--sidebar start-->
    <aside>
    <div id="sidebar" class="nav-collapse ">
    <!-- sidebar menu start-->
    <ul class="sidebar-menu" id="nav-accordion">
        <?php
        // Fetch user ID
        $userId = $_SESSION['idno'];
        
        // Check if the user has an uploaded profile picture
        $image = "path/to/default/image.jpg"; // Default image
        $target_dir = "../Employees/";

        // Check for profile picture in multiple formats
        if (file_exists($target_dir . $userId . ".png")) {
            $image = $target_dir . $userId . ".png";
        } elseif (file_exists($target_dir . $userId . ".jpg")) {
            $image = $target_dir . $userId . ".jpg";
        } elseif (file_exists($target_dir . $userId . ".jpeg")) {
            $image = $target_dir . $userId . ".jpeg";
        }
        ?>

        <!-- Display profile picture -->
        <p class="centered">
            <img src="<?= $image; ?>" alt="Profile Picture" class="img-circle" width="80" height="80">
        </p>
          <h5 class="centered"><?=$fullname;?></h5>
          <li class="mt">
            <a href="dashboard.php?main">
              <i class="fa fa-user"></i>
              <span>Profile</span>
              </a>
          </li>
          <li class="sub-menu">
            <a href="javascript:;">
              <i class=" fa fa-file-text-o"></i>
              <span>Leave Credits</span>
              </a>

              <?php
              $userId = $_SESSION['idno'];
              $query = "SELECT company FROM employee_details WHERE idno = '$userId'";
              $result = mysqli_query($con, $query);
              $row = mysqli_fetch_assoc($result);
              $company = $row['company'];
              
              // Count pending leave applications for the same company
              $query = "SELECT COUNT(*) AS total FROM leave_application la
                        INNER JOIN employee_details ed ON la.idno = ed.idno
                        WHERE la.appstatus = 'Pending' AND ed.company = '$company'";
              $result = mysqli_query($con, $query);
              $row = mysqli_fetch_assoc($result);
              $leave_count = $row['total'];
              
              // Count pending overtime applications for the same company
              $query = "SELECT COUNT(*) AS total FROM overtime_application oa
                        INNER JOIN employee_details ed ON oa.idno = ed.idno
                        WHERE oa.app_status = 'Pending' AND ed.company = '$company'";
              $result = mysqli_query($con, $query);
              $row = mysqli_fetch_assoc($result);
              $overtime_count = $row['total'];
              ?>
          
            <ul class="sub">
              <li><a href="dashboard.php?applyleave">Apply Leave</a></li>
              <li><a href="dashboard.php?manageleave">Manage Leave</a></li>
              <li <?=$view;?>><a href="dashboard.php?manageleaveapplication">Manage Leave Application 
                <span class="badge" style="color: red; background-color: white;">
                    <?php echo $leave_count; ?>
                </span></a></li>
              <li <?=$view;?>><a href="dashboard.php?manageovertimeapplication">Manage OT Application
                <span class="badge" style="color:red; background-color:white;">
                    <?php echo $overtime_count; ?>
                </span>
              </a></li>
            </ul>
          </li>
          <li>
            <a href="dashboard.php?viewpayroll">
              <i class="fa fa-credit-card"></i>
              <span>Payslip</span>
              </a>
          </li>
          <li>
            <a href="dashboard.php?infractions">
              <i class="fa fa-folder-open"></i>
              <span>Infractions</span>
              </a>
          </li>
          <li>
            <a href="dashboard.php?attendance">
              <i class="fa fa-clock-o"></i>
              <span>Attendance</span>
              </a>
          </li>
          <li>
            <a href="dashboard.php?applyovertime">
              <i class="fa fa-calendar"></i>
              <span>Overtime</span>
              </a>
          </li>
        </ul>
        <!-- sidebar menu end-->
      </div>
    </aside>
    <!--sidebar end-->
    <!-- **********************************************************************************************************************************************************
        MAIN CONTENT
        *********************************************************************************************************************************************************** -->
    <!--main content start-->
    <section id="main-content">
      <section class="wrapper site-min-height">
        <div class="row">
          <div class="col-lg-12">
          </div>
        </div>
        <div class="row mt">
          <?php
            if(isset($_GET['main'])){include('main.php');}
            if(isset($_GET['attendance'])){include('attendance.php');}
            if(isset($_GET['infractions'])){include('infractions.php');}
            if(isset($_GET['applyleave'])){include('applyleave.php');}
            if(isset($_GET['editleave'])){include('editleave.php');}
            if(isset($_GET['employeechecklist'])){include('employeechecklist.php');}
            if(isset($_GET['employeecontract'])){include('employeecontract.php');}
            if(isset($_GET['employeemovement'])){include('employeemovement.php');}
            if(isset($_GET['manageleave'])){include('manageleave.php');}
            if(isset($_GET['applyovertime'])){include('applyovertime.php');}
            if(isset($_GET['manageleaveapplication'])){include('manageleaveapplication.php');}
            if(isset($_GET['addovertime'])){include('addovertime.php');}
            if(isset($_GET['editovertime'])){include('editovertime.php');}
            if(isset($_GET['manageovertimeapplication'])){include('manageovertimeapplication.php');}
            if(isset($_GET['viewpayroll'])){include('viewpayroll.php');}

            if(isset($_GET['addinfraction'])){include('addinfraction.php');}
            if(isset($_GET['editinfraction'])){include('editinfraction.php');}
            if(isset($_GET['monitorinfraction'])){include('monitorinfraction.php');}
            if(isset($_GET['employeereferral'])){include('employeereferral.php');}
            if(isset($_GET['editoffense'])){include('editoffense.php');}
            if(isset($_GET['manageuser'])){include('manageuser.php');}

          ?>
          <!-- /col-lg-3 -->
        </div>
        <!-- /row -->
      </section>
    </section>
    <!--main content end-->

    <!--footer start-->
    <footer class="site-footer fixed">
      <div class="text-center">
        <p>
          &copy; Copyrights <strong>iHRIS</strong>. All Rights Reserved
        </p>
        <div class="credits">
          <!--
            You are NOT allowed to delete the credit link to TemplateMag with free version.
            You can delete the credit link only if you bought the pro version.
            Buy the pro version with working PHP/AJAX contact form: https://templatemag.com/dashio-bootstrap-admin-template/
            Licensing information: https://templatemag.com/license/
          -->
          Created with Dashio template by <a href="#">Eczekiel H. Aboy</a>
        </div>
        <a href="index.html#" class="go-top">
          <i class="fa fa-angle-up"></i>
          </a>
      </div>
    </footer>
    <!--footer end-->
  </section>
  <!-- js placed at the end of the document so the pages load faster -->
  <script src="lib/jquery/jquery.min.js"></script>

  <script src="lib/bootstrap/js/bootstrap.min.js"></script>
  <script class="include" type="text/javascript" src="lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="lib/jquery.scrollTo.min.js"></script>
  <script src="lib/jquery.nicescroll.js" type="text/javascript"></script>
  <script src="lib/jquery.sparkline.js"></script>
  <!--common script for all pages-->
  <script src="lib/common-scripts.js"></script>
  <script type="text/javascript" src="lib/gritter/js/jquery.gritter.js"></script>
  <script type="text/javascript" src="lib/gritter-conf.js"></script>
  <!--script for this page-->
  <script src="lib/sparkline-chart.js"></script>
  <script src="lib/zabuto_calendar.js"></script>
  <script type="text/javascript" language="javascript" src="lib/advanced-datatable/js/jquery.dataTables.js"></script>
  <script type="text/javascript" src="lib/advanced-datatable/js/DT_bootstrap.js"></script>
   <script type="text/javascript" src="lib/bootstrap-fileupload/bootstrap-fileupload.js"></script>
  <script type="text/javascript" src="lib/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
  <script type="text/javascript" src="lib/bootstrap-daterangepicker/date.js"></script>
  <script type="text/javascript" src="lib/bootstrap-daterangepicker/daterangepicker.js"></script>
  <script type="text/javascript" src="lib/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
  <script type="text/javascript" src="lib/bootstrap-daterangepicker/moment.min.js"></script>
  <script type="text/javascript" src="lib/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
  <script src="lib/advanced-form-components.js"></script>
  <script type="application/javascript">
    $(document).ready(function() {
      $("#date-popover").popover({
        html: true,
        trigger: "manual"
      });
      $("#date-popover").hide();
      $("#date-popover").click(function(e) {
        $(this).hide();
      });

      $("#my-calendar").zabuto_calendar({
        action: function() {
          return myDateFunction(this.id, false);
        },
        action_nav: function() {
          return myNavFunction(this.id);
        },
        ajax: {
          url: "show_data.php?action=1",
          modal: true
        },
        legend: [{
            type: "text",
            label: "Special event",
            badge: "00"
          },
          {
            type: "block",
            label: "Regular event",
          }
        ]
      });
    });

    $(document).ready(function() {
      var oTable = $('#hidden-table-info').dataTable({
        "aoColumnDefs": [{
          "bSortable":false,
          "aTargets": [0]
        }],
        "aaSorting": [
          [0, 'asc']
        ]
      });

      /* Add event listener for opening and closing details
       * Note that the indicator for showing which row is open is not controlled by DataTables,
       * rather it is done here
       */
      });


    function myNavFunction(id) {
      $("#date-popover").hide();
      var nav = $("#" + id).data("navigation");
      var to = $("#" + id).data("to");
      console.log('nav ' + nav + ' to: ' + to.month + '/' + to.year);
    }
  </script>
</body>

</html>
