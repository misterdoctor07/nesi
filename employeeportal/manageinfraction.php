<?php

include('../config.php'); // Include your database connection file

// Get the logged-in user ID
$userId = $_SESSION['idno'];

// Fetch user details
$userQuery = mysqli_query($con, "SELECT ep.lastname, jt.jobtitle, ed.designation, ed.department 
                                 FROM employee_details ed 
                                 INNER JOIN employee_profile ep ON ep.idno = ed.idno 
                                 INNER JOIN jobtitle jt ON jt.id = ed.designation 
                                 WHERE ed.idno = '$userId'");
$userDetails = mysqli_fetch_assoc($userQuery);

// Extract user designation
$designation = $userDetails['designation']; 

// Check if the user has the required designation
if ($designation != 8) {
    // Redirect to the home page or show an access denied message
    echo "<script>alert('Access Denied!');window.location='?main';</script>";
    exit();
}
?>
            
            
            <div class="col-lg-12">
            <div class="content-panel">
              <div class="panel-heading">
              <h4><a href="?main"><i class="fa fa-arrow-left"></i> HOME</a> | <i class="fa fa-user"></i> INFRACTION LIST<div style="float:right;"></div></h4>              
            </div>
              <div class="panel-body">
                <table class="table table-bordered table-striped table-condensed" id="hidden-table-info">
                  <thead>
                    <tr>
                      <th width="3%">No.</th>
                      <th>Emp ID</th>
                      <th>Employee Name</th>
                      <th>Team</th>
                      <th>Company</th>
                      <th>Date Issued</th>
                      <th>Date Served</th>
                      <th>Type of Memo</th>
                      <th>Type of Offense</th>
                      <th>Points</th>
                      <th>Memo No.</th>
                      <th>Date of Incident</th>
                      <th>Suspention Dates</th>
                      <th>Status</th>
                      
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $x=1;
                      $sqlEmployee=mysqli_query($con,"SELECT ep.*,i.id,i.dateserved,i.dateissued,i.typeofoffense,i.dateofincident,i.typeofmemo,i.points,i.memonumber,i.dateofsuspension,i.status FROM employee_profile ep INNER JOIN infraction i ON i.idno=ep.idno ORDER BY i.dateissued ASC");
                      if(mysqli_num_rows($sqlEmployee)>0){
                        while($company=mysqli_fetch_array($sqlEmployee)){
                          $idno=$company['idno'];
                          $lastname=$company['lastname'];
                          $firstname=$company['firstname'];
                          $middlename=$company['middlename'];
                          $suffix=$company['suffix'];
                          $dateissued=$company['dateissued'];
                          $dateserved=$company['dateserved'];
                          $typeofoffense=$company['typeofoffense'];
                          $typeofmemo=$company['typeofmemo'];
                          $dateofincident=$company['dateofincident'];
                          $points=$company['points'];
                          $memonumber=$company['memonumber'];
                          $dateofsuspension=$company['dateofsuspension'];
                          $status=$company['status'];
                          
                         
                          $sqlDept=mysqli_query($con,"SELECT d.department FROM department d LEFT JOIN employee_details ed ON ed.department=d.id WHERE ed.idno='$idno'");
                          $dept=mysqli_fetch_array($sqlDept);
                          $sqlDept=mysqli_query($con,"SELECT company FROM employee_details WHERE idno='$idno'");
                          $comp=mysqli_fetch_array($sqlDept);
                          if($status=="Void"){
                            $style="class='danger'";
                          }elseif($status=="pending"){
                            $style="class='warning'";
                          }else{
                            $style="class='success'";
                          }
                          echo "<tr $style>";
                            echo "<td>$x.</td>";
                            echo "<td>$idno</td>";
                            echo "<td>$lastname, $firstname $middlename $suffix</td>";
                            echo "<td>$dept[department]</td>";
                            echo "<td>$comp[company]</td>";
                            echo "<td>$dateissued</td>";
                            echo "<td>$dateserved</td>";
                            echo "<td>$typeofmemo</td>";
                            echo "<td>$typeofoffense</td>";                            
                            echo "<td>$points</td>";
                            echo "<td align='center'>$memonumber</td>";
                            echo "<td>$dateofincident</td>";  
                            echo "<td align='center'>$dateofsuspension</td>";
                            echo "<td align='center'>$status</td>";
                           
                            ?>
                            <!-- <td align="center">
                             <?php
                              if($status=="pending"){
                              ?>                              
                              <a href="?manageinfraction&id=<?=$company['id'];?>&serve" class="btn btn-success btn-xs" title="Serve Infraction" onclick="return confirm('Do you wish to serve this infraction?'); return false;"><i class='fa fa-check'></i></a>
                              <a href="?editinfraction&id=<?=$company['id'];?>" class="btn btn-primary btn-xs" title="Edit Infraction"><i class='fa fa-pencil'></i></a>
                              <?php
                              }                              
                              if($status=="pending"){
                              ?>
                              <a href="?manageinfraction&id=<?=$company['id'];?>&delete" class="btn btn-danger btn-xs" title="Void Infraction" onclick="return confirm('Do you wish to void this infraction?'); return false;"><i class='fa fa-trash'></i></a>
                              <?php
                              }else{
                                ?>
                                <a href="?manageinfraction&id=<?=$company['id'];?>&undo" class="btn btn-info btn-xs" title="Restore Infraction" onclick="return confirm('Do you wish to restore this infraction?'); return false;"><i class='fa fa-exchange'></i></a>
                                <?php
                              }
                              ?> -->
                            </td> 
                            <?php
                          echo "</tr>";
                          $x++;
                        }
                      }else{
                        echo "<tr><td colspan='13' align='center'>No record found!</td></tr>";
                      }
                    ?>
                  </tbody>
                </table>              
                </div>  
            </div>
            </div>                    
            <?php
            if(isset($_GET['delete'])){
              $id=$_GET['id'];
              $datenow=date('Y-m-d H:i:s');
              $sqlDelete=mysqli_query($con,"UPDATE infraction SET `status`='Void',updatedby='$fullname',updateddatetime='$datenow' WHERE id='$id'");
              if($sqlDelete){
                echo "<script>alert('Infraction successfully void!');window.location='?manageinfraction';</script>";
              }else{
                echo "<script>alert('Unable to void infraction!');window.location='?manageinfraction';</script>";
              }
            }
            if(isset($_GET['undo'])){
              $id=$_GET['id'];
              $datenow=date('Y-m-d H:i:s');
              $sqlDelete=mysqli_query($con,"UPDATE infraction SET `status`='pending',updatedby='$fullname',updateddatetime='$datenow' WHERE id='$id'");
              if($sqlDelete){
                echo "<script>alert('Infraction successfully restored!');window.location='?manageinfraction';</script>";
              }else{
                echo "<script>alert('Unable to restore infraction!');window.location='?manageinfraction';</script>";
              }
            }
            if(isset($_GET['serve'])){
              $id=$_GET['id'];              
              $datenow=date('Y-m-d H:i:s');
              $sqlDelete=mysqli_query($con,"UPDATE infraction SET `status`='Served',updatedby='$fullname',updateddatetime='$datenow' WHERE id='$id'");
              if($sqlDelete){
                echo "<script>alert('infraction successfully served!');window.location='?manageinfraction';</script>";
              }else{
                echo "<script>alert('Unable to serve infraction!');window.location='?manageinfraction';</script>";
              }
            }
            ?>