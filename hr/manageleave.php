<div class="col-lg-12">
  <div class="content-panel">
    <div class="panel-heading">
      <h4><a href="?main"><i class="fa fa-arrow-left"></i> HOME</a> | <i class="fa fa-file-text"></i> EMPLOYEE LEAVE CREDITS</h4>
    </div>
    <div class="panel-body">
      <table class="table table-bordered table-striped table-condensed" id="hidden-table-info">
        <thead>
          <tr>
            <th width="3%">No.</th>
            <th>Emp ID</th>
            <th>Employee Name</th>
            <th>Department</th>
            <th>Eligibility</th>
            <th>Length of Service</th>
            <th>Period (From - Through)</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $x = 1;
          $sqlEmployee = mysqli_query($con, "SELECT ep.*, ed.* FROM employee_profile ep LEFT JOIN employee_details ed ON ed.idno = ep.idno WHERE ed.status NOT LIKE '%RESIGNED%' ORDER BY ep.lastname ASC");
          if (mysqli_num_rows($sqlEmployee) > 0) {
            while ($company = mysqli_fetch_array($sqlEmployee)) {
              if ($company['status'] == "REGULAR") {
                $status = "<span class='label label-success label-mini'>$company[status]</span>";
              } else {
                $status = "<span class='label label-warning label-mini'>$company[status]</span>";
              }
              $shift = date('h:i A', strtotime($company['startshift'])) . " - " . date('h:i A', strtotime($company['endshift']));
              $datehired = date('m/d/Y', strtotime($company['dateofhired']));
              $dateregular = date('m/d/Y', strtotime($company['dateofregular']));
              $sqlDept = mysqli_query($con, "SELECT department FROM department WHERE id = '$company[department]'");
              if (mysqli_num_rows($sqlDept) > 0) {
                $dept = mysqli_fetch_array($sqlDept);
                $department = $dept['department'];
              } else {
                $department = "";
              }

              $dhire = new DateTime($company['dateofhired']);
              $dnow = new DateTime(date('Y-m-d'));

              $interval = $dhire->diff($dnow);
              $years = $interval->y;
              $month = $interval->m;
              $days = $interval->d;
              $periodfrom = date('F d, Y', strtotime($years . " years", strtotime($company['dateofregular'])));
              $periodto = date('F d, Y', strtotime('1 years', strtotime($periodfrom)));
              
              if ($years <= 2 ) {
                  $vacationleaves = 5;
                 
              } elseif ($years <= 4) {
                  $vacationleaves = 8;
                  
              } elseif ($years <= 6) {
                  $vacationleaves = 12;
                  
              } elseif ($years <= 8) {
                  $vacationleaves = 15;
              } elseif ($years <= 9) {
                  $vacationleaves = 25;
                  
              } else {
                  $vacationleaves = 30;
                  
              }
              
              $dhire = new DateTime($company['dateofhired']);
              $dnow = new DateTime(date('Y-m-d'));
              
              $interval = $dhire->diff($dnow);
              $years = $interval->y;
              $month = $interval->m;
              $days = $interval->d;
              
              $currentMonth = date('n'); // get the current month (1-12)
              $currentDay = date('j'); // get the current day (1-31)
              
              $dateHiredMonth = $dhire->format('n'); // get the month of dateofhired (1-12)
              $dateHiredDay = $dhire->format('j'); // get the day of dateofhired (1-31)
              
              if ($currentMonth == $dateHiredMonth && $currentDay == $dateHiredDay) {
                  // reset vlused, ptoused, slused, blp_used, and eo_used to zero
                  $sqlResetCredits = mysqli_query($con, "UPDATE leave_credits SET vlused = 0, slused = 0, ptoused = 0, blp_used = 0, eo_used = 0 WHERE idno = '$company[idno]'");
              }
             

              if ($years <= 1|| $years <= 2|| $years <= 3|| $years <= 4|| $years <=5){
                $sickleave = 5;
              }
              else 
              {
                $sickleave = 7;
              }
              
              if($year = 1){
                $bdayleave = 1;
              }
              if($years == 0){
              $variable = $month;

              switch ($variable) {
                  case $month = 0:
                    $pto = 5;
                      break;
                  
                  case $month = 1:
                    $pto = 4;
                      break;
                  
                  case $month = 2:
                    $pto = 4;
                      break;
                      case $month = 3:
                        $pto = 3;
                        break;
                        case $month = 4:
                          $pto = 3;
                          break;
                          case $month = 5:
                            $pto = 3;
                            break;
                            case $month = 6:
                              $pto = 2;
                              break;
                              case $month = 7:
                                $pto = 2;
                                break;
                                case $month = 8:
                                  $pto = 2;
                                  break;
                                  case $month = 9:
                                    $pto = 1;
                                    break;
                                    case $month = 10:
                                      $pto = 1;
                                      break;
                                      case $month = 11:
                                        // Code to execute if $variable is equal to "value3"
                                        break;
                  default:
                      // Code to execute if $variable does not match any case
                      break;
              }
              }else {
                $pto = 5;
              }


              if ($month){
                $earlyout = 2;
              }


            
          // Define the SQL query to check leave credits
$sqlCheckCredits = mysqli_query($con, "SELECT * FROM leave_credits WHERE idno = '$company[idno]'");

if (mysqli_num_rows($sqlCheckCredits) > 0) {
    // Update the existing record
    $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits SET vacationleave = '$vacationleaves', sickleave = '$sickleave', pto  = '$pto', bdayleave = '$bdayleave' , earlyout = '$earlyout'WHERE idno = '$company[idno]'");



} else {
    // Insert a new record
    $sqlInsertCredits = mysqli_query($con, "INSERT INTO leave_credits (idno, vacationleave, sickleave, pto, bdayleave, earlyout) VALUES ('$company[idno]', '$vacationleaves', '$sickleave', '$pto',  '$bdayleave', '$earlyout'/*, '$vlused', '$slused', '$ptoused', '$blp_used', '$eo_used'*/)");


}


            
               
              echo "<tr>";
              echo "<td>$x.</td>";
              echo "<td>$company[idno]</td>";
              echo "<td>$company[lastname], $company[firstname] $company[middlename] $company[suffix]</td>";
              echo "<td align='center'>$department</td>";
              echo "<td>$dateregular</td>";
              echo "<td>$years years $month months $days days</td>";
              echo "<td>$periodfrom - $periodto </td>";
            
              ?>
              <td align="center">
                              <a href="?viewleave&id=<?=$company['id'];?>" class="btn btn-success btn-xs" title="Manage Leave Credits"><i class='fa fa-edit'></i></a>
                              
                            </td>
                            <?php
                          echo "</tr>";
                          $x++;
                        }
                      }else{
                        echo "<tr><td colspan='8' align='center'>No record found!</td></tr>";
                      }
             
                    
                    ?>
                  </tbody>
                </table>
                </div>
            </div>
            </div>
            <?php
              if(isset($_GET['update'])){
                $idno=$_GET['idno'];
                $periodfrom=$_GET['periodfrom'];
                $periodto=$_GET['periodto'];
                $sqlInsert=mysqli_query($con,"INSERT INTO credits_eligibility(idno,periodfrom,periodto) VALUES('$idno','$periodfrom','$periodto')");
                echo "<script>window.location='?manageleave';</script>";
              }
            ?>
