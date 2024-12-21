<?php
$comp=$_GET['company'];
$startdate=$_GET['startdate'];
$enddate=$_GET['enddate'];
$dept=$_GET['departments'];
?>
        <div class="col-lg-12">
            <div class="content-panel">
              <div class="panel-heading">
              <h4><a href="?monitorattendance"><i class="fa fa-arrow-left"></i> HOME</a> | <i class="fa fa-user"></i> EMPLOYEE LIST (<?=$comp?>,<?=$dept?>) <button onclick="tableToExcel('printThis','Detailed_Report')" class="btn btn-success" style="float:right;"><i class="fa fa-download"> </i> EXPORT</button></h4>
            </div>
              <div class="panel-body" id="printThis">
                <b>Company: <?=$comp;?><br />
                <b>Department: <?=$dept;?><br />
                Date Range: <?=date('m/d/Y',strtotime($startdate));?> - <?=date('m/d/Y',strtotime($enddate));?></b>
                <table class="table table-bordered table-striped table-condensed">
                  <thead>
                    <tr>
                      <th width="3%" rowspan="2" style="vertical-align:middle;">No.</th>
                      <th  rowspan="2" style="vertical-align:middle;">Emp ID</th>
                      <th  rowspan="2" style="vertical-align:middle;">Employee Name</th>
                      <th  rowspan="2" style="vertical-align:middle;">Department</th>
                      <th  rowspan="2" style="vertical-align:middle;">Shift</th>
                      <th  rowspan="2" style="vertical-align:middle;">Work Area</th>
                      <th  rowspan="2" style="vertical-align:middle;">Date</th>
                      <th colspan="2" align="center">Shift 1</th>
                      <th colspan="2" align="center">Shift 2</th>
                      <th  rowspan="2" style="vertical-align:middle;">Action</th>
                        <th  rowspan="2" style="vertical-align:middle;">Add Time</th>
                    </tr>
                    <tr>
                        <th align="center">Login</th>
                        <th align="center">Lunch out</th>
                        <th align="center">Lunch in</th>
                        <th align="center">Logout</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $x=1;
                    mysqli_query($con,"SET NAMES 'utf8'");
                     // Add the WHERE clause based on the selected fields
