<script type="text/javascript">
      function SubmitDetails(){        
          return confirm('Do you wish to submit details?');        
      }
    </script>
    <div class="row">
      <div class="col-lg-12">
      <h4 style="text-indent: 10px;"><a href="?main"><i class="fa fa-arrow-left"></i> BACK</a> | <i class="fa fa-road"></i> HMO & INSURANCE TRACKER</h4>      
    </div>
    </div>
    <form class="form-horizontal style-form" method="GET">
      <input type="hidden" name="insurancetracker">
    <div class="col-lg-4 mt">
            <div class="content-panel">
              <div class="panel-heading">                
                <input type="submit" name="submit" class="btn btn-primary" value="Proceed" style="float:right;">
              <h4><i class="fa fa-user"></i> EMPLOYEE HMO & INSURANCE</h4>
            </div>
            <div class="panel-body">                                                              
                <div class="form-group">
                  <label class="col-sm-3 col-sm-3 control-label">Company</label>
                  <div class="col-sm-7">
                    <select name="company" class="form-control" required>
                      <option value=""></option>
                      <?php
                      $sqlCompany=mysqli_query($con,"SELECT companycode,companyname FROM settings GROUP BY companycode");
                      if(mysqli_num_rows($sqlCompany)>0){
                          while($row = mysqli_fetch_array($sqlCompany)){
                            echo "<option value='$row[companycode]'>$row[companyname]</option>";
                          }                          
                      }
                      ?>                      
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 col-sm-3 control-label">Type</label>
                  <div class="col-sm-7">
                    <select name="type" class="form-control" required>
                      <option value="insurance">Life Insurance</option>
                      <option value="hmo">HMO</option>
                    </select>
                  </div>
                </div>                                 
            </div>
          </div>
          <!-- col-lg-12-->
        </div>                
        </form>        