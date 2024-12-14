    <div class="col-lg-12">
      <h4><a href="?main"><i class="fa fa-arrow-left"></i> HOME</a> | RESIGNED EMPLOYEE</h4>
    </div>
    <div class="col-lg-12">
            <div class="content-panel">
              <div class="panel-heading">
              <h4><i class="fa fa-user"></i> EMPLOYEE LIST (NEWIND) <a href="?addresign&company=NEWIND" class="btn btn-primary" style="float:right;"><i class="fa fa-plus"></i> Add Employee</a></h4>
            </div>
              <div class="panel-body">
              <table class="table table-bordered table-striped table-condensed" >
                  <thead>
                    <tr>
                      <th width="3%">No.</th>
                      <th>Emp ID</th>
                      <th>Employee Name</th>
                      <th>Date of Birth</th>
                      <th>Job Title</th>
                      <th>Department</th>
                      <th>Date Hired</th> s
                      <th>Date Resigned</th>
                      <th>Remarks</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $x=1;
                    mysqli_query($con,"SET NAMES 'utf8'");
                    $sqlEmployee=mysqli_query($con,"SELECT ep.*,ed.*,r.* FROM employee_profile ep LEFT JOIN employee_details ed ON ed.idno=ep.idno LEFT JOIN resignation r ON r.idno=ep.idno WHERE ed.status LIKE '%RESIGNED%' AND ed.company='NEWIND'");
                      if(mysqli_num_rows($sqlEmployee)>0){
                        while($company=mysqli_fetch_array($sqlEmployee)){
                          if($company['status']=="REGULAR"){
                            $status="<span class='label label-success label-mini'>$company[status]</span>";
                          }else{
                            $status="<span class='label label-warning label-mini'>$company[status]</span>";
                          }
                          $shift=date('h:i A',strtotime($company['startshift']))." - ".date('h:i A',strtotime($company['endshift']));
                          $datehired=date('m/d/Y',strtotime($company['dateofhired']));
                          $sqlJobTitle=mysqli_query($con,"SELECT jobtitle FROM jobtitle WHERE id='$company[designation]'");
                          if(mysqli_num_rows($sqlJobTitle)>0){
                            $job=mysqli_fetch_array($sqlJobTitle);
                            $jobtitle=$job['jobtitle'];
                          }else{
                            $jobtitle="";
                          }
                          $sqlJobTitle=mysqli_query($con,"SELECT department FROM department WHERE id='$company[department]'");
                          if(mysqli_num_rows($sqlJobTitle)>0){
                            $job=mysqli_fetch_array($sqlJobTitle);
                            $department=$job['department'];
                          }else{
                            $department="";
                          }
                          $empname=$company['lastname'].", ".$company['firstname'];
                          echo "<tr>";
                            echo "<td>$x.</td>";
                            echo "<td>$company[idno]</td>";
                            echo "<td>$company[lastname], $company[firstname] $company[middlename] $company[suffix]</td>";
                            echo "<td>".date('M-d-Y',strtotime($company['birthdate']))."</td>";
                            echo "<td>$jobtitle</td>";
                            echo "<td>$department</td>";
                            echo "<td>$datehired</td>";
                            echo "<td>".date('m/d/Y',strtotime($company['dateresigned']))."</td>";
                            echo "<td align='center'>$company[reason]</td>";
                            echo "<td align='center'>
                            <a href='?editresignation&id=$company[id]&empname=$empname' title='Edit Details'><i class='fa fa-pencil fa-fw'></i></a>
                            </td>";
                          echo "</tr>";
                          $x++;
                        }
                      }else{
                        echo "<tr><td colspan='10   ' align='center'>No record found!</td></tr>";
                      }
                    ?>
                  </tbody>
                </table>
                </div>
            </div>
            </div>
            <div class="col-lg-12">
            <div class="content-panel">
              <div class="panel-heading">
              <h4><i class="fa fa-user"></i> EMPLOYEE LIST (SOLUTIONS) <a href="?addresign&company=NESI1" class="btn btn-primary" style="float:right;"><i class="fa fa-plus"></i> Add Employee</a></h4>
            </div>
              <div class="panel-body">
              <table class="table table-bordered table-striped table-condensed" >
                  <thead>
                    <tr>
                      <th width="3%">No.</th>
                      <th>Emp ID</th>
                      <th>Employee Name</th>
                      <th>Date of Birth</th>
                      <th>Job Title</th>
                      <th>Department</th>
                      <th>Date Hired</th>
                      <th>Date Resigned</th>
                      <th>Remarks</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $x=1;
                    mysqli_query($con,"SET NAMES 'utf8'");
                      $sqlEmployee=mysqli_query($con,"SELECT ep.*,ed.*,r.* FROM employee_profile ep LEFT JOIN employee_details ed ON ed.idno=ep.idno LEFT JOIN resignation r ON r.idno=ep.idno WHERE ed.status LIKE '%RESIGNED%' AND ed.company='NESI1'");
                      if(mysqli_num_rows($sqlEmployee)>0){
                        while($company=mysqli_fetch_array($sqlEmployee)){
                          if($company['status']=="REGULAR"){
                            $status="<span class='label label-success label-mini'>$company[status]</span>";
                          }else{
                            $status="<span class='label label-warning label-mini'>$company[status]</span>";
                          }
                          $shift=date('h:i A',strtotime($company['startshift']))." - ".date('h:i A',strtotime($company['endshift']));
                          $datehired=date('m/d/Y',strtotime($company['dateofhired']));
                          $sqlJobTitle=mysqli_query($con,"SELECT jobtitle FROM jobtitle WHERE id='$company[designation]'");
                          if(mysqli_num_rows($sqlJobTitle)>0){
                            $job=mysqli_fetch_array($sqlJobTitle);
                            $jobtitle=$job['jobtitle'];
                          }else{
                            $jobtitle="";
                          }
                          $sqlJobTitle=mysqli_query($con,"SELECT department FROM department WHERE id='$company[department]'");
                          if(mysqli_num_rows($sqlJobTitle)>0){
                            $job=mysqli_fetch_array($sqlJobTitle);
                            $department=$job['department'];
                          }else{
                            $department="";
                          }
                          $empname=$company['lastname'].", ".$company['firstname'];
                          echo "<tr>";
                            echo "<td>$x.</td>";
                            echo "<td>$company[idno]</td>";
                            echo "<td>$company[lastname], $company[firstname] $company[middlename] $company[suffix]</td>";
                            echo "<td>".date('M-d-Y',strtotime($company['birthdate']))."</td>";
                            echo "<td>$jobtitle</td>";
                            echo "<td>$department</td>";
                            echo "<td>$datehired</td>";
                            echo "<td>".date('m/d/Y',strtotime($company['dateresigned']))."</td>";
                            echo "<td align='center'>$company[reason]</td>";
                            echo "<td align='center'>
                            <a href='?editresignation&id=$company[id]&empname=$empname' title='Edit Details'><i class='fa fa-pencil fa-fw'></i></a>
                            </td>";
                          echo "</tr>";
                          $x++;
                        }
                      }else{
                        echo "<tr><td colspan='10   ' align='center'>No record found!</td></tr>";
                      }
                    ?>
                  </tbody>
                </table>
                </div>
            </div>
            </div>
            <div class="col-lg-12">
            <div class="content-panel">
              <div class="panel-heading">
              <h4><i class="fa fa-user"></i> EMPLOYEE LIST (STRATEGIES) <a href="?addresign&company=NESI2" class="btn btn-primary" style="float:right;"><i class="fa fa-plus"></i> Add Employee</a></h4>
            </div>
              <div class="panel-body">
              <table class="table table-bordered table-striped table-condensed" >
                  <thead>
                    <tr>
                      <th width="3%">No.</th>
                      <th>Emp ID</th>
                      <th>Employee Name</th>
                      <th>Date of Birth</th>
                      <th>Job Title</th>
                      <th>Department</th>
                      <th>Date Hired</th>
                      <th>Date Resigned</th>
                      <th>Remarks</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $x=1;
                    mysqli_query($con,"SET NAMES 'utf8'");
                    $sqlEmployee=mysqli_query($con,"SELECT ep.*,ed.*,r.* FROM employee_profile ep LEFT JOIN employee_details ed ON ed.idno=ep.idno LEFT JOIN resignation r ON r.idno=ep.idno WHERE ed.status LIKE '%RESIGNED%' AND ed.company='NESI2'");
                      if(mysqli_num_rows($sqlEmployee)>0){
                        while($company=mysqli_fetch_array($sqlEmployee)){
                          if($company['status']=="REGULAR"){
                            $status="<span class='label label-success label-mini'>$company[status]</span>";
                          }else{
                            $status="<span class='label label-warning label-mini'>$company[status]</span>";
                          }
                          $shift=date('h:i A',strtotime($company['startshift']))." - ".date('h:i A',strtotime($company['endshift']));
                          $datehired=date('m/d/Y',strtotime($company['dateofhired']));
                          $sqlJobTitle=mysqli_query($con,"SELECT jobtitle FROM jobtitle WHERE id='$company[designation]'");
                          if(mysqli_num_rows($sqlJobTitle)>0){
                            $job=mysqli_fetch_array($sqlJobTitle);
                            $jobtitle=$job['jobtitle'];
                          }else{
                            $jobtitle="";
                          }
                          $sqlJobTitle=mysqli_query($con,"SELECT department FROM department WHERE id='$company[department]'");
                          if(mysqli_num_rows($sqlJobTitle)>0){
                            $job=mysqli_fetch_array($sqlJobTitle);
                            $department=$job['department'];
                          }else{
                            $department="";
                          }
                          $empname=$company['lastname'].", ".$company['firstname'];
                          echo "<tr>";
                            echo "<td>$x.</td>";
                            echo "<td>$company[idno]</td>";
                            echo "<td>$company[lastname], $company[firstname] $company[middlename] $company[suffix]</td>";
                            echo "<td>".date('M-d-Y',strtotime($company['birthdate']))."</td>";
                            echo "<td>$jobtitle</td>";
                            echo "<td>$department</td>";
                            echo "<td>$datehired</td>";
                            echo "<td>".date('m/d/Y',strtotime($company['dateresigned']))."</td>";
                            echo "<td align='center'>$company[reason]</td>";
                            echo "<td align='center'>
                            <a href='?editresignation&id=$company[id]&empname=$empname' title='Edit Details'><i class='fa fa-pencil fa-fw'></i></a>
                            </td>";
                          echo "</tr>";
                          $x++;
                        }
                      }else{
                        echo "<tr><td colspan='10   ' align='center'>No record found!</td></tr>";
                      }
                    ?>
                  </tbody>
                </table>
                </div>
            </div>
            </div>
