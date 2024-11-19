<?php
          $id=$_GET['id'];

          $sqlProfile=mysqli_query($con,"SELECT * FROM employee_profile WHERE id='$id'");
          $profile=mysqli_fetch_array($sqlProfile);
          $idno=$profile['idno'];
          $empname=$profile['lastname'].", ".$profile['firstname']." ".$profile['middlename']." ".$profile['suffix'];
          $birthdate=date('F d, Y',strtotime($profile['birthdate']));
          $nickname=$profile['nickname'];
          $gender=$profile['sex'];
          if($gender=="M"){
            $gender="Male";
          }else{
            $gender="Female";
          }
          $civilstatus=$profile['civilstatus'];
          if($civilstatus=="S"){
              $civilstatus="Single";
          }elseif($civilstatus=="M"){
              $civilstatus="Married";
          }else{
              $civilstatus="Widowed";
          }
          $eligibility=$profile['eligibility'];
          $address=$profile['address'];

          $sqlDetails=mysqli_query($con,"SELECT * FROM employee_details WHERE idno='$idno'");
          $details=mysqli_fetch_array($sqlDetails);
          $jobid=$details['designation'];
          $deptid=$details['department'];
          $compid=$details['company'];
          $status=$details['status'];
          $shift=date('h:i A',strtotime($details['startshift']))." - ".date('h:i A',strtotime($details['endshift']));
          $location=$details['location'];

          $eligible=date('m/d/Y',strtotime($details['dateleaveeffective']));

          $datehired=date('F d, Y',strtotime($details['dateofhired']));
          $dateregular=date('F d, Y',strtotime($details['dateofregular']));
          $datefulltime=date('F d, Y',strtotime($details['dateoffulltime']));


          $sqlJobTitle=mysqli_query($con,"SELECT * FROM jobtitle WHERE id='$jobid'");
          $jobtitle=mysqli_fetch_array($sqlJobTitle);
          $designation=$jobtitle['jobtitle'];

          $sqlDepartment=mysqli_query($con,"SELECT * FROM department WHERE id='$deptid'");
          $dept=mysqli_fetch_array($sqlDepartment);
          $department=$dept['department'];

          $sqlCompany=mysqli_query($con,"SELECT * FROM settings WHERE companycode='$compid'");
          $company=mysqli_fetch_array($sqlCompany);
          $companyname=$company['companyname'];

          $sqlBenefits=mysqli_query($con,"SELECT * FROM employee_benefits WHERE idno='$idno'");
          if(mysqli_num_rows($sqlBenefits)>0){
            $benefits=mysqli_fetch_array($sqlBenefits);
            $insurance=date('F d, Y',strtotime($benefits['insurance']));
            $hmo=date('F d, Y',strtotime($benefits['hmo']));
            $sss=$benefits['sss'];
            $tin=$benefits['tin'];
            $phic=$benefits['phic'];
            $hdmf=$benefits['hdmf'];
          }else{
            $insurance="";
            $hmo="";
            $sss="";
            $tin="";
            $phic="";
            $hdmf="";
          }


          $sqlChecklist=mysqli_query($con,"SELECT * FROM employee_checklist WHERE idno='$idno'");
          if(mysqli_num_rows($sqlChecklist)>0){
            $checklist=mysqli_fetch_array($sqlChecklist);
            $dateoriented=date('F d, Y',strtotime($checklist['dateoriented']));
            $tempid=$checklist['releasedtempid'];
            $permanentid=$checklist['releasedpermanentid'];
            $statuschecklist=$checklist['status'];
          }else{
            $dateoriented="";
            $tempid="";
            $permanentid="";
            $statuschecklist="";
          }

          $sqlContract=mysqli_query($con,"SELECT * FROM employee_contract WHERE idno='$idno'");
          if(mysqli_num_rows($sqlContract)>0){
            $contract=mysqli_fetch_array($sqlContract);
            $probationary=$contract['probationary']." / ".date('M-d-Y',strtotime($contract['probationarydate']));
            $regular=$contract['regular']." / ".date('M-d-Y',strtotime($contract['regulardate']));
            $fulltime=$contract['fulltime']." / ".date('M-d-Y',strtotime($contract['fulltimedate']));
          }else{
            $probationary="";
            $regular="";
            $fulltime="";
          }

          $sqlReferral=mysqli_query($con,"SELECT ep.lastname,ep.firstname,er.effectivity FROM employee_referral er LEFT JOIN employee_profile ep ON ep.idno=er.referredby WHERE er.idno='$idno'");
          if(mysqli_num_rows($sqlReferral)>0){
            $referral=mysqli_fetch_array($sqlReferral);
            $referredby=$referral['firstname']." ".$referral['lastname'];
            $effectivity=$referral['effectivity'];
          }else{
            $referredby="";
            $effectivity="";
          }
          $dhire=new DateTime($details['dateofregular']);
          $dnow=new DateTime(date('Y-m-d'));
          $interval=$dhire->diff($dnow);
          $years=$interval->y;
          $month=$interval->m;
          $days=$interval->d;
          $periodfrom=date('F d, Y',strtotime($years." years",strtotime($details['dateofregular'])));
          $periodto=date('F d, Y',strtotime('1 years',strtotime($periodfrom)));

          $sqlLeaveCredits=mysqli_query($con,"SELECT * FROM leave_credits WHERE idno='$idno'");
          if(mysqli_num_rows($sqlLeaveCredits)>0){
            $leave=mysqli_fetch_array($sqlLeaveCredits);
            $vl=$leave['vacationleave'];
            $vlused=$leave['vlused'];
            $sl=$leave['sickleave'];
            $slused=$leave['slused'];
            $pto=$leave['pto'];
            $ptoused=$leave['ptoused'];
            $vlrem=$vl-$vlused;
            $slrem=$sl-$slused;
            $ptorem=$pto-$ptoused;
          }else{
            $vl="";
            $vlused="";
            $sl="";
            $slused="";
            $pto="";
            $ptoused="";
            $vlrem="";
            $slrem="";
            $ptorem="";
          }
          ?>
        <div class="col-lg-12">
            <a href="?manageemployee"><h4><i class="fa fa-arrow-left"></i> Back</h4></a>
        </div>
          <div class="col-lg-12 mt">
            <div class="row content-panel">
              <div class="col-md-4 profile-text mt mb centered">
                <div class="right-divider hidden-sm hidden-xs">
                  <h4><?=$datehired;?></h4>
                  <h6>DATE HIRED</h6>
                  <h4><?=$dateregular;?></h4>
                  <h6>DATE OF REGULARIZATION</h6>
                  <h4><?=$datefulltime;?></h4>
                  <h6>DATE OF FULLTIME</h6>
                </div>
              </div>
              <!-- /col-md-4 -->
              <div class="col-md-4 profile-text">
                <h3><?=$empname;?></h3>
                <h6><?=$designation;?></h6>
                <p><?=$companyname;?></p>
                <br>
                <p>&nbsp;</p>
              </div>
              <!-- /col-md-4 -->
              <?php
              if(file_exists('../Employees/'.$idno.'.png')){
                $image="../Employees/".$idno.".png";
              }else{
                $image="../Employees/".$idno.".jpg";
              }
              ?>
              <div class="col-md-4 centered">
                <div class="profile-pic">
                  <p><img src="<?=$image;?>" class="img-circle"></p>
                  <p>
                    <!-- <button class="btn btn-theme"><i class="fa fa-check"></i> Follow</button>
                    <button class="btn btn-theme02">Add</button> -->
                    <?=$idno;?>
                  </p>
                </div>
              </div>
              <!-- /col-md-4 -->
            </div>
            <!-- /row -->
          </div>
          <!-- /col-lg-12 -->
          <div class="col-lg-12 mt">
            <div class="row content-panel">
              <div class="panel-heading">
                <ul class="nav nav-tabs nav-justified">
                  <li class="active">
                    <a data-toggle="tab" href="#overview">Overview</a>
                  </li>
                  <!-- <li>
                    <a data-toggle="tab" href="#contact" class="contact-map">Contact</a>
                  </li>
                  <li>
                    <a data-toggle="tab" href="#edit">Edit Profile</a>
                  </li> -->
                </ul>
              </div>
              <!-- /panel-heading -->
              <div class="panel-body">
                <div class="tab-content">
                  <div id="overview" class="tab-pane active">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="detailed">
                        <h4>Employee Information</h4>
                        <div class="col-lg-12">
                            <table width="100%">
                                <tr>
                                    <td width="15%">Nickname:</td>
                                    <td><i>"<?=$nickname;?>"</i></td>
                                </tr>
                                <tr>
                                    <td width="15%">Date of Birth:</td>
                                    <td><?=$birthdate;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Gender:</td>
                                    <td><?=$gender;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Civil Status:</td>
                                    <td><?=$civilstatus;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Eligibility:</td>
                                    <td><?=$eligibility;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Address:</td>
                                    <td><?=$address;?></td>
                                </tr>
                            </table>
                        </div>
                        </div>
                        <div class="detailed">
                        <h4>Employment Information</h4>
                        <div class="col-lg-12">
                            <table width="100%">
                                <tr>
                                    <td width="15%">Department:</td>
                                    <td><?=$department;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Status:</td>
                                    <td><?=$status;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Shift:</td>
                                    <td><?=$shift;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Work Location:</td>
                                    <td><?=$location;?></td>
                                </tr>
                            </table>
                        </div>
                        </div>
                        <div class="detailed">
                        <h4>Employee Benefits Information</h4>
                        <div class="col-lg-12">
                            <table width="100%">
                              <tr>
                                  <td width="15%">LC Effectivity:</td>
                                  <td><?=$eligible;?></td>
                              </tr>
                                <tr>
                                    <td width="25%">Life Insurance Effectivity:</td>
                                    <td><?=$insurance;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">HMO Effectivity:</td>
                                    <td><?=$hmo;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">SSS:</td>
                                    <td><?=$sss;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">TIN:</td>
                                    <td><?=$tin;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">PHIC:</td>
                                    <td><?=$phic;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">PAG-IBIG:</td>
                                    <td><?=$hdmf;?></td>
                                </tr>
                            </table>
                        </div>
                        </div>
                        <div class="detailed">
                        <h4>Employee Checklist</h4>
                        <div class="col-lg-12">
                            <table width="100%">
                                <tr>
                                    <td width="25%">Date Oriented:</td>
                                    <td><?=$dateoriented;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Temp ID:</td>
                                    <td><?=$tempid;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Permanent ID:</td>
                                    <td><?=$permanentid;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Requirement/s:</td>
                                    <td><?=$statuschecklist;?></td>
                                </tr>
                            </table>
                        </div>
                        </div>
                        <div class="detailed">
                        <h4>Employee Contract Status</h4>
                        <div class="col-lg-12">
                            <table width="100%">
                                <tr>
                                    <td width="25%">Probationary:</td>
                                    <td><?=$probationary;?></td>
                                </tr>
                            </table>
                        </div>
                        </div>
                        <div class="detailed">
                        <h4>Referral Information</h4>
                        <div class="col-lg-12">
                            <table width="100%">
                                <tr>
                                    <td width="25%">Referred By:</td>
                                    <td><?=$referredby;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Effectivity:</td>
                                    <td><?=$effectivity;?></td>
                                </tr>
                            </table>
                        </div>
                        </div>
                        <!-- /detailed -->
                      </div>
                      <!-- /col-md-6 -->
                      <div class="col-md-6 detailed">
                        <h4>Leave Credits</h4>
                        <div class="col-lg-12">
                            <table width="100%">
                                <tr>
                                    <td width="25%">Length of Service (yrs):</td>
                                    <td><?=$years;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Length of Service (months):</td>
                                    <td><?=$month;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Length of Service (days):</td>
                                    <td><?=$days;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Period (from):</td>
                                    <td><?=$periodfrom;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Period (through):</td>
                                    <td><?=$periodto;?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4 detailed">
                        <h4>Vacation Leave Credits</h4>
                        <div class="col-lg-12">
                            <table width="100%">
                                <tr>
                                    <td width="50%">Credits:</td>
                                    <td><?=$vl;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Used:</td>
                                    <td><?=$vlused;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Remaining:</td>
                                    <td><?=$vlrem;?></td>
                                </tr>
                            </table>
                        </div>
                      </div>
                      <div class="col-md-4 detailed">
                        <h4>Sick Leave Credits</h4>
                        <div class="col-lg-12">
                            <table width="100%">
                                <tr>
                                    <td width="50%">Credits:</td>
                                    <td><?=$sl;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Used:</td>
                                    <td><?=$slused;?></td>
                                </tr>
                                <tr>
                                    <td width="15%">Remaining:</td>
                                    <td><?=$slrem;?></td>
                                </tr>
                            </table>
                        </div>
                      </div>
                      <div class="col-md-4 detailed">
                        <h4>PTO Credits</h4>
                        <div class="col-lg-12">
                            <table width="100%">
                                <tr>
                                    <td width="50%">Credits:</td>
                                    <td><?=$pto;?></td>
                                </tr>
                                <tr>
                                    <td width="30%">Used:</td>
                                    <td><?=$ptoused;?></td>
                                </tr>
                                <tr>
                                    <td width="30%">Remaining:</td>
                                    <td><?=$ptorem;?></td>
                                </tr>
                            </table>
                        </div>
                      </div>
                      </div>



                      <!-- /col-md-6 -->
                    </div>
                    <!-- /OVERVIEW -->
                  </div>
                  <!-- /tab-pane -->
                </div>
                <!-- /tab-content -->
              </div>
              <!-- /panel-body -->
            </div>
            <!-- /col-lg-12 -->
          </div>