if (!empty($comp) && !empty($dept)) {
  $sqlEmployee = mysqli_query($con, "SELECT ep.*, ed.* FROM employee_profile ep 
    LEFT JOIN employee_details ed ON ed.idno = ep.idno 
    WHERE ed.status NOT LIKE '%RESIGNED%' AND company = '$comp' AND department = '$dept'
    ORDER BY ep.lastname ASC");
} elseif (!empty($comp)) {
  $sqlEmployee = mysqli_query($con, "SELECT ep.*, ed.* FROM employee_profile ep 
    LEFT JOIN employee_details ed ON ed.idno = ep.idno 
    WHERE ed.status NOT LIKE '%RESIGNED%' AND company = '$comp'
    ORDER BY ep.lastname ASC");
} elseif (!empty($dept)) {
  $sqlEmployee = mysqli_query($con, "SELECT ep.*, ed.* FROM employee_profile ep 
    LEFT JOIN employee_details ed ON ed.idno = ep.idno 
    WHERE ed.status NOT LIKE '%RESIGNED%' AND department = '$dept'
    ORDER BY ep.lastname ASC");
} else {
  $sqlEmployee = mysqli_query($con, "SELECT ep.*, ed.* FROM employee_profile ep 
    LEFT JOIN employee_details ed ON ed.idno = ep.idno 
    WHERE ed.status NOT LIKE '%RESIGNED%'
    ORDER BY ep.lastname ASC");
}
                      if(mysqli_num_rows($sqlEmployee)>0){
                        while($company=mysqli_fetch_array($sqlEmployee)){
                          $idn=$company['idno'];
                          if($company['status']=="REGULAR"){
                            $status="<span class='label label-success label-mini'>$company[status]</span>";
                          }else{
                            $status="<span class='label label-warning label-mini'>$company[status]</span>";
                          }
                          $sqlDepartment=mysqli_query($con,"SELECT * FROM department WHERE id='$company[department]'");
                          if(mysqli_num_rows($sqlDepartment)>0){
                            $dept=mysqli_fetch_array($sqlDepartment);
                            $department=$dept['department'];
                          }else{
                            $department="";
                          }
                          
                          $shift=date('h:i A',strtotime($company['startshift']))." - ".date('h:i A',strtotime($company['endshift']));
                          $datehired=date('m/d/Y',strtotime($company['dateofhired']));
                          $sqlAttendance=mysqli_query($con,"SELECT * FROM attendance WHERE logindate BETWEEN '$startdate' AND '$enddate' AND idno='$company[idno]' ORDER BY logindate ASC");
                                $login1="";
                                $logout1="";
                                $login2="";
                                $logout2="";
                                $datearray="";
                                $action="";
                                $removepoint="";
                            if(mysqli_num_rows($sqlAttendance)>0){
                                while($attend=mysqli_fetch_array($sqlAttendance)){
                                  $idno=$company['idno'];
                                  $datearray .=date('m/d/Y',strtotime($attend['logindate']))."<br>";
                                  $shiftfrom=$company['startshift'];
                                  if($attend['loginam'] > $shiftfrom || $attend['loginam']=="00:00:00"){
                                    $color="style='color:red;'";
                                  }else{
                                    $color="";
                                  }
                                  if($attend['loginpm']=="00:00:00" || $attend['logoutpm']=="00:00:00" || $attend['logoutam']=="00:00:00"){
                                    $color1="style='color:red;'";
                                  }else{
                                    $color1="";
                                  }

                                  $login1 .="<font $color>".$attend['loginam']."</font><br>";
                                  $logout1 .="<font $color1>".$attend['logoutam']."</font><br>";
                                  $login2 .="<font $color1>".$attend['loginpm']."</font><br>";
                                  $logout2 .="<font $color1>".$attend['logoutpm']."</font><br>";
                                  $sqlPoints=mysqli_query($con,"SELECT * FROM points WHERE idno='$idno' AND logindate='$attend[logindate]'");
                                  if(mysqli_num_rows($sqlPoints)>0){
                                    $point=mysqli_fetch_array($sqlPoints);
                                    $points=$point['points'];
                                    $point_id=$point['id'];
                                  }else{
                                    $points=0;
                                    $point_id="";
                                  }
                                  if($point_id <> ''){
                                  $removepoint="| <a href='?attendancemonitoring&idno=$idno&id=$point_id&deleteinfraction&company=$comp&startdate=$startdate&enddate=$enddate&logindate=$attend[logindate]' title='Delete Time'><i class='fa fa-trash'></i> Remove Infraction</a>";

                                }else{
                                  $removepoint="";
                                }
                                  $action .="<a href='?attendancemonitoringsummary&edit&company=$comp&startdate=$startdate&enddate=$enddate&idno=$idno&logindate=$attend[logindate]'><i class='fa fa-edit fa-fw'></i> Infraction</a> | <a href='?edittime&idno=$idno&id=$attend[id]&company=$comp&startdate=$startdate&enddate=$enddate' title='Edit Time'><i class='fa fa-edit'></i> Time</a> | <a href='?attendancemonitoring&idno=$idno&id=$attend[id]&deletetime&company=$comp&startdate=$startdate&enddate=$enddate&logindate=$attend[logindate]' title='Delete Time'><i class='fa fa-trash'></i> Delete Time</a> $removepoint"."<br>";
                                }
                            }else{
                                $login1="";
                                $logout1="";
                                $login2="";
                                $logout2="";
                                $datearray="";
                                $action="";
                            }
                            $idno=$company['idno'];
                          echo "<tr>";
                            echo "<td>$x.</td>";
                            echo "<td>$company[idno]</td>";
                            echo "<td>$company[lastname], $company[firstname] $company[middlename] $company[suffix]</td>";
                            echo "<td>$department</td>";
                            echo "<td>$shift</td>";
                            echo "<td align='center'>$company[location]</td>";
                            echo "<td align='center'>$datearray</td>";
                            echo "<td align='center'>$login1</td>";
                            echo "<td align='center'>$logout1</td>";
                            echo "<td align='center'>$login2</td>";
                            echo "<td align='center'>$logout2</td>";
                            echo "<td align='left' width='30%'>$action</td>";
                            echo "<td align='left'><a href='?edittime&idno=$idno&id=&company=$comp&startdate=$startdate&enddate=$enddate&logindate' title='Add Time'><i class='fa fa-edit'></i> Add Time</a></td>";
                          echo "</tr>";
                          $x++;
                        }
                      }else{
                        echo "<tr><td colspan='9' align='center'>No record found!</td></tr>";
                      }
                    ?>
                  </tbody>
                </table>
                </div>
            </div>
            </div>
            <?php
            if(isset($_GET['deletetime'])){
              $idno=$_GET['idno'];
              $id=$_GET['id'];
              $company=$_GET['company'];
              $startdate=$_GET['startdate'];
              $enddate=$_GET['enddate'];
              $logindate=$_GET['logindate'];
              $sqlDelete=mysqli_query($con,"DELETE FROM attendance WHERE id='$id'");
              if($sqlDelete){
                $delete=mysqli_query($con,"DELETE FROM points WHERE idno='$idno' AND logindate='$logindate'");
              echo "<script>";
                echo "alert('Item successfully removed!');window.location='?attendancemonitoring&company=$company&startdate=$startdate&enddate=$enddate';";
              echo "</script>";
            }else{
              echo "<script>";
                echo "alert('Unable to delete time!');window.location='?attendancemonitoring&company=$company&startdate=$startdate&enddate=$enddate';";
              echo "</script>";
              }
            }
            if(isset($_GET['deleteinfraction'])){
              $idno=$_GET['idno'];
              $id=$_GET['id'];
              $company=$_GET['company'];
              $startdate=$_GET['startdate'];
              $enddate=$_GET['enddate'];
              $logindate=$_GET['logindate'];
              $sqlDelete=mysqli_query($con,"DELETE FROM points WHERE id='$id'");
              if($sqlDelete){
                $sqlUpdate=mysqli_query($con,"UPDATE attendance SET remarks='P' WHERE logindate='$logindate' AND idno='$idno'");
              echo "<script>";
                echo "alert('Infraction successfully removed!');window.location='?attendancemonitoring&company=$company&startdate=$startdate&enddate=$enddate';";
              echo "</script>";
            }else{
              echo "<script>";
                echo "alert('Unable to remove infraction!');window.location='?attendancemonitoring&company=$company&startdate=$startdate&enddate=$enddate';";
              echo "</script>";
              }
            }
            ?>
