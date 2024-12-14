<?php
    $id=$_GET['period'];
    $comp=$_GET['company'];
    $sqlPayroll=mysqli_query($con,"SELECT * FROM payroll WHERE id='$id'");
    $payroll=mysqli_fetch_array($sqlPayroll);
    $posted=0;
    $notposted=0;
    $sqlDetails=mysqli_query($con,"SELECT pd.status FROM payroll_details pd INNER JOIN employee_details ed ON ed.idno=pd.idno WHERE pd.payrollperiod='$id' AND ed.company='$comp'");
    if(mysqli_num_rows($sqlDetails)>0){
      while($details=mysqli_fetch_array($sqlDetails)){
        if($details['status']=="posted"){
          $posted++;
        }else{
          $notposted++;
        }
      }
    }

    // Fetch unique companies from the employee_details table
    $sqlCompanies = mysqli_query($con, "SELECT DISTINCT company FROM employee_details ORDER BY company");

    if (!$sqlCompanies) {
        echo "Query error: " . mysqli_error($con);
    }
?>
<div class="col-lg-12">
            <div class="content-panel">
              <div class="panel-heading">
              <h4><a href="?main"><i class="fa fa-arrow-left"></i> HOME</a> | <i class="fa fa-calendar"></i>
              <div style="float:right; margin-bottom: 20px;">
                    <form>
                        <button type="button" onclick="tablesToExcel('Leave_Application_Report')" class="btn btn-success">EXPORT TO EXCEL</button>
                    </form>
                </div></h4>
                <h4><i class="fa fa-file-text-o"></i> SELECT PERIOD</h4>            
            </div>
            <div class="panel-body">                                                                        
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Payroll Period</label>
                  <div class="col-sm-8">
                    <select name="period" class="form-control" required>
                        <option value=""></option>
                        <?php
                        $sqlPeriod=mysqli_query($con,"SELECT * FROM payroll ORDER BY id DESC");
                        if(mysqli_num_rows($sqlPeriod)>0){
                            while($period=mysqli_fetch_array($sqlPeriod)){
                                echo "<option value='$period[id]'>".date('F d, Y',strtotime($period['periodfrom']))." to ".date('F d, Y',strtotime($period['periodto']))."</option>";
                            }
                        }
                        ?>
                    </select>
                    </div>
                  </div> 
                <?php
                if($posted==0 && $notposted==0){

              }elseif($notposted>0){
                ?>
                <a href="?managepayroll&postpayslip&period=<?=$id;?>&company=<?=$comp;?>" class="btn btn-primary" style="float:right;" onclick="return confirm('Do you wish to post payslip?');return false;">POST PAYSLIP</a>
                <?php
              }else{
                ?>
                <a href="?managepayroll&undopostpayslip&period=<?=$id;?>&company=<?=$comp;?>" class="btn btn-warning" style="float:right;" onclick="return confirm('Do you wish to undo post?');return false;">UNDO POST</a>
                <?php
              }
                ?>
              </h4>
            </div>

        <!-- Company Tabs -->
        <ul class="nav nav-tabs">
            <?php
                $active = 'active'; // Set the first tab as active
                while ($company = mysqli_fetch_array($sqlCompanies)) {
                    $companyCode = $company['company'];
                    
                    // Fetch count of pending leave applications for the company
                    $sqlCount = mysqli_query($con, "SELECT COUNT(*) AS total FROM leave_application la
                        INNER JOIN employee_details ed ON la.idno = ed.idno
                        WHERE ed.company = '$companyCode' 
                        AND la.appstatus NOT IN ('Pending', 'Cancelled', 'Disapproved') 
                        AND la.remarks != 'POSTED'");
                    $count = mysqli_fetch_assoc($sqlCount)['total'];
                    
                    echo "<li class='$active' style='position: relative;'>
                            <a data-toggle='tab' href='#tab-$companyCode'>$companyCode";
                    if ($count > 0) {
                        echo "<span class='badge badge-right'>$count</span>";
                    }
                    echo "</a></li>";
                    $active = ''; // Remove active class from subsequent tabs
                }
            ?>
        </ul>


        <div class="tab-content">
            <?php
                // Reset the result pointer to reuse it
                mysqli_data_seek($sqlCompanies, 0);
                $active = 'in active'; // Set the first tab content as active
                while ($company = mysqli_fetch_array($sqlCompanies)) {
                    $companyCode = $company['company'];
                    echo "<div id='tab-$companyCode' class='tab-pane fade $active'>";

                    // Fetch unique departments for the company
                    $sqlDepartments = mysqli_query($con, "SELECT DISTINCT d.department FROM employee_details ed
                        INNER JOIN department d ON d.id = ed.department
                        WHERE ed.company = '$companyCode' ORDER BY d.department");

                    echo "<ul class='nav nav-pills' style='margin-top: 10px;'>";
                    $deptActive = 'active';
                    
                    while ($department = mysqli_fetch_array($sqlDepartments)) {
                        $departmentName = $department['department'];
                        
                        // Fetch count of pending leave applications for the department
                        $sqlDeptCount = mysqli_query($con, "SELECT COUNT(*) AS total FROM leave_application la
                            INNER JOIN employee_details ed ON la.idno = ed.idno
                            INNER JOIN department d ON d.id = ed.department
                            WHERE ed.company = '$companyCode' 
                            AND d.department = '$departmentName'
                            AND la.appstatus NOT IN ('Pending', 'Cancelled', 'Disapproved') 
                            AND la.remarks != 'POSTED'");
                        $deptCount = mysqli_fetch_assoc($sqlDeptCount)['total'];

                        // Assign unique ID using company and department names
                        $deptId = preg_replace('/[^A-Za-z0-9\-]/', '', $departmentName); // Remove special characters

                        // Add department tab with badge in top-right corner
                        echo "<li class='$deptActive' style='position: relative;'><a data-toggle='pill' href='#dept-$companyCode-$deptId'>$departmentName";
                        if ($deptCount > 0) {
                            echo "<span class='badge badge-right'>$deptCount</span>";
                        }
                        echo "</a></li>";
                        $deptActive = ''; // Remove active class from subsequent department tabs
                    }
                    echo "</ul>";

                    echo "<div class='tab-content' style='margin-top: 10px;'>";
                    mysqli_data_seek($sqlDepartments, 0);
                    $deptActive = 'in active';

                    // Department Content
                    while ($department = mysqli_fetch_array($sqlDepartments)) {
                        $departmentName = $department['department'];
                        $deptId = preg_replace('/[^A-Za-z0-9\-]/', '', $departmentName); // Remove special characters
                        // Use unique ID for department tabs
                        echo "<div id='dept-$companyCode-$deptId' class='tab-pane fade $deptActive'>";

                        // Fetch employees based on company and department
                        $sqlEmployee = mysqli_query($con, "SELECT la.*, la.id as laid, ep.*, ed.*, d.department 
                            FROM leave_application la
                            INNER JOIN employee_profile ep ON ep.idno = la.idno 
                            INNER JOIN employee_details ed ON ed.idno = ep.idno
                            INNER JOIN department d ON d.id = ed.department 
                            WHERE ed.company = '$companyCode' AND d.department = '$departmentName' 
                            ORDER BY 
                                CASE 
                                    WHEN la.appstatus NOT IN ('Pending', 'Cancelled', 'Disapproved') AND la.remarks != 'POSTED' THEN 1 
                                    ELSE 2 
                                END,
                            la.datearray DESC");

            ?>
                <!-- Search Bar -->
                <div class="d-flex align-items-center mb-3" style="margin-bottom: 3px;">
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Search..." onkeyup="filterTable(this)">
                    </div>
                </div>

                    <table class="table table-bordered table-striped table-condensed" id="attendanceTable">
                        <thead>
                            <tr>
                              <th width="3%">No.</th>
                              <th>Emp ID</th>
                              <th>Employee Name</th>
                              <th>Team</th>
                              <th>Company</th>
                              <th>Addons</th>
                              <th>Total Gross</th>
                              <th>Total Deductions</th>
                              <th>Net Pay</th>
                              <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                    $x=1;
                      $sqlEmployee=mysqli_query($con,"SELECT ep.* FROM employee_profile ep LEFT JOIN employee_details ed ON ed.idno=ep.idno WHERE ed.status NOT LIKE '%RESIGNED%' AND ed.company='$comp'  ORDER BY ed.department ASC,ep.lastname ASC");
                      if(mysqli_num_rows($sqlEmployee)>0){
                        while($company=mysqli_fetch_array($sqlEmployee)){
                          $idno=$company['idno'];
                          $lastname=$company['lastname'];
                          $firstname=$company['firstname'];
                          $middlename=$company['middlename'];
                          $suffix=$company['suffix'];
                          $sqlGross=mysqli_query($con,"SELECT * FROM payroll_details WHERE idno='$idno' AND payrollperiod='$id'");
                          if(mysqli_num_rows($sqlGross)>0){
                              $empgross=mysqli_fetch_array($sqlGross);
                                $totalpay=$empgross['totalpay'];
                                $reghours=$empgross['reghours'];
                                $payroll_id=$empgross['id'];
                          }else{
                              $totalpay=0;
                              $hourswork=0;
                              $payroll_id="";
                              $reghours=0;
                          }

                          $sqlDept=mysqli_query($con,"SELECT d.department FROM department d LEFT JOIN employee_details ed ON ed.department=d.id WHERE ed.idno='$idno'");
                          if(mysqli_num_rows($sqlDept)>0){
                            $dept=mysqli_fetch_array($sqlDept);
                            $department=$dept['department'];
                          }else{
                            $department="";
                          }
                          $sqlDept=mysqli_query($con,"SELECT company FROM employee_details WHERE idno='$idno'");
                          $comp=mysqli_fetch_array($sqlDept);
                          $emppayroll=mysqli_query($con,"SELECT * FROM employee_payroll WHERE idno='$idno'");
                          if(mysqli_num_rows($emppayroll)>0){
                            $emp=mysqli_fetch_array($emppayroll);
                            $salary=$emp['salary'];
                          }else{
                            $salary=0;
                          }
                          $perhour=$salary/8;
                          //$grosspay=$hourswork*$hoursrate;
                          //$gross=number_format($grosspay,2);
                          $sqlDeduction=mysqli_query($con,"SELECT SUM(amount) as amount FROM payroll_deductions WHERE idno='$idno' AND payrollperiod='$id' GROUP BY idno");
                          if(mysqli_num_rows($sqlDeduction)>0){
                              $deduct=mysqli_fetch_array($sqlDeduction);
                              $deductions=$deduct['amount'];
                          }else{
                              $deductions=0;
                          }
                          $sqlAddons=mysqli_query($con,"SELECT SUM(amount) as amount FROM payroll_addons WHERE idno='$idno' AND payrollperiod='$id' GROUP BY idno");
                          if(mysqli_num_rows($sqlAddons)>0){
                              $add=mysqli_fetch_array($sqlAddons);
                              $addons=$add['amount'];
                          }else{
                              $addons=0;
                          }
                          $tdeduct=$deductions;
                          $totaldeductions=number_format($tdeduct,2);
                          $netpay=number_format(($totalpay+$addons)-$tdeduct,2);
                          //$adjustment=$totalpay-($perhour*$hourswork);
                          $adjustment=0;
                          echo "<tr>";
                            echo "<td>$x.</td>";
                            echo "<td>$idno</td>";
                            echo "<td>$lastname, $firstname $middlename $suffix</td>";
                            echo "<td>$department</td>";
                            echo "<td>$comp[company]</td>";
                            echo "<td align='right'>".number_format($addons,2)."</td>";
                            echo "<td align='right'>".number_format($totalpay,2)."</td>";
                            echo "<td align='right'>$totaldeductions</td>";
                            echo "<td align='right'>$netpay</td>";
                            ?>
                            <td align="center">
                              <a href="?editpayroll&idno=<?=$idno?>&period=<?=$id;?>&company=<?=$comp['company'];?>" class="btn btn-primary btn-xs" title="Edit Payroll" target="_blank"><i class='fa fa-pencil'></i></a>
                              <?php
                              if($payroll_id==""){

                              }else{
                                ?>
                                <a href="payslip.php?id=<?=$payroll_id;?>" class="btn btn-warning btn-xs" title="Print Payslip" target="_blank"><i class='fa fa-print'></i></a>
                                <?php
                              }
                              ?>
                            </td>
                            <?php
                          echo "</tr>";
                          $x++;
                        }
                      }else{
                        echo "<tr><td colspan='12' align='center'>No record found!</td></tr>";
                      }
                    ?>
                        </tbody>
                    </table>

                    <?php
                        echo "</div>"; // End of department tab content
                        $deptActive = ''; // Remove active class from subsequent department contents
                    }
                    echo "</div>"; // End of department tabs content
                    echo "</div>"; // End of company tab content
                    $active = ''; // Remove active class from subsequent company contents
                }
                    ?>
        </div>
    </div> 
            </div>
            <?php
            // if(isset($_GET['delete'])){
            //   $id=$_GET['id'];
            //   $company=$_GET['company'];
            //   $datenow=date('Y-m-d H:i:s');
            //   $sqlDelete=mysqli_query($con,"UPDATE infraction SET `status`='Void',updatedby='$fullname',updateddatetime='$datenow' WHERE id='$id'");
            //   if($sqlDelete){
            //     echo "<script>alert('Infraction successfully void!');window.location='?manageinfraction&company=$company';</script>";
            //   }else{
            //     echo "<script>alert('Unable to void infraction!');window.location='?manageinfraction';</script>";
            //   }
            // }
            if(isset($_GET['postpayslip'])){
              $id=$_GET['period'];
              $company=$_GET['company'];
              $datenow=date('Y-m-d H:i:s');
              $sqlCheck=mysqli_query($con,"SELECT idno FROM employee_details WHERE company='$company' AND status NOT LIKE '%RESIGNED%'");
              if(mysqli_num_rows($sqlCheck)>0){
                while($check=mysqli_fetch_array($sqlCheck)){
                  $idno=$check['idno'];
                  $sqlUpdate=mysqli_query($con,"UPDATE payroll_details SET status='posted',dateposted='$datenow' WHERE idno='$idno' AND payrollperiod='$id' AND status='pending'");
                }
              }
              if($sqlUpdate){
                echo "<script>alert('Payslip successfully posted!');window.location='?managepayroll&period=$id&company=$company';</script>";
              }else{
                echo "<script>alert('Unable to post payslip!');window.location='?managepayroll&period=$id&company=$company';</script>";
              }
            }
            if(isset($_GET['undopostpayslip'])){
              $id=$_GET['period'];
              $company=$_GET['company'];
              $datenow=date('Y-m-d H:i:s');
              $sqlCheck=mysqli_query($con,"SELECT idno FROM employee_details WHERE company='$company' AND status NOT LIKE '%RESIGNED%'");
              if(mysqli_num_rows($sqlCheck)>0){
                while($check=mysqli_fetch_array($sqlCheck)){
                  $idno=$check['idno'];
                  $sqlUpdate=mysqli_query($con,"UPDATE payroll_details SET status='pending',dateposted=null WHERE idno='$idno' AND payrollperiod='$id' AND status='posted'");
                }
              }
              if($sqlUpdate){
                echo "<script>alert('Payslip successfully unposted!');window.location='?managepayroll&period=$id&company=$company';</script>";
              }else{
                echo "<script>alert('Unable to undo post payslip!');window.location='?managepayroll&period=$id&company=$company';</script>";
              }
            }
            ?>
