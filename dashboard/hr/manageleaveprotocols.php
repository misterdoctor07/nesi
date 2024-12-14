<?php
$appofficer=$_GET['approvingofficer'];
$sqlProtocol=mysqli_query($con,"SELECT * FROM jobtitle WHERE id='$appofficer'");
if(mysqli_num_rows($sqlProtocol)>0){
    $app=mysqli_fetch_array($sqlProtocol);
    $appofficername=$app['jobtitle'];
}else{
    $appofficername="";
}
?>
<script type="text/javascript">
      function SubmitDetails(){        
          return confirm('Do you wish to submit details?');        
      }
    </script>
    <div class="row">
      <div class="col-lg-12">
      <h4 style="text-indent: 10px;"><a href="?leaveprotocols"><i class="fa fa-arrow-left"></i> BACK</a> | <i class="fa fa-file-text"></i> LEAVE PROTOCOLS</h4>      
    </div>
    </div>
    <form class="form-horizontal style-form" method="GET" onSubmit="return SubmitDetails();">
      <input type="hidden" name="manageleaveprotocols">            
      <input type="hidden" name="appofficer" value="<?=$appofficer;?>">          
    <div class="col-lg-10 mt">
            <div class="content-panel">
              <div class="panel-heading">                
                <input type="submit" name="submit" class="btn btn-primary" value="Save Details" style="float:right;">
              <h4><i class="fa fa-file-text"></i> UPDATE LEAVE PROTOCOLS</h4>            
            </div>
            <div class="panel-body">                                                        
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Approving Officer  </label>
                  <div class="col-sm-7">
                    <select name="approvingofficer" class="form-control" required>
                        <option value="<?=$appofficer;?>" selected><?=$appofficername;?></option>
                        <?php
                        $sqlEmployee=mysqli_query($con,"SELECT * FROM jobtitle");
                        if(mysqli_num_rows($sqlEmployee)>0){
                            while($employee=mysqli_fetch_array($sqlEmployee)){
                                echo "<option value='$employee[id]'>$employee[jobtitle]</option>";
                            }
                        }
                        ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Subordinates</label>
                  <div class="col-sm-5">
                    
                  </div>
                </div>
                <div class="form-group">
                  <!-- <label class="col-sm-4 col-sm-4 control-label"></label> -->
                  <div class="col-sm-12">
  <table width="100%" border="0">
    <tr>
      <?php
      $x = 1;

      // Fetch selected subordinates for the approving officer
      $selectedRequestors = [];
      $sqlSelected = mysqli_query($con, "SELECT requestingofficer FROM leave_protocols WHERE approvingofficer='$appofficer'");
      while ($row = mysqli_fetch_array($sqlSelected)) {
          $selectedRequestors[] = $row['requestingofficer'];
      }

      // Fetch all job titles
      $sqlJobtitle = mysqli_query($con, "SELECT * FROM jobtitle ORDER BY jobtitle ASC");
      if (mysqli_num_rows($sqlJobtitle) > 0) {
          while ($jobtitle = mysqli_fetch_array($sqlJobtitle)) {
              // Check if this jobtitle id is in the selectedRequestors array
              $status = in_array($jobtitle['id'], $selectedRequestors) ? "checked" : "";
              
              echo "<td><input type='checkbox' name='requestor[]' value='$jobtitle[id]' $status> $jobtitle[jobtitle]</td>";
              
              if ($x == 3) {
                  echo "</tr><tr>";
                  $x = 1;
              } else {
                  $x++;
              }
          }
      }
      ?>
    </tr>
  </table>
</div>

                </div>                
            </div>
          </div>
          <!-- col-lg-12-->
        </div>                
        </form>
  <?php
    if(isset($_GET['submit'])){               
        $addedby=$_GET['addedby'];
        $datenow=date('Y-m-d H:i:s');
        $approvingofficer=$_GET['approvingofficer'];  
        $requestor=$_GET['requestor'];
        $appofficer=$_GET['appofficer'];   
        if(sizeof($requestor)>0){
                $sqlCheck=mysqli_query($con,"SELECT * FROM leave_protocols WHERE approvingofficer='$appofficer'");
                if(mysqli_num_rows($sqlCheck)>0){
                    mysqli_query($con,"DELETE FROM leave_protocols WHERE approvingofficer='$appofficer'");
                    foreach($requestor AS $subordinate){
                        $table="leave_protocols(approvingofficer,requestingofficer)";
                        $values="VALUES('$approvingofficer','$subordinate')";
                        $sqlAddEmployee=mysqli_query($con,"INSERT INTO $table $values");
                    }
                }else{
                    foreach($requestor AS $subordinate){
                        $table="leave_protocols(approvingofficer,requestingofficer)";
                        $values="VALUES('$approvingofficer','$subordinate')";
                        $sqlAddEmployee=mysqli_query($con,"INSERT INTO $table $values");
                    }
                }
            if($sqlAddEmployee){
                echo "<script>";
                echo "alert('Details successfully saved!');window.location='?manageleaveprotocols&approvingofficer=$appofficer';";
                echo "</script>";
            }else{
                echo "<script>";
                echo "alert('Unable to saved details!');window.location='?manageleaveprotocols&approvingofficer=$appofficer';";
                echo "</script>";
            }
        }else{
                echo "<script>";
                echo "alert('Please select at least 1 item from the list!');window.history.back();";
                echo "</script>";
        }
    }
  ?>