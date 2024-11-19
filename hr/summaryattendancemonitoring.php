<?php
$comp = $_GET['company'];
$startdate = $_GET['startdate'];
$enddate = $_GET['enddate'];

$dept = isset($_GET['departments']) ? $_GET['departments'] : []; 
?>

<div class="col-lg-12">
    <div class="content-panel">
        <div class="panel-heading">
            <h4>
                <a href="?monitorattendance"><i class="fa fa-arrow-left"></i> HOME</a> | 
                <i class="fa fa-user"></i> EMPLOYEE LIST 
                <button onclick="tableToExcel('printThis','Detailed_Report')" class="btn btn-success" style="float:right;">
                    <i class="fa fa-download"> </i> EXPORT
                </button>
            </h4>
        </div>

        <!-- Tabs for Departments -->
        <ul class="nav nav-tabs" role="tablist">
            <?php
            $active = "active";
            if (!empty($dept)) {
                foreach ($dept as $dpt) {
                    $deptName = htmlspecialchars($dpt); // Sanitize output
                    echo "<li role='presentation' class='$active'><a href='#$deptName' aria-controls='$deptName' role='tab' data-toggle='tab'>$deptName</a></li>";
                    $active = ""; // Reset active after first tab
                }
            } else {
                echo "<li role='presentation' class='active'><a href='#allDepartments' aria-controls='allDepartments' role='tab' data-toggle='tab'>All Employees</a></li>";
            }
            ?>
        </ul>

        <div class="panel-body tab-content" id="printThis">
            <b>Company: <?=!empty($comp) ? $comp : 'All';?><br />
            Date Range: <?=date('m/d/Y',strtotime($startdate));?> - <?=date('m/d/Y',strtotime($enddate));?></b>

            <?php
            
            $active = "active";

            if (!empty($dept)) {
                foreach ($dept as $dpt) {
                    $deptName = htmlspecialchars($dpt); // Sanitize output
                    ?>
                    <div role="tabpanel" class="tab-pane <?=$active;?>" id="<?=$deptName?>">
                        <h4>Department: <?=$deptName;?></h4>
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th width="3%" rowspan="2" style="vertical-align:middle;">No.</th>
                                    <th rowspan="2" style="vertical-align:middle;">Emp ID</th>
                                    <th rowspan="2" style="vertical-align:middle;">Employee Name</th>
                                    <th rowspan="2" style="vertical-align:middle;">Department</th>
                                    <th rowspan="2" style="vertical-align:middle;">Shift</th>
                                    <th rowspan="2" style="vertical-align:middle;">Work Area</th>
                                    <th rowspan="2" style="vertical-align:middle;">Date</th>
                                    <th colspan="2" align="center">Shift 1</th>
                                    <th colspan="2" align="center">Shift 2</th>
                                    <th rowspan="2" style="vertical-align:middle;">Action</th>
                                    <th rowspan="2" style="vertical-align:middle;">Add Time</th>
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
                                $x = 1;
                                mysqli_query($con, "SET NAMES 'utf8'");

                                // Condition 1: If both company and department are selected
                                if (!empty($comp) && !empty($dept)) {
                                    $sqlEmployee = mysqli_query($con, "SELECT ep.*, ed.* FROM employee_profile ep 
                                        LEFT JOIN employee_details ed ON ed.idno = ep.idno 
                                        WHERE ed.status NOT LIKE '%RESIGNED%' 
                                        AND company = '$comp' AND department = '$deptName'
                                        ORDER BY ep.lastname ASC");
                                }
                                // Condition 2: If only the department is selected
                                elseif (!empty($dept) && empty($comp)) {
                                    $sqlEmployee = mysqli_query($con, "SELECT ep.*, ed.* FROM employee_profile ep 
                                        LEFT JOIN employee_details ed ON ed.idno = ep.idno 
                                        WHERE ed.status NOT LIKE '%RESIGNED%' 
                                        AND department = '$deptName' 
                                        ORDER BY ep.lastname ASC");
                                }
                                 // Display Employees
                                 if (mysqli_num_rows($sqlEmployee) > 0) {
                                    while ($company = mysqli_fetch_array($sqlEmployee)) {
                                        $idn = $company['idno'];
                                        $statusLabel = ($company['status'] == "REGULAR") 
                                            ? "<span class='label label-success label-mini'>$company[status]</span>" 
                                            : "<span class='label label-warning label-mini'>$company[status]</span>";
                                            
                                        $shift = date('h:i A', strtotime($company['startshift'])) . " - " . date('h:i A', strtotime($company['endshift']));
                                        $datehired = date('m/d/Y', strtotime($company['dateofhired']));

                                        $sqlDepartment=mysqli_query($con,"SELECT * FROM department WHERE id='$company[department]'");
                                        if(mysqli_num_rows($sqlDepartment)>0){
                                          $dept=mysqli_fetch_array($sqlDepartment);
                                          $deptName=$dept['department'];
                                        }else{
                                          $deptName="";
                                        }
                                        // Fetch attendance data
                                        $shift = date('h:i A', strtotime($company['startshift'])) . " - " . date('h:i A', strtotime($company['endshift']));
                                        $datehired = date('m/d/Y', strtotime($company['dateofhired']));
                                        
                                        $sqlAttendance = mysqli_query($con, "SELECT * FROM attendance WHERE logindate BETWEEN '$startdate' AND '$enddate' AND idno='$company[idno]' ORDER BY logindate ASC");
                                        
                                        $login1 = "";
                                        $logout1 = "";
                                        $login2 = "";
                                        $logout2 = "";
                                        $datearray = "";
                                        $action = "";
                                        $removepoint = "";
                                        
                                        if (mysqli_num_rows($sqlAttendance) > 0) {
                                            $datearray = ""; // Initialize $datearray if not done before
                                            while ($attend = mysqli_fetch_array($sqlAttendance)) {
                                                $idno = $company['idno'];
                                                $datearray .= date('m/d/Y', strtotime($attend['logindate'])) . "<br>";
                                                $shiftfrom = $company['startshift'];
                                                $endshift = $company['endshift'];
                                               
                                                // Set late threshold (15 minutes after startshift)
                                            //    $lateThreshold = date('H:i:s', strtotime($shiftfrom) + 15 * 60);
                                            //    $remarks = ($attend['loginam'] > $lateThreshold) ? 'L' : 'P';
                                            //    $loginTime = $attend['loginam'];
                                       
                                            //    if ($loginTime > $lateThreshold || ($loginTime >= '00:00:00' && $loginTime <= '02:00:00')) {
                                            //        $remarks = 'L';
                                            //    } else {
                                            //        $remarks = 'P';
                                            //    }
                                       
                                               // If the user is late, assign offense points and remarks automatically
                                               if ($remarks === 'L') {
                                                   // Query offense details for "late with 15 mins" (assuming offense ID is 16)
                                                   $sqlOffense = mysqli_query($con, "SELECT * FROM offense WHERE id='15'");
                                                   $off = mysqli_fetch_array($sqlOffense);
                                                   $code = str_replace('Attendance Infraction ', '', $off['title']);
                                                   $penalty = $off['fpoints'];
                                                   $frequency = $off['frequency'] - 1;
                                       
                                                   // Check frequency within the current period
                                                   $freq = 0;
                                                   $firstStartMonth = date('Y') . "-01-01";
                                                   $firstEndMonth = date('Y') . "-06-30";
                                                   $secondStartMonth = date('Y') . "-07-01";
                                                   $secondEndMonth = date('Y') . "-12-31";
                                       
                                                   if (strtotime($attend['logindate']) >= strtotime($firstStartMonth) && strtotime($attend['logindate']) <= strtotime($firstEndMonth)) {
                                                       $sqlInstance = mysqli_query($con, "SELECT * FROM points WHERE logindate BETWEEN '$firstStartMonth' AND '$firstEndMonth' AND offense='15' AND idno='$idno'");
                                                       $freq += mysqli_num_rows($sqlInstance);
                                                   } elseif (strtotime($attend['logindate']) >= strtotime($secondStartMonth) && strtotime($attend['logindate']) <= strtotime($secondEndMonth)) {
                                                       $sqlInstance = mysqli_query($con, "SELECT * FROM points WHERE logindate BETWEEN '$secondStartMonth' AND '$secondEndMonth' AND offense='15' AND idno='$idno'");
                                                       $freq += mysqli_num_rows($sqlInstance);
                                                   }
                                       
                                                   // Calculate points based on frequency
                                                   $points = ($freq >= $frequency) ? $off['points'] + $penalty : $off['points'];
                                       
                                                   // Insert or update points in points table
                                                   $sqlPointCheck = mysqli_query($con, "SELECT * FROM points WHERE idno='$idno' AND logindate='{$attend['logindate']}' AND offense='15'");
                                                   if (mysqli_num_rows($sqlPointCheck) > 0) {
                                                       $sqlInsert = mysqli_query($con, "UPDATE points SET points='$points' WHERE idno='$idno' AND logindate='{$attend['logindate']}' AND offense='15'");
                                                   } else {
                                                       $sqlInsert = mysqli_query($con, "INSERT INTO points (idno, logindate, points, offense) VALUES ('$idno', '{$attend['logindate']}', '$points', '15')");
                                                   }
                                       
                                                   // Update attendance remarks with offense code
                                                   mysqli_query($con, "UPDATE attendance SET remarks='$code' WHERE idno='$idno' AND logindate='{$attend['logindate']}'");
                                                   $color = "style='color:red;'"; // Red color for late
                                               } else {
                                                   $color = "";
                                               }
                                       
                                               $colorLogoutAM = "";
                                               $colorLoginPM = "";
                                               $colorLogoutPM = "";

                                          

                                               
                                                   // Code for detecting lateness remains unchanged...
                                                   
                                                   // Detect Over Break
                                                   if (isset($attend['logoutam']) && isset($attend['loginpm'])) {
                                                       $interval = strtotime($attend['loginpm']) - strtotime($attend['logoutam']); // Calculate interval between logoutam and loginpm
                                                       if ($interval > 3600) { // Overbreak threshold (1 hour)
                                                           $remarks .= 'Code OB'; // Set Over Break remark
                                                                   
                                                           // Query offense details for "Over Break" (assuming offense ID is 17)
                                                           $sqlOffense = mysqli_query($con, "SELECT * FROM offense WHERE id='19'");
                                                           $off = mysqli_fetch_array($sqlOffense);
                                                           $code = str_replace('Attendance Infraction ', '', $off['title']);
                                                           $penalty = $off['fpoints'];
                                                           $frequency = $off['frequency'] - 1;
                                                   
                                                           // Check frequency within the current period
                                                           $freq = 0;
                                                           $firstStartMonth = date('Y') . "-01-01";
                                                           $firstEndMonth = date('Y') . "-06-30";
                                                           $secondStartMonth = date('Y') . "-07-01";
                                                           $secondEndMonth = date('Y') . "-12-31";
                                                   
                                                           if (strtotime($attend['logindate']) >= strtotime($firstStartMonth) && strtotime($attend['logindate']) <= strtotime($firstEndMonth)) {
                                                               $sqlInstance = mysqli_query($con, "SELECT * FROM points WHERE logindate BETWEEN '$firstStartMonth' AND '$firstEndMonth' AND offense='19' AND idno='$idno'");
                                                               $freq += mysqli_num_rows($sqlInstance);
                                                           } elseif (strtotime($attend['logindate']) >= strtotime($secondStartMonth) && strtotime($attend['logindate']) <= strtotime($secondEndMonth)) {
                                                               $sqlInstance = mysqli_query($con, "SELECT * FROM points WHERE logindate BETWEEN '$secondStartMonth' AND '$secondEndMonth' AND offense='19' AND idno='$idno'");
                                                               $freq += mysqli_num_rows($sqlInstance);
                                                           } 
                                                   
                                                           // Calculate points based on frequency
                                                           $points = ($freq >= $frequency) ? $off['points'] + $penalty : $off['points'];
                                                   
                                                           // Insert or update points in points table
                                                           $sqlPointCheck = mysqli_query($con, "SELECT * FROM points WHERE idno='$idno' AND logindate='{$attend['logindate']}' AND offense='19'");
                                                           if (mysqli_num_rows($sqlPointCheck) > 0) {
                                                               $sqlInsert = mysqli_query($con, "UPDATE points SET points='$points' WHERE idno='$idno' AND logindate='{$attend['logindate']}' AND offense='19'");
                                                           } else {
                                                               $sqlInsert = mysqli_query($con, "INSERT INTO points (idno, logindate, points, offense) VALUES ('$idno', '{$attend['logindate']}', '$points', '19')");
                                                           }
                                                   
                                                           // Update attendance remarks with offense code
                                                           mysqli_query($con, "UPDATE attendance SET remarks='$code' WHERE idno='$idno' AND logindate='{$attend['logindate']}'");
                                                                   
                                                           // Set color specifically for overbreak fields (loginpm and logoutam)
                                                           $colorLogoutAM = "style='color:Blue;'";
                                                           $colorLoginPM = "style='color:Blue;'";
                                                       }
                                                   }

                                                    // Set gray color if no overbreak condition for logoutam, loginpm, and logoutpm
                                                    if ($attend['logoutam'] == "00:00:00") {
                                                        $colorLogoutAM = "style='color:transparent;'";
                                                    } else if (!$colorLogoutAM) { // Only gray if not already orange
                                                        $colorLogoutAM = "style='color:gray;'";
                                                    }

                                                    if ($attend['loginpm'] == "00:00:00") {
                                                        $colorLoginPM = "style='color:transparent;'";
                                                    } else if (!$colorLoginPM) { // Only gray if not already orange
                                                        $colorLoginPM = "style='color:gray;'";
                                                    }

                                                    if ($attend['logoutpm'] == "00:00:00") {
                                                        $colorLogoutPM = "style='color:transparent;'";
                                                    } else {
                                                        $colorLogoutPM = "style='color:gray;'";
                                                    }

                                                    // Build the output strings with the appropriate colors
                                                    $login1 .= "<font $color>" . date('h:i A', strtotime($attend['loginam'])) . "</font><br>";
                                                    $logout1 .= "<font $colorLogoutAM>" . date('h:i A', strtotime($attend['logoutam'])) . "</font><br>";
                                                    $login2 .= "<font $colorLoginPM>" . date('h:i A', strtotime($attend['loginpm'])) . "</font><br>";
                                                    $logout2 .= "<font $colorLogoutPM>" . date('h:i A', strtotime($attend['logoutpm'])) . "</font><br>";
                                                                                                    
                                        
                                                $sqlPoints = mysqli_query($con, "SELECT * FROM points WHERE idno='$idno' AND logindate='{$attend['logindate']}'");
                                                if (mysqli_num_rows($sqlPoints) > 0) {
                                                    $point = mysqli_fetch_array($sqlPoints);
                                                    $points = $point['points'];
                                                    $point_id = $point['id'];
                                                } else {
                                                    $points = 0;
                                                    $point_id = "";
                                                }
                                        
                                                if ($point_id <> '') {
                                                    $removepoint = "| <a href='?attendancemonitoring&idno=$idno&id=$point_id&deleteinfraction&company=$comp&startdate=$startdate&enddate=$enddate&logindate={$attend['logindate']}' title='Delete Time'><i class='fa fa-trash'></i> Remove Infraction</a>";
                                                } else {
                                                    $removepoint = "";
                                                }
                                                
                                        
                                                $action .= "<a href='?attendancemonitoringsummary&edit&company=$comp&startdate=$startdate&enddate=$enddate&idno=$idno&logindate={$attend['logindate']}'>
                                                              <i class='fa fa-edit fa-fw'></i> Infraction</a> | 
                                                              <a href='?edittime&idno=$idno&id={$attend['id']}&company=$comp&startdate=$startdate&enddate=$enddate' title='Edit Time'>
                                                              <i class='fa fa-edit'></i> Time</a> | 
                                                              <a href='?attendancemonitoring&idno=$idno&id={$attend['id']}&deletetime&company=$comp&startdate=$startdate&enddate=$enddate&logindate={$attend['logindate']}' title='Delete Time'>
                                                              <i class='fa fa-trash'></i> Delete Time</a> $removepoint<br>";
                                            }
                                        } else {
                                            $login1 = "";
                                            $logout1 = "";
                                            $login2 = "";
                                            $logout2 = "";
                                            $datearray = "";
                                            $action = "";
                                        }
                                        $idno = $company['idno'];
                                       
                                        echo "<tr>";
                                        echo "<td>$x.</td>";
                                        echo "<td>$idn</td>";
                                        echo "<td>$company[lastname], $company[firstname]</td>";
                                        echo "<td>$deptName</td>";
                                        echo "<td>$shift</td>";
                                        echo "<td align='center'>$company[location]</td>";
                                        echo "<td align='center'>$datearray</td>";
                                        echo "<td align='center'>$login1</td>";
                                        echo "<td align='center'>$logout1</td>";
                                        echo "<td align='center'>$login2</td>";
                                        echo "<td align='center'>$logout2</td>";
                                        echo "<td align='left'>$action</td>";
                                        echo "<td align='left'><a href='?edittime&idno=$idno&id=&company=$comp&startdate=$startdate&enddate=$enddate&logindate' title='Add Time'><i class='fa fa-edit'></i> Add Time</a></td>";
                                        echo "</tr>";
                                        $x++;
                                    }
                                } else {
                                    echo "<tr><td colspan='12' align='center'>No record found for $deptName!</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    $active = ""; // Reset active class after the first tab
                }
            }  else {
                // Condition 3: If only the company is selected
                
                ?>
                
                <div role="tabpanel" class="tab-pane active" id="allDepartments">
                    <h4>All Departments</h4>
                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th width="3%" rowspan="2" style="vertical-align:middle;">No.</th>
                                <th rowspan="2" style="vertical-align:middle;">Emp ID</th>
                                <th rowspan="2" style="vertical-align:middle;">Employee Name</th>
                                <th rowspan="2" style="vertical-align:middle;">Shift</th>
                                <th rowspan="2" style="vertical-align:middle;">Work Area</th>
                                <th rowspan="2" style="vertical-align:middle;">Date</th>
                                <th colspan="2" align="center">Shift 1</th>
                                <th colspan="2" align="center">Shift 2</th>
                                <th rowspan="2" style="vertical-align:middle;">Action</th>
                                <th rowspan="2" style="vertical-align:middle;">Add Time</th>
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
$x = 1;

mysqli_query($con, "SET NAMES 'utf8'");

// Only the company is selected (no departments)
$sqlEmployee = mysqli_query($con, "SELECT ep.*, ed.* FROM employee_profile ep 
    LEFT JOIN employee_details ed ON ed.idno = ep.idno 
    WHERE ed.status NOT LIKE '%RESIGNED%' 
    AND company = '$comp' 
    ORDER BY ep.lastname ASC");

if (mysqli_num_rows($sqlEmployee) > 0) {
    while ($company = mysqli_fetch_array($sqlEmployee)) {
        $idn = $company['idno'];
        $statusLabel = ($company['status'] == "REGULAR") 
            ? "<span class='label label-success label-mini'>$company[status]</span>" 
            : "<span class='label label-warning label-mini'>$company[status]</span>";

        $shift = date('h:i A', strtotime($company['startshift'])) . " - " . date('h:i A', strtotime($company['endshift']));
        $datehired = date('m/d/Y', strtotime($company['dateofhired']));

        // Fetch attendance data
        $sqlAttendance = mysqli_query($con, "SELECT * FROM attendance 
            WHERE logindate BETWEEN '$startdate' AND '$enddate' 
            AND idno = '$idn' 
            ORDER BY logindate ASC");

        $login1 = $logout1 = $login2 = $logout2 = $datearray = $action = "";

        if (mysqli_num_rows($sqlAttendance) > 0) {
            while ($attend = mysqli_fetch_array($sqlAttendance)) {
                $login1 .= date('h:i A', strtotime($attend['loginam'])) . "<br>";
                $logout1 .= date('h:i A', strtotime($attend['logoutam'])) . "<br>";
                $login2 .= date('h:i A', strtotime($attend['loginpm'])) . "<br>";
                $logout2 .= date('h:i A', strtotime($attend['logoutpm'])) . "<br>";
                $datearray .= date('m/d/Y', strtotime($attend['logindate'])) . "<br>";
                
                // Action links for editing and deleting attendance
                $action .= "<a href='?attendancemonitoringsummary&edit&company=$comp&startdate=$startdate&enddate=$enddate&idno=$idn&logindate={$attend['logindate']}' title='Edit Attendance'><i class='fa fa-edit'></i> Edit</a> | 
                            <a href='?attendancemonitoring&idno=$idn&id={$attend['id']}&deletetime&company=$comp&startdate=$startdate&enddate=$enddate&logindate={$attend['logindate']}' title='Delete Attendance'><i class='fa fa-trash'></i> Delete</a><br>";
            }
        } else {
            $login1 = $logout1 = $login2 = $logout2 = $datearray = "-";
        }

        echo "<tr>";
        echo "<td>$x.</td>";
        echo "<td>$idn</td>";
        echo "<td>$company[lastname], $company[firstname]</td>";
        echo "<td>$shift</td>";
        echo "<td align='center'>$company[location]</td>";
        echo "<td align='center'>$datearray</td>";
        echo "<td align='center'>$login1</td>";
        echo "<td align='center'>$logout1</td>";
        echo "<td align='center'>$login2</td>";
        echo "<td align='center'>$logout2</td>";
        echo "<td align='left'>$action</td>";
        echo "<td align='left'><a href='?edittime&idno=$idn&id=&company=$comp&startdate=$startdate&enddate=$enddate&logindate' title='Add Time'><i class='fa fa-edit'></i> Add Time</a></td>";
        echo "</tr>";
        $x++;
    }
} else {
    echo "<tr><td colspan='12' align='center'>No records found!</td></tr>";
}
?>
                        </tbody>
                    </table>
                </div>
                <?php
           }
           if (isset($_GET['deletetime'])) {
            $idno = $_GET['idno'];
            $id = $_GET['id'];
            $company = $_GET['company'];
            $startdate = $_GET['startdate'];
            $enddate = $_GET['enddate'];
            $logindate = $_GET['logindate'];
        
            // Retrieve the remarks from the attendance table
            $sqlGetRemarks = mysqli_query($con, "SELECT remarks FROM attendance WHERE id = '$id'");
            if ($sqlGetRemarks && mysqli_num_rows($sqlGetRemarks) > 0) {
                $row = mysqli_fetch_assoc($sqlGetRemarks);
                $remarks = $row['remarks']; 
        
                // Delete the attendance record
                $sqlDelete = mysqli_query($con, "DELETE FROM attendance WHERE id = '$id'");
                if ($sqlDelete) {
                    // Only update leave credits if the remarks are for a leave type
                    if (in_array($remarks, ['VL', 'SL', 'PTO', 'BLP', 'EO', 'SPL'])) {
                        // Update the appropriate leave credits based on the remarks (leave type)
                        switch ($remarks) {
                            case 'VL':
                                $sqlUpdateCredits = mysqli_query($con, 
                                    "UPDATE leave_credits SET vlused = vlused - 1 WHERE idno = '$idno'");
                                break;
                            case 'SL':
                            case 'SL-A':
                                $sqlUpdateCredits = mysqli_query($con, 
                                    "UPDATE leave_credits SET slused = slused - 1 WHERE idno = '$idno'");
                                break;
                            case 'PTO':
                                $sqlUpdateCredits = mysqli_query($con, 
                                    "UPDATE leave_credits SET ptoused = ptoused - 1 WHERE idno = '$idno'");
                                break;
                            case 'BLP':
                                $sqlUpdateCredits = mysqli_query($con, 
                                    "UPDATE leave_credits SET blp_used = blp_used - 1 WHERE idno = '$idno'");
                                break;
                            case 'EO':
                            case 'P-EO':
                                $sqlUpdateCredits = mysqli_query($con, 
                                    "UPDATE leave_credits SET eo_used = eo_used - 1 WHERE idno = '$idno'");
                                break;
                            case 'SPL':
                              $sqlUpdateCredits = mysqli_query($con, 
                                  "UPDATE leave_credits SET spl_used = spl_used - 1 WHERE idno = '$idno'");
                                break;
                            default:
                                echo "<script>alert('Leave type not recognized. No credits updated.');</script>";
                                break;
                        }
                    }
        
                    // Also delete the associated points log
                    $deletePoints = mysqli_query($con, "DELETE FROM points WHERE idno='$idno' AND logindate='$logindate'");
        
                    echo "<script>alert('Item successfully removed!'); window.location='?attendancemonitoring&company=$company&startdate=$startdate&enddate=$enddate';</script>";
                } else {
                    echo "<script>alert('Unable to delete time!'); window.location='?attendancemonitoring&company=$company&startdate=$startdate&enddate=$enddate';</script>";
                }
            } else {
                echo "<script>alert('Error retrieving remarks for the attendance record.'); window.location='?attendancemonitoring&company=$company&startdate=$startdate&enddate=$enddate';</script>";
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
       </div>
   </div>
</div>

<script>
    $(document).ready(function(){
        $('.nav-tabs a').click(function(){
            $(this).tab('show');
        });
    });
</script>

