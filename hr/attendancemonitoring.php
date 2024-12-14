<?php
  $comp=$_GET['company'];
  $startdate=$_GET['startdate'];
  $enddate=$_GET['enddate'];
  $sqlName=mysqli_query($con,"SELECT companyname FROM settings WHERE companycode='$comp'");
  $companyname=mysqli_fetch_array($sqlName);
?>

<?php
if(isset($_GET['edit'])){
    $comp=$_GET['company'];
    $startdate=$_GET['startdate'];
    $enddate=$_GET['enddate'];
    $idno=$_GET['idno'];
    $logindate=$_GET['logindate'];
    $sqlProfile=mysqli_query($con,"SELECT * FROM points WHERE idno='$idno' AND logindate='$logindate'");
    if(mysqli_num_rows($sqlProfile)>0){
      $point=mysqli_fetch_array($sqlProfile);
      $logindate=$point['logindate'];
      $offense=$point['offense'];
      $point_id=$point['id'];
    }else{
      $offense="";
      $point_id="";
    }
    $sqlOffense=mysqli_query($con,"SELECT * FROM offense WHERE id='$offense'");
    if(mysqli_num_rows($sqlOffense)>0){
      $off=mysqli_fetch_array($sqlOffense);
      $offdescription=$off['description'];
    }else{
      $offdescription="";
    }
?>

<div class="col-lg-4 mt">
  <div class="content-panel">
    <div class="panel-heading">
      <h4>
        <a href="javascript:history.back();"><i class="fa fa-times"></i></a> | Manage Attendance Infractions
      </h4>
    </div>
    <div class="panel-body">
        <form name="f2" method="GET">
          <input type="hidden" name="attendancemonitoringsummary">
          <input type="hidden" name="company" value="<?=$comp;?>">
          <input type="hidden" name="startdate" value="<?=$startdate;?>">
          <input type="hidden" name="enddate" value="<?=$enddate;?>">
          <input type="hidden" name="idno" value="<?=$idno;?>">
          <input type="hidden" name="logindate" value="<?=$logindate;?>">
          <input type="hidden" name="point_id" value="<?=$point_id;?>">
          <div class="form-group">
            <label>Date</label>
              <input type="date" class="form-control" value="<?=$logindate;?>" disabled>
          </div>
          <div class="form-group">
            <label>Offense</label>
              <select class="form-control" name="offense" required>
                <option value="<?=$offense;?>"><?=$offdescription;?></option>
                <?php
                $sqlOffense=mysqli_query($con,"SELECT * FROM offense WHERE title LIKE '%Attendance%'");
                if(mysqli_num_rows($sqlOffense)>0){
                  while($off=mysqli_fetch_array($sqlOffense)){
                    echo "<option value='$off[id]'>$off[description]</option>";
                  }
                }
                ?>
              </select>
            </div>
            <div class="form-group">
              <input type="submit" value="submit" name="submitInfraction" class="btn btn-primary">
          </div>
        </form>
    </div>
  </div>
</div>

<?php
  }
?>

<?php
  //Adding Infraction
  if(isset($_GET['submitInfraction'])){
      $comp=$_GET['company'];
      $startdate=$_GET['startdate'];
      $enddate=$_GET['enddate'];
      $idno=$_GET['idno'];
      $logindate=$_GET['logindate'];
      $offense=$_GET['offense'];
      $point_id=$_GET['point_id'];
      $firstStartMonth=date('Y')."-01-01";
      $firstEndMonth=date('Y')."-06-30";
      $secondStartMonth=date('Y')."-07-01";
      $secondEndMonth=date('Y')."-12-31";
      $sqlOffense=mysqli_query($con,"SELECT * FROM offense WHERE id='$offense'");
      $off=mysqli_fetch_array($sqlOffense);
      $freq=0;
      $penalty=$off['fpoints'];
      $frequency=$off['frequency']-1;
      $sqlCheckInstance=mysqli_query($con,"SELECT * FROM offense WHERE category='$off[category]'");
      if(mysqli_num_rows($sqlCheckInstance)>0){
        while($ins=mysqli_fetch_array($sqlCheckInstance)){
          if(strtotime($logindate)>=strtotime($firstStartMonth) && strtotime($logindate)<=strtotime($firstEndMonth)){
            $sqlInstance=mysqli_query($con,"SELECT * FROM points WHERE logindate BETWEEN '$firstStartMonth' AND '$firstEndMonth' AND offense='$ins[id]' AND idno='$idno'");
            $freq +=mysqli_num_rows($sqlInstance);
          }elseif(strtotime($logindate)>=strtotime($secondStartMonth) && strtotime($logindate)<=strtotime($secondEndMonth)){
            $sqlInstance=mysqli_query($con,"SELECT * FROM points WHERE logindate BETWEEN '$secondStartMonth' AND '$secondEndMonth' AND offense='$offense' AND idno='$idno'");
            $freq +=mysqli_num_rows($sqlInstance);
          }
        }
      }
      $points=0;
      $sqlPointCheck=mysqli_query($con,"SELECT * FROM points WHERE id='$point_id'");
      if(mysqli_num_rows($sqlPointCheck)>0){
        $pointcheck=mysqli_fetch_array($sqlPointCheck);
        // if($freq >=$frequency ){
          $points=$pointcheck['points'];
        // }else{
        //   $points=$off['points'];
        // }
        $sqlInsert=mysqli_query($con,"UPDATE points SET points='$points',offense='$offense' WHERE id='$point_id'");
      }else{
        if($freq >=$frequency ){
          $points=$off['points']+$penalty;
        }else{
          $points=$off['points'];
        }
        $sqlInsert=mysqli_query($con,"INSERT INTO points(idno,logindate,points,offense) VALUES('$idno','$logindate','$points','$offense')");
      }

      if($sqlInsert){
        $sqlRemarks=mysqli_query($con,"UPDATE attendance SET remarks='$code' WHERE logindate='$logindate' AND idno='$idno'");
        echo "<script>window.history.back();</script>";
      }else{
        echo "<script>alert('Unable to insert infraction!');window.history.back();</script>";
      }
  }
  
  //Delete Time
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
      echo "alert('Item successfully removed!');window.history.back();</script>";
    echo "</script>";
  }else{
    echo "<script>";
      echo "alert('Unable to delete time!');window.history.back();</script>";
    echo "</script>";
    }
  }
  
  // Delete the infraction
  $sqlDelete = mysqli_query($con, "DELETE FROM points WHERE id='$id'");
  if (!$sqlDelete) {
      die("Error deleting infraction: " . mysqli_error($con));
  }

  if ($sqlDelete) {
      // Mark attendance as manually reviewed and update remarks
      $sqlUpdate = mysqli_query($con, 
          "UPDATE attendance 
           SET remarks='P'
           WHERE logindate='$logindate' AND idno='$idno'"
      );

      if (!$sqlUpdate) {
          die("Error updating attendance: " . mysqli_error($con));
      }

      if ($sqlUpdate) {
          echo "<script>";
          echo "alert('Infraction successfully removed!');window.history.back();</script>";
          echo "</script>";
      }
  } else {
      echo "<script>";
      echo "alert('Unable to remove infraction!');window.history.back();</script>";
      echo "</script>";
  }

?>
