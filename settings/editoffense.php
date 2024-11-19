    <?php
    $id=$_GET['id'];
    $sqlMemo=mysqli_query($con,"SELECT * FROM offense WHERE id='$id'");
    $memo=mysqli_fetch_array($sqlMemo);
    ?>
    <script type="text/javascript">
      function SubmitDetails(){
        var code=$('#companycode').val();        
        var name=$('#companyname').val();
        if(code==''){
          alert('Please enter Type of Memo!');   
          return false;       
        }else if(name==''){
          alert('Please enter Description!');   
          return false;       
        }else{
          return confirm('Do you wish to submit details?');
        }
      }
    </script>
    <div class="col-lg-6">
            <div class="content-panel">
              <div class="panel-heading">
              <h4><a href="?manageoffense"><i class="fa fa-arrow-left"></i> BACK</a> | <i class="fa fa-building-o"></i> UPDATE OFFENSE TYPE</h4>              
            </div>
            <div class="panel-body">              
              <form class="form-horizontal style-form" method="GET" onSubmit="return SubmitDetails();">
                <input type="hidden" name="editoffense">
                <input type="hidden" name="id" value="<?=$id;?>">
                <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">Title</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="companycode" id="companycode" value="<?=$memo['title'];?>">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">Description</label>
                  <div class="col-sm-10">
                    <textarea name="companyname" class="form-control" cols="20" rows="10" id="companyname"><?=$memo['description'];?></textarea>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">Points</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="points" id="points" value="<?=$memo['points'];?>">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">Instance</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="instance" id="points" value="<?=$memo['frequency'];?>">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">Category</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="category" id="points" value="<?=$memo['category'];?>">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">Infraction Points</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="penalty" id="points" value="<?=$memo['fpoints'];?>">
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-2 col-sm-2 control-label">&nbsp;</label>
                  <div class="col-sm-10">
                    <input type="submit" class="btn btn-primary" name="submit" value="Submit Details">
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- col-lg-12-->
        </div>      
    </div>
  <?php
    if(isset($_GET['submit'])){
      $id=$_GET['id'];
      $code=$_GET['companycode'];      
      $name=$_GET['companyname'];   
      $points=$_GET['points'];
      $instance=$_GET['instance'];
      $category=$_GET['category'];
      $penalty=$_GET['penalty'];
      $data="title='$code',description='$name',points='$points',frequency='$instance',category='$category',fpoints='$penalty'";
      $sqlAddCompany=mysqli_query($con,"UPDATE offense SET $data WHERE id='$id'");
      if($sqlAddCompany){
        echo "<script>";
          echo "alert('Details successfully updated!');window.location='?editoffense&id=$id';";
        echo "</script>";
      }else{
        echo "<script>";
          echo "alert('Unable to updated details!');window.location='?editoffense&id=$id';";
        echo "</script>";
      }
    }
  ?>