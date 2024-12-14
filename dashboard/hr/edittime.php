<?php
    include '../config.php';

    $idno=$_GET['idno'];
    $id=$_GET['id'];
    $comp=$_GET['company'];
    $startdate=$_GET['startdate'];
    $enddate=$_GET['enddate'];

    $sqlCredits = mysqli_query($con, "SELECT * FROM leave_credits WHERE idno='$idno'");
    $credits = []; 
    if (mysqli_num_rows($sqlCredits) > 0) {
        $credit = mysqli_fetch_array($sqlCredits);
        $credits['VL'] = $credit['vacationleave'] - $credit['vlused'];
        $credits['SL'] = $credit['sickleave'] - $credit['slused'];
        $credits['PTO'] = $credit['pto'] - $credit['ptoused'];
        $credits['BLP'] = $credit['bdayleave'] - $credit['blp_used'];
        $credits['EO'] = $credit['earlyout'] - $credit['eo_used'];
    }
    $sqlAttendance=mysqli_query($con,"SELECT * FROM attendance WHERE id='$id'");
    if(mysqli_num_rows($sqlAttendance)>0){
        $attend=mysqli_fetch_array($sqlAttendance);
        $loginam=date('H:i',strtotime($attend['loginam']));
        $logoutam=date('H:i',strtotime($attend['logoutam']));
        $loginpm=date('H:i',strtotime($attend['loginpm']));
        $logoutpm=date('H:i',strtotime($attend['logoutpm']));
        $logindate=$attend['logindate'];
        $status=$attend['status'];
        $remarks=$attend['remarks'];
    }else{
        $loginam="";
        $logoutam="";
        $loginpm="";
        $logoutpm="";
        $logindate=$_GET['logindate'];
        $status="";
        $remarks="";
    }
            $work="";
            $rh="";
            $snwh="";
            $nd="";
            $leave="";
            $ot="";
            $pt="";
    //if(sizeof($status)>0){
        $stat=explode('/',$status);
        for($i=0;$i<sizeof($stat);$i++){
            if($stat[$i]=="work"){
                $work="checked";
            }
            if($stat[$i]=="rh"){
                $rh="checked";
            }
            if($stat[$i]=="snwh"){
                $snwh="checked";
            }
            if($stat[$i]=="nd"){
                $nd="checked";
            }
            if($stat[$i]=="leave"){
                $leave="checked";
            }
            if($stat[$i]=="ot"){
                $ot="checked";
            }
            if($stat[$i]=="pt"){
                $pt="checked";
            }
        }
    // }else{
    //         $work="";
    //         $rh="";
    //         $snwh="";
    //         $nd="";
    //         $leave="";
    //         $ot="";
    //         $pt="";
    // }
    ?>
    <script type="text/javascript">
      function SubmitDetails(){
          return confirm('Do you wish to submit details?');
      }
    </script>
    <div class="row">
      <div class="col-lg-12">
      <h4 style="text-indent: 10px;"><a href="?attendancemonitoring&view&company=<?=$comp;?>&startdate=<?=$startdate;?>&enddate=<?=$enddate;?>"><i class="fa fa-arrow-left"></i> BACK</a> | <i class="fa fa-money"></i> MANAGE TIME</h4>
    </div>
    </div>
    <form class="form-horizontal style-form" method="GET" onSubmit="return SubmitDetails();">
      <input type="hidden" name="edittime">
      <input type="hidden" name="addedby" value="<?=$fullname;?>">
      <input type="hidden" name="idno" value="<?=$idno;?>">
      <input type="hidden" name="id" value="<?=$id;?>">
      <input type="hidden" name="company" value="<?=$comp;?>">
      <input type="hidden" name="startdate" value="<?=$startdate;?>">
      <input type="hidden" name="enddate" value="<?=$enddate;?>">
    <div class="col-lg-4 mt">
            <div class="content-panel">
              <div class="panel-heading">
                <input type="submit" name="submit" class="btn btn-primary" value="Save Details" style="float:right;">
              <h4><i class="fa fa-clock-o"></i> ATTENDANCE DETAILS</h4>
            </div>
            <div class="panel-body">
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Shift 1</label>
                  <div class="col-sm-5">

                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Login</label>
                  <div class="col-sm-5">
                    <input type="time" class="form-control" name="loginam" required value="<?=$loginam;?>">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Logout</label>
                  <div class="col-sm-5">
                    <input type="time" class="form-control" name="logoutam" required value="<?=$logoutam;?>">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Shift 2</label>
                  <div class="col-sm-5">

                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Login</label>
                  <div class="col-sm-5">
                    <input type="time" class="form-control" name="loginpm" required value="<?=$loginpm;?>">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Logout</label>
                  <div class="col-sm-5">
                    <input type="time" class="form-control" name="logoutpm" required value="<?=$logoutpm;?>">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Log Date</label>
                  <div class="col-sm-5">
                    <input type="date" class="form-control" name="logindate" required value="<?=$logindate;?>">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Status</label>
                  <div class="col-sm-5">
                  <input type="checkbox" name="status[]" value="work" <?=$work;?>> Regular Work<br>
                    <input type="checkbox" name="status[]" value="rh" <?=$rh;?>> Regular Holiday<br>
                    <input type="checkbox" name="status[]" value="snwh" <?=$snwh;?>> Special Non-Working Holiday<br>
                    <input type="checkbox" name="status[]" value="nd" <?=$nd;?>> Night Differential<br>
                    <input type="checkbox" name="status[]" value="leave" <?=$leave;?>> Leave<br>
                    <input type="checkbox" name="status[]" value="ot" <?=$ot;?>> OT after 8 hours worked<br>
                    <input type="checkbox" name="status[]" value="pt" <?=$pt;?>> OT before 8 hours worked
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Leave Type</label>
                  <div class="col-sm-6">
                    <select name="leavetype" class="form-control" required>
                      <option value="<?=$remarks;?>"><?=$remarks;?></option>
                      <option value="P">P</option>
                      <option value="VL"> Vacation Leave (VL)</option>
                      <option value="SL" required> Sick Leave (SL)</option>
                      <option value="PTO" required> Unpaid Leave (PTO) </option>
                      <option value="MTL" required> Maternity Leave (MTL)</option>
                      <option value="PTL" required> Paternity Leave (PTL) </option>
                      <option value="SPL" required> Solo Parent Leave (SPL)</option>
                      <option value="BL" required> Bereavement Leave (BL) </option>
                      <option value="MDL" required> Medical Leave (MDL)</option>
                      <option value="LTL" required> Long Term Leave (LTL) </option>
                      <option value="BLP" required> Birthday Leave (BLP)</option>
                      <option value="EO" required> Early Out (EO) </option>
                      <option value="EEO" required> Emergency Early Out Leave (EEO)</option>
                    </select>
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
        $logindate=$_GET['logindate'];
        $loginam=$_GET['loginam'];
        $logoutam=$_GET['logoutam'];
        $loginpm=$_GET['loginpm'];
        $logoutpm=$_GET['logoutpm'];
        $idno=$_GET['idno'];
        $stat=$_GET['status'];
        $id=$_GET['id'];
        $startdate=$_GET['startdate'];
        $enddate=$_GET['enddate'];
        $leavetype=$_GET['leavetype'];
        $remarks=$_GET['remarks'];
        if(sizeof($stat)>0){
        $status="";
        foreach($stat AS $s){
            $status .=$s."/";
        }

        //Update Leave Credits Based on Leave Type 
        switch ($remarks) {
          case 'VL':
              $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits 
                                                      SET vlused = vlused + 1 
                                                      WHERE idno = '$idno'");
              break;
          case 'SL':
              $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits 
                                                      SET slused = slused + 1 
                                                      WHERE idno = '$idno'");
              break;
          case 'PTO':
              $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits 
                                                      SET ptoused = ptoused + 1 
                                                      WHERE idno = '$idno'");
              break;
          case 'BLP':
              $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits 
                                                      SET blp_used = blp_used + 1 
                                                      WHERE idno = '$idno'");
              break;
          case 'EO':
              $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits 
                                                      SET eo_used = eo_used + 1 
                                                      WHERE idno = '$idno'");
              break;                                        
          default:
              echo "<script>alert('Leave type not recognized. No credits updated.');</script>";
              break;
      }

        $sqlCheck=mysqli_query($con,"SELECT * FROM attendance WHERE id='$id'");
        if(mysqli_num_rows($sqlCheck)>0){
          $table="attendance";
          $values="SET loginam='$loginam',logoutam='$logoutam',loginpm='$loginpm',logoutpm='$logoutpm',status='$status',remarks='$leavetype' WHERE id='$id'";
          $sqlAddEmployee=mysqli_query($con,"UPDATE $table $values");
        }else{
            $table="attendance(idno,loginam,logoutam,loginpm,logoutpm,logindate,status,remarks)";
            $values="VALUES('$idno','$loginam','$logoutam','$loginpm','$logoutpm','$logindate','$status','$leavetype')";
            $sqlAddEmployee=mysqli_query($con,"INSERT INTO $table $values");
        }
      if($sqlAddEmployee){
          echo "<script>";
          echo "alert('Attendance successfully saved!');window.location='?edittime&idno=$idno&logindate=$logindate&id=$id&company=$comp&startdate=$startdate&enddate=$enddate';";
        echo "</script>";
      }else{
        echo "<script>";
          echo "alert('Unable to save details!');window.location='?edittime&idno=$idno&logindate=$logindate&id=$id&company=$comp&startdate=$startdate&enddate=$enddate';";
        echo "</script>";
      }
        }else{
        echo "<script>";
          echo "alert('Please select at least 1 status!');window.location='?edittime&idno=$idno&logindate=$logindate&id=$id&company=$comp&startdate=$startdate&enddate=$enddate';";
        echo "</script>";
    }
    }
  ?>
