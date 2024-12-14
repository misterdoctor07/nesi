    <?php
    $sqlMemo=mysqli_query($con,"SELECT memonumber FROM infraction ORDER BY id DESC LIMIT 1");
    if(mysqli_num_rows($sqlMemo)>0){
      $mem=mysqli_fetch_array($sqlMemo);
      $num=explode('-',$mem['memonumber']);
      $nextvalue=$num[1]+1;
      $memo=$num[0]."-".$nextvalue;
    }else{
      $memo=date('y')."-1000";
    }
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
                  <label class="col-sm-4 col-sm-4 control-label">Memo No.</label>
                  <div class="col-sm-5">
                    <input type="text" class="form-control" name="memonumber" value="<?=$memo;?>">
                  </div>
                </div>
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
                    <select name="offense" class="form-control" required>
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
                  <label class="col-sm-4 col-sm-4 control-label">Points</label>
                  <div class="col-sm-3">
                    <input type="text" class="form-control" name="points" style="text-align:center;">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-4 col-sm-4 control-label">Remarks</label>
                  <div class="col-sm-7">
                    <textarea name="datesuspension" class="form-control" rows="5"></textarea>
                  </div>
                </div>
            </div>
          </div>
          <!-- col-lg-12-->
        </div>                
        </form>
  <?php
    if(isset($_GET['submit'])){        
        $idno=$_GET['idno'];
        $addedby=$_GET['addedby'];
        $datenow=date('Y-m-d H:i:s');
        $dateissued=$_GET['dateissued'];
        $dateserved=$_GET['dateserved'];
        $memotype=$_GET['memotype'];  
        $memo=$_GET['memonumber'];  
        $offense=$_GET['offense'];      
        $points=$_GET['points'];   
        $datesuspension=$_GET['datesuspension'];     
        $sqlCheck=mysqli_query($con,"SELECT * FROM infraction WHERE memonumber='$memo'");
        if(mysqli_num_rows($sqlCheck)>0){
          echo "<script>";
          echo "alert('Memo number already in used!');window.history.back();";
        echo "</script>";
        }else{
            $table="infraction(idno,dateissued,dateserved,typeofmemo,typeofoffense,points,memonumber,dateofsuspension,status,addedby,addeddatetime)";
            $values="VALUES('$idno','$dateissued','$dateserved','$memotype','$offense','$points','$memo','$datesuspension','pending','$addedby','$datenow')";
            $sqlAddEmployee=mysqli_query($con,"INSERT INTO $table $values");
        }
      if($sqlAddEmployee){
        echo "<script>";
          echo "alert('Details successfully saved!');window.location='?addinfraction';";
        echo "</script>";
      }else{
        echo "<script>";
          echo "alert('Unable to saved details!');window.location='?addinfraction;";
        echo "</script>";
      }
    }
  ?>