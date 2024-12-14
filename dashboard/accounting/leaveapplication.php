         
          <div class="col-lg-12">
            <div class="content-panel">
              <div class="panel-heading">
              <h4><a href="?main"><i class="fa fa-arrow-left"></i> HOME</a> | <i class="fa fa-file-text"></i> LEAVE APPLICATION
              <div style="float:right; width:40%">
                <form method="GET">
                    <input type="hidden" name="leaveapplication">
                    <table border="0" width="100%" cellspacing="1" cellpadding="1">
                        <tr>
                            <td>From</td>
                            <td><input type="date" name="startdate" class="form-control"></td>
                            <td>&nbsp;To</td>
                            <td><input type="date" name="enddate" class="form-control"></td>
                            <td>&nbsp;<input type="submit" name="submit" value="Search" class="btn btn-primary"></td>
                        </tr>                        
                    </table>                    
                </form>
              </div>
            </h4>              
            </div>
              <div class="panel-body">
                <table class="table table-bordered table-striped table-condensed" id="hidden-table-info">
                  <thead>
                    <tr>
                      <th width="3%">No.</th>
                      <th>Employee</th>
                      <th>Leave Type</th>
                      <th>Inclusive Dates</th>
                      <th>Reasons</th>                      
                      <th>Date Applied</th>
                      <th>Approved By</th>
                      <!-- <th>Action</th> -->
                    </tr>
                  </thead>
                  <tbody>
                    <?php                    
                    $x=1;
                    if(isset($_GET['submit'])){
                      $startdate=$_GET['startdate'];
                      $enddate=$_GET['enddate'];
                      $sqlEmployee=mysqli_query($con,"SELECT la.*,la.id as leaveno,la.status as leavestatus,ep.*,ed.* FROM leave_application la INNER JOIN employee_profile ep ON ep.idno=la.idno INNER JOIN employee_details ed ON ed.idno=ep.idno WHERE la.datearray BETWEEN '$startdate' AND '$enddate' ORDER BY la.datearray DESC");
                    }else{
                      $sqlEmployee=mysqli_query($con,"SELECT la.*,la.id as leaveno,la.status as leavestatus,ep.*,ed.* FROM leave_application la INNER JOIN employee_profile ep ON ep.idno=la.idno INNER JOIN employee_details ed ON ed.idno=ep.idno WHERE YEAR(la.datearray) = '".date('Y')."' ORDER BY la.datearray DESC");
                    }                                              
                      if(mysqli_num_rows($sqlEmployee)>0){
                        while($company=mysqli_fetch_array($sqlEmployee)){                                                                                
                          if($company['leavetype']=="VL"){
                              $leave="Vacation Leave (VL)";
                          }
                          if($company['leavetype']=="SL"){
                            $leave="Sick Leave (SL)";
                        }
                        if($company['leavetype']=="PTO"){
                            $leave="Unpaid Leave (PTO)";
                        }
                        if($company['leavetype']=="MTL"){
                            $leave="Maternity Leave (MTL)";
                        }
                        if($company['leavetype']=="PTL"){
                            $leave="Paternity Leave (PTL)";
                        }
                        if($company['leavetype']=="SPL"){
                            $leave="Solo Parent Leave (SPL)";
                        }
                        if($company['leavetype']=="BL"){
                            $leave="Bereavement Leave (BL)";
                        }
                        if($company['leavetype']=="MDL"){
                            $leave="Medical Leave (MDL)";
                        }
                        if($company['leavetype']=="LTL"){
                            $leave="Long Term Leave (LTL)";
                        }
                        if($company['leavetype']=="BLP"){
                            $leave="Birthday Leave (BLP)";
                        }
                        if($company['leavetype']=="EO"){
                            $leave="Early Out (EO)";
                        }
                        if($company['leavetype']=="EEO"){
                            $leave="Emergency Early Out (EEO)";
                        }                   
                          echo "<tr>";
                            echo "<td>$x.</td>";
                            echo "<td>$company[lastname], $company[firstname]</td>";
                            echo "<td>$leave</td>";                            
                            echo "<td>$company[inclusivedates]</td>";                            
                            echo "<td align='left'>$company[reason]</td>";
                            echo "<td align='left'>".date('m/d/Y',strtotime($company['datearray']))."</td>";
                            echo "<td align='left'>";
                            $sqlApproved=mysqli_query($con,"SELECT * FROM leave_signatories WHERE leave_id='$company[leaveno]'");
                            if(mysqli_num_rows($sqlApproved)>0){
                                $signature=mysqli_fetch_array($sqlApproved);
                                $sqlSign=mysqli_query($con,"SELECT ep.*,jt.jobtitle FROM employee_profile ep INNER JOIN employee_details ed ON ed.idno=ep.idno INNER JOIN jobtitle jt ON jt.id=ed.designation WHERE ep.idno='$signature[sign_first]'");
                                if(mysqli_num_rows($sqlSign)>0){
                                    $row=mysqli_fetch_array($sqlSign);
                                    echo $row['firstname']." ".$row['lastname']." - ".$row['jobtitle']."<br>";
                                }
                                $sqlSign=mysqli_query($con,"SELECT ep.*,jt.jobtitle FROM employee_profile ep INNER JOIN employee_details ed ON ed.idno=ep.idno INNER JOIN jobtitle jt ON jt.id=ed.designation WHERE ep.idno='$signature[sign_second]'");
                                if(mysqli_num_rows($sqlSign)>0){
                                    $row=mysqli_fetch_array($sqlSign);
                                    echo $row['firstname']." ".$row['lastname']." - ".$row['jobtitle']."<br>";
                                }
                                $sqlSign=mysqli_query($con,"SELECT ep.*,jt.jobtitle FROM employee_profile ep INNER JOIN employee_details ed ON ed.idno=ep.idno INNER JOIN jobtitle jt ON jt.id=ed.designation WHERE ep.idno='$signature[sign_third]'");
                                if(mysqli_num_rows($sqlSign)>0){
                                    $row=mysqli_fetch_array($sqlSign);
                                    echo $row['firstname']." ".$row['lastname']." - ".$row['jobtitle']."<br>";
                                }
                                $sqlSign=mysqli_query($con,"SELECT ep.*,jt.jobtitle FROM employee_profile ep INNER JOIN employee_details ed ON ed.idno=ep.idno INNER JOIN jobtitle jt ON jt.id=ed.designation WHERE ep.idno='$signature[sign_fourth]'");
                                if(mysqli_num_rows($sqlSign)>0){
                                    $row=mysqli_fetch_array($sqlSign);
                                    echo $row['firstname']." ".$row['lastname']." - ".$row['jobtitle']."<br>";
                                }
                            }
                            echo "</td>";
                            ?>
                            <!-- <td align="center">                                 
                              <a href="?manageleaveapplication&id=<?=$company['leaveno'];?>&approved" class="btn btn-success btn-xs" title="Approved" onclick="return confirm('Do you wish to sign approve this leave application?');return false;"><i class='fa fa-thumbs-up'></i></a>
                              <a href="?manageleaveapplication&id=<?=$company['leaveno'];?>&disapproved" class="btn btn-danger btn-xs" title="Disapproved" onclick="return confirm('Do you wish to disapprove this leave application?');return false;"><i class='fa fa-thumbs-down'></i></a>
                              <a href="?manageleaveapplication&id=<?=$company['idno'];?>&view" class="btn btn-primary btn-xs" title="View Credits"><i class='fa fa-eye'></i></a>
                            </td> -->
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
          if(isset($_GET['view'])){
              $id=$_GET['id'];
          ?>
          <div class="col-lg-3">
            <div class="content-panel">
              <div class="panel-heading">
              <h4><a href="?manageleaveapplication"><i class="fa fa-arrow-left"></i> Close</a> | <i class="fa fa-file-text"></i> LEAVE CREDITS</h4>              
            </div>
              <div class="panel-body">
                <table class="table table-bordered table-striped table-condensed" id="hidden-table-info">
                  <thead>
                    <tr>                                            
                      <th>Leave Type</th>
                      <th>Credit</th>
                      <th>Used</th>                      
                      <th>Remaining</th>                      
                    </tr>
                  </thead>
                  <tbody>                      
                      <?php
                        $sqlCredits=mysqli_query($con,"SELECT * FROM leave_credits WHERE idno='$id'");
                        if(mysqli_num_rows($sqlCredits)>0){
                            $credit=mysqli_fetch_array($sqlCredits);
                            $vlrem=$credit['vacationleave']-$credit['vlused'];                            
                            $slrem=$credit['sickleave']-$credit['slused'];
                            $ptorem=$credit['pto']-$credit['ptoused'];
                            echo "<tr>";
                                echo "<td>Vacation Leave</td>";
                                echo "<td align='center'>$credit[vacationleave]</td>";
                                echo "<td align='center'>$credit[vlused]</td>";
                                echo "<td align='center'>$vlrem</td>";
                            echo "</tr>";
                            echo "<tr>";
                                echo "<td>Sick Leave</td>";
                                echo "<td align='center'>$credit[sickleave]</td>";
                                echo "<td align='center'>$credit[slused]</td>";
                                echo "<td align='center'>$slrem</td>";
                            echo "</tr>";
                            echo "<tr>";
                                echo "<td>PTO</td>";
                                echo "<td align='center'>$credit[pto]</td>";
                                echo "<td align='center'>$credit[ptoused]</td>";
                                echo "<td align='center'>$ptorem</td>";
                            echo "</tr>";
                        }
                      ?>                      
                  </tbody>
                </table>              
                </div>  
            </div>
          </div>
          <?php
          }
          ?>
            <?php
            if(isset($_GET['approved'])){
                $id=$_GET['id'];
                $sqlCheck=mysqli_query($con,"SELECT * FROM leave_signatories WHERE leave_id='$id'");
                if(mysqli_num_rows($sqlCheck)>0){
                    $check=mysqli_fetch_array($sqlCheck);
                    if($check['sign_first']==0){
                        $sqlUpdate=mysqli_query($con,"UPDATE leave_signatories SET sign_first='$_SESSION[idno]' WHERE leave_id='$id'");                        
                    }elseif($check['sign_second']==0){
                        $sqlUpdate=mysqli_query($con,"UPDATE leave_signatories SET sign_second='$_SESSION[idno]' WHERE leave_id='$id'");
                    }elseif($check['sign_third']==0){
                        $sqlUpdate=mysqli_query($con,"UPDATE leave_signatories SET sign_third='$_SESSION[idno]' WHERE leave_id='$id'");
                    }else{                        
                        $sqlUpdate=mysqli_query($con,"UPDATE leave_signatories SET sign_fourth='$_SESSION[idno]' WHERE leave_id='$id'");
                        $sqlUpdate=mysqli_query($con,"UPDATE leave_application SET `status`='approved' WHERE id='$id'");
                        $sqlLeave=mysqli_query($con,"SELECT * FROM leave_application WHERE id='$id'");
                        $leave=mysqli_fetch_array($sqlLeave);
                        $type=$leave['leavetype'];           
                        $nofdays=$leave['numberofdays'];             
                        $sqlCredits=mysqli_query($con,"SELECT * FROM leave_credits WHERE idno='$leave[idno]'");
                        $credit=mysqli_fetch_array($sqlCredits);
                        if($type=="VL"){
                            $used=$credit['vlused'];
                            $newused=$used+$nofdays;
                            $sqlUpdate=mysqli_query($con,"UPDATE leave_credits SET vlused='$newused' WHERE idno='$leave[idno]'");
                        }
                        if($type=="SL"){
                            $used=$credit['slused'];
                            $newused=$used+$nofdays;
                            $sqlUpdate=mysqli_query($con,"UPDATE leave_credits SET slused='$newused' WHERE idno='$leave[idno]'");
                        }
                        if($type=="PTO"){
                            $used=$credit['ptoused'];
                            $newused=$used+$nofdays;
                            $sqlUpdate=mysqli_query($con,"UPDATE leave_credits SET ptoused='$newused' WHERE idno='$leave[idno]'");                            
                        }                        
                    }
                }else{
                    $sqlUpdate=mysqli_query($con,"INSERT INTO leave_signatories(leave_id,sign_first) VALUES('$id','$_SESSION[idno]')");

                }         
                if($sqlUpdate){
                    echo "<script>";
                        echo "alert('Leave successfully signed!');";
                        echo "window.location='?manageleaveapplication';";
                    echo "</script>";
                }else{
                    echo "<script>";
                        echo "alert('Unable to sign leave application!');";
                        echo "window.location='?manageleaveapplication';";
                    echo "</script>";
                }
            }

            if(isset($_GET['disapproved'])){
                $id=$_GET['id'];
                $sqlUpdate=mysqli_query($con,"UPDATE leave_application SET status='disapproved' WHERE id='$id'");         
                if($sqlUpdate){
                    $sqlUpdate=mysqli_query($con,"DELETE FROM leave_signatories WHERE leave_id='$id'");
                    echo "<script>";
                        echo "alert('Leave successfully disapproved!');";
                        echo "window.location='?manageleaveapplication';";
                    echo "</script>";
                }else{
                    echo "<script>";
                        echo "alert('Unable to disapprove leave application!');";
                        echo "window.location='?manageleaveapplication';";
                    echo "</script>";
                }
            }
            ?>