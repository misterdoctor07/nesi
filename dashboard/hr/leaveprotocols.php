<div class="col-lg-12">
            <div class="content-panel">
              <div class="panel-heading">
              <h4><a href="?main"><i class="fa fa-arrow-left"></i> HOME</a> | <i class="fa fa-file-text"></i> LEAVE PROTOCOLS<div style="float:right;"><a href="?addleaveprotocols" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add Leave Protocol</a></div></h4>
            </div>
              <div class="panel-body">
                <table class="table table-bordered table-striped table-condensed">
                  <thead>
                    <tr>
                      <th width="3%">No.</th>
                      <th>Approving Officer</th>
                      <th>Subordinates</th>                      
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $x=1;
                      $sqlEmployee=mysqli_query($con,"SELECT lp.*,jt.jobtitle FROM leave_protocols lp INNER JOIN jobtitle jt ON jt.id=lp.approvingofficer GROUP BY lp.approvingofficer ORDER BY approvingofficer ASC");
                      if(mysqli_num_rows($sqlEmployee)>0){
                        while($company=mysqli_fetch_array($sqlEmployee)){                          
                          $approvingofficer=$company['jobtitle'];
                          $appofficer=$company['approvingofficer'];
                          $requestor="";
                          $sqlDept=mysqli_query($con,"SELECT lp.*,jt.jobtitle FROM leave_protocols lp INNER JOIN jobtitle jt ON jt.id=lp.requestingofficer WHERE lp.approvingofficer='$appofficer'");
                          if(mysqli_num_rows($sqlDept)>0){
                            while($dept=mysqli_fetch_array($sqlDept)){
                                $requestor .=$dept['jobtitle'].", ";
                            }
                          }           
                          echo "<tr>";
                            echo "<td>$x.</td>";
                            echo "<td>$approvingofficer</td>";
                            echo "<td>$requestor</td>";                            
                            ?>
                            <td align="center">                            
                              <a href="?manageleaveprotocols&approvingofficer=<?=$appofficer;?>" class="btn btn-success btn-xs" title="Edit Leave Protocol"><i class='fa fa-pencil'></i></a>
                              <a href="?leaveprotocols&approvingofficer=<?=$appofficer;?>&delete" class="btn btn-primary btn-xs" title="Delete Leave Protocol" onclick="return confirm('Do you wish to remove this item?'); return false;"><i class='fa fa-trash'></i></a>                              
                            </td>
                            <?php
                          echo "</tr>";
                          $x++;
                        }
                      }else{
                        echo "<tr><td colspan='4' align='center'>No record found!</td></tr>";
                      }
                    ?>
                  </tbody>
                </table>              
                </div>  
            </div>
            </div>                    
            <?php
            if(isset($_GET['delete'])){
              $appofficer=$_GET['approvingofficer'];              
              $sqlDelete=mysqli_query($con,"DELETE FROM leave_protocols WHERE approvingofficer='$appofficer'");
              if($sqlDelete){
                echo "<script>alert('Leave protocol successfully removed!');window.location='?leaveprotocols';</script>";
              }else{
                echo "<script>alert('Unable to remove leave protocol!');window.location='?leaveprotocols';</script>";
              }
            }            
            ?>