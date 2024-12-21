    <?php
    $sqlMemo=mysqli_query($con,"SELECT memonumber FROM infraction ORDER BY id DESC LIMIT 1");
   
    ?>
    <script type="text/javascript">
      function SubmitDetails(){        
          return confirm('Do you wish to submit details?');        
      }
    </script>
    <div class="row">
      <div class="col-lg-12">
      <h4 style="text-indent: 10px;"><a href="?manageinfraction"><i class="fa fa-arrow-left"></i> BACK</a> | <i class="fa fa-file-text"></i> EMPLOYEE INFRACTION</h4>      
    </div>
    </div>
    <form class="form-horizontal style-form" method="GET" onSubmit="return SubmitDetails();">
      <input type="hidden" name="addinfraction">            
      <input type="hidden" name="addedby" value="<?=$fullname;?>">          
    <div class="col-lg-4 mt">
            <div class="content-panel">
              <div class="panel-heading">                
                <input type="submit" name="submit" class="btn btn-primary" value="Save Details" style="float:right;">
              <h4><i class="fa fa-file-text"></i> ISSUE INFRACTION</h4>            
            </div>
            <div class="panel-body">                                            
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Employee</label>
                  <div class="col-sm-7">
                    <select name="idno" class="form-control" required>
                        <option value=""></option>
                        <?php
                        $sqlEmployee=mysqli_query($con,"SELECT ep.*,ed.* FROM employee_profile ep LEFT JOIN employee_details ed ON ed.idno=ep.idno WHERE ed.status NOT LIKE '%RESIGNED%'");
                        if(mysqli_num_rows($sqlEmployee)>0){
                            while($employee=mysqli_fetch_array($sqlEmployee)){
                                echo "<option value='$employee[idno]'>$employee[lastname], $employee[firstname]</option>";
                            }
                        }
                        ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Date Issued</label>
                  <div class="col-sm-5">
                    <input type="date" class="form-control" name="dateissued" value="<?=date('Y-m-d');?>">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Date Served</label>
                  <div class="col-sm-5">
                    <input type="date" class="form-control" name="dateserved">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Type of Memo</label>
                  <div class="col-sm-7">
                    <select name="memotype" class="form-control" required>
                        <option value=""></option>
                        <?php
                        $sqlEmployee=mysqli_query($con,"SELECT * FROM memo");
                        if(mysqli_num_rows($sqlEmployee)>0){
                            while($employee=mysqli_fetch_array($sqlEmployee)){
                                echo "<option value='$employee[title]'>$employee[title]</option>";
                            }
                        }
                        ?>
                    </select>
                  </div>
                </div>                
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Type of Offense</label>
                  <div class="col-sm-7">
                    <select name="typeofoffense" class="form-control" required>
                        <option value=""></option>
                        <?php
                        $sqlEmployee=mysqli_query($con,"SELECT * FROM offense WHERE title NOT LIKE '%Attendance%'");
                        if(mysqli_num_rows($sqlEmployee)>0){
                            while($employee=mysqli_fetch_array($sqlEmployee)){
                                echo "<option value='$employee[title]'>$employee[title]</option>";
                            }
                        }
                        ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Date of Incident</label>
                  <div class="col-sm-5">
                    <input type="date" class="form-control" name="dateofincident" value="<?=date('Y-m-d');?>">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Points</label>
                  <div class="col-sm-3">
                    <input type="text" class="form-control" name="points" style="text-align:center;">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Suspention Dates</label>
                  <div class="col-sm-7">
                    <textarea name="dateofsuspension" class="form-control" rows="5"></textarea>
                  </div>
                </div>
            </div>
          </div>
          <!-- col-lg-12-->
        </div>                
        </form>
        <?php
// Fetch the latest memonumber and increment it
$sqlMemo = mysqli_query($con, "SELECT memonumber FROM infraction ORDER BY id DESC LIMIT 1");
$lastMemo = mysqli_fetch_assoc($sqlMemo);
$lastMemoNumber = $lastMemo ? $lastMemo['memonumber'] : '24-0000';

// Extract the numeric part from the last memonumber and increment it
$lastNumber = (int)substr($lastMemoNumber, 3); // Remove the '24-' part and convert the remaining to integer
$nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT); // Increment and pad with leading zeros
$newMemoNumber = '24-' . $nextNumber;

// Handling form submission
if (isset($_GET['submit'])) {
    $idno = $_GET['idno'];
    $addedby = $_GET['addedby'];
    $datenow = date('Y-m-d H:i:s');
    $dateissued = $_GET['dateissued'];
    $dateserved = $_GET['dateserved'];
    $memotype = $_GET['memotype'];
    $memo = $newMemoNumber;  // Use the generated memonumber
    $typeofoffense = $_GET['typeofoffense'];
    $points = $_GET['points'];
    $dateofincident = $_GET['dateofincident'];
    $dateofsuspension = $_GET['dateofsuspension'];

    // Check if the memonumber already exists
    $sqlCheck = mysqli_query($con, "SELECT * FROM infraction WHERE memonumber='$memo'");
    if (mysqli_num_rows($sqlCheck) > 0) {
        echo "<script>";
        echo "alert('Memo number already in use!');window.history.back();";
        echo "</script>";
    } else {
        // Insert the new record into the database
        $table = "infraction(idno,dateissued,dateserved,typeofmemo,dateofincident,typeofoffense,points,memonumber,dateofsuspension,status,addedby,addeddatetime)";
        $values = "VALUES('$idno','$dateissued','$dateserved','$memotype','$dateofincident','$typeofoffense','$points','$memo','$dateofsuspension','pending','$addedby','$datenow')";
        $sqlAddEmployee = mysqli_query($con, "INSERT INTO $table $values");

        if ($sqlAddEmployee) {
            echo "<script>";
            echo "alert('Details successfully saved!');window.location='?addinfraction';";
            echo "</script>";
        } else {
            echo "<script>";
            echo "alert('Unable to save details!');window.location='?addinfraction';";
            echo "</script>";
        }
    }
}
?>
