          <div class="col-lg-12">
          <div class="border-head">
              <h3><?=$_SESSION['access'];?> DASHBOARD</h3>
            </div>
            <div class="row mt">
              <!-- SERVER STATUS PANELS -->
              <div class="col-md-4 col-sm-2 mb">
                <div class="grey-panel pn donut-chart">
                  <div class="grey-header">
                    <h5>NEWIND</h5>
                  </div>
                  <div class="row mt"><a href="viewemployeemasterlist.php?company=NEWIND" target="_blank"><i class="fa fa-users fa-5x"></i></a></div>
                  <?php
                    $sqlEmployee=mysqli_query($con,"SELECT ep.idno FROM employee_profile ep LEFT JOIN employee_details ed ON ed.idno=ep.idno WHERE ed.company='NEWIND' AND ed.status NOT LIKE '%RESIGNED%'");
                    $regular=mysqli_num_rows($sqlEmployee);
                  ?>
                  <h3><?=$regular;?></h3>
                  Employees
                </div>
                <!-- /grey-panel -->
              </div>
              <!-- /col-md-4-->
              <div class="col-md-4 col-sm-2 mb">
                <div class="grey-panel pn donut-chart">
                  <div class="grey-header">
                    <h5>SOLUTIONS</h5>
                  </div>
                  <div class="row mt"><a href="viewemployeemasterlist.php?company=NESI1" target="_blank"><i class="fa fa-users fa-5x"></i></a></div>
                  <?php
                    $sqlEmployee=mysqli_query($con,"SELECT ep.idno FROM employee_profile ep LEFT JOIN employee_details ed ON ed.idno=ep.idno WHERE ed.company='NESI1' AND ed.status NOT LIKE '%RESIGNED%'");
                    $probationary=mysqli_num_rows($sqlEmployee);
                  ?>
                  <h3><?=$probationary;?></h3>
                  Employees
                </div>
                <!-- /grey-panel -->
              </div>
              <div class="col-md-4 col-sm-2 mb">
                <div class="grey-panel pn donut-chart">
                  <div class="grey-header">
                    <h5>STRATEGIES</h5>
                  </div>
                  <div class="row mt"><a href="viewemployeemasterlist.php?company=NESI2" target="_blank"><i class="fa fa-users fa-5x"></i></a></div>
                  <?php
                    $sqlEmployee=mysqli_query($con,"SELECT ep.idno FROM employee_profile ep LEFT JOIN employee_details ed ON ed.idno=ep.idno WHERE ed.company='NESI2' AND ed.status NOT LIKE '%RESIGNED%'");
                    $resigned=mysqli_num_rows($sqlEmployee);
                  ?>
                  <h3><?=$resigned;?></h3>
                  Employees
                </div>
                <!-- /grey-panel -->
              </div>              
              <!-- /col-md-4 -->
            </div>
          </div>
