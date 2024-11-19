<?php
include '../config.php';
session_start();
$userID = $_SESSION['idno'];
$sqlUserDetails=mysqli_query($con, "SELECT  * FROM employee_profile WHERE idno='$userID'");
if($sqlUserDetails && mysqli_num_rows($sqlUserDetails)>0){
    $UserDetails=mysqli_fetch_array($sqlUserDetails);
    $fullname=$UserDetails['lastname'].",".$UserDetails['firstname']." ".$UserDetails['middlename']." ".$UserDetails['suffix'];
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $leaveId = $_GET['id'];
    // Retrieve the leave application details
    $sqlLeaveDetails = mysqli_query($con, "SELECT * FROM leave_application WHERE id='$leaveId'");
    if ($sqlLeaveDetails && mysqli_num_rows($sqlLeaveDetails) > 0) {
        $leaveDetails = mysqli_fetch_array($sqlLeaveDetails);
        $leavetype = $leaveDetails['leavetype'];
        $numberofdays = $leaveDetails['numberofdays'];
        $dayfrom = $leaveDetails['dayfrom'];
        $dayto = $leaveDetails['dayto'];
        $reason = $leaveDetails['reason'];
        $leaveId = $leaveDetails['id'];
        $idno = $leaveDetails['idno'];
    } else {
        echo "<script>alert('Leave application not found!');</script>";
        echo "<script>window.location='?leaveapplication';</script>";
        return;
    }
} else {
    echo "<script>alert('Leave ID not provided!');</script>";
    echo "<script>window.location='?leaveapplication';</script>";
    return;
}

$sqlCredits = mysqli_query($con, "SELECT lc.*, la.* FROM leave_credits lc INNER JOIN leave_application la  WHERE la.idno=lc.idno AND la.id='$leaveId'");

$credits = []; 
if (mysqli_num_rows($sqlCredits) > 0) {
    $credit = mysqli_fetch_array($sqlCredits);
    $credits['VL'] = $credit['vacationleave'] - $credit['vlused'];
    $credits['SL'] = $credit['sickleave'] - $credit['slused'];
    $credits['PTO'] = $credit['pto'] - $credit['ptoused'];
    $credits['BLP'] = $credit['bdayleave'] - $credit['blp_used'];
    $credits['EO'] = $credit['earlyout'] - $credit['eo_used'];
}

$sqlProfile = mysqli_query($con, "SELECT la.*, ep.lastname, ep.firstname, ep.middlename, ep.suffix 
    FROM employee_profile ep 
    INNER JOIN leave_application la ON la.idno=ep.idno
    WHERE la.id='$leaveId'");
        if ($sqlProfile && mysqli_num_rows($sqlProfile) > 0) {
            $Profile = mysqli_fetch_array($sqlProfile);
            $name = $Profile['lastname'].",".$Profile['firstname']." ".$Profile['middlename']." ".$Profile['suffix'];
        }
?>

<div class="row">
    <div class="col-lg-12">
        <h4 style="text-indent: 10px;">
            <a href="?leaveapplication"><i class="fa fa-arrow-left"></i> BACK</a> | 
            <i class="fa fa-file-text"></i> LEAVE APPLICATION
        </h4>      
    </div>
</div>

<form class="form-horizontal" method="POST" action="overrideleaveapp.php" onsubmit="return checkCredits();">
    <input type="hidden" name="editleave">            
    <input type="hidden" name="addedby" value="<?=$fullname;?>">  
    <input type="hidden" name="id" value="<?=$leaveId;?>">          

    <div class="col-lg-4">
        <div class="content-panel">
            <div class="panel-heading">                
                <input type="submit" name="submit" class="btn btn-primary" value="Submit Details" style="float:right;">
                <h4><i class="fa fa-user" style="margin-right: 15px; margin-left: -10px;"></i><?=$name;?></h4>            
            </div>
            <div class="panel-body"> 
                <div class="form-group">
                    <label class="col-sm-4 control-label">Leave Type</label>
                    <div class="col-sm-8">
                        <select name="leavetype" class="form-control" required onchange="updateCredits(this.value)">
                        <option value="" disabled selected>Select Leave Type</option>
                            <option value="VL" <?= ($leavetype == 'VL') ? 'selected' : ''; ?>>Vacation Leave (VL)</option>
                            <option value="SL" <?= ($leavetype == 'SL') ? 'selected' : ''; ?>>Sick Leave (SL)</option>
                            <option value="PTO" <?= ($leavetype == 'PTO') ? 'selected' : ''; ?>>Unpaid Leave (PTO)</option>
                            <option value="MTL" <?= ($leavetype == 'MTL') ? 'selected' : ''; ?>>Maternity Leave (MTL)</option>
                            <option value="PTL" <?= ($leavetype == 'PTL') ? 'selected' : ''; ?>>Paternity Leave (PTL)</option>
                            <option value="SPL" <?= ($leavetype == 'SPL') ? 'selected' : ''; ?>>Solo Parent Leave (SPL)</option>
                            <option value="BL" <?= ($leavetype == 'BL') ? 'selected' : ''; ?>>Bereavement Leave (BL)</option>
                            <option value="MDL" <?= ($leavetype == 'MDL') ? 'selected' : ''; ?>>Medical Leave (MDL)</option>
                            <option value="LTL" <?= ($leavetype == 'LTL') ? 'selected' : ''; ?>>Long Term Leave (LTL)</option>
                            <option value="BLP" <?= ($leavetype == 'BLP') ? 'selected' : ''; ?>>Birthday Leave (BLP)</option>
                            <option value="EO" <?= ($leavetype == 'EO') ? 'selected' : ''; ?>>Early Out (EO)</option>
                        </select>
                        <small id="credit-info" class="form-text text-muted"></small>
                    </div>
                </div>
                
                <div class="form-group">
                  <label class="col-sm-4 control-label">No. of Days</label>
                  <div class="col-sm-4">
                      <input type="number" name="nofdays" id="nofdays" class="form-control" value="<?=$numberofdays;?>" required onchange="checkCredits();">
                  </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label">Start Date</label>
                    <div class="col-sm-8">
                        <input type="date" name="startDate" class="form-control" value="<?=$dayfrom;?>" required onchange="checkCredits();">
                        <span id="date-warning" style="color: red; display: none;">*You must file for leave at least 3 days in advance.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label">End Date</label>
                    <div class="col-sm-8">
                        <input type="date" name="endDate" class="form-control" value="<?=$dayto;?>" required onchange="checkCredits();">
                        <span id="end-date-warning" style="color: red; display: none;"></span>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-4 control-label">Reason(s)</label>
                    <div class="col-sm-8">
                        <textarea name="reasons" class="form-control" required><?=$reason;?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
if (isset($_POST['submit']) && isset($_POST['editleave']))  {
    $leaveId = $leaveDetails['id'];
    $idno = $_POST['idno'];
    $leavetype = $_POST['leavetype'];
    $nofdays = $_POST['nofdays'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];   
    $reasons = $_POST['reasons'];    
    $datenow = date('Y-m-d H:i:s'); 

    // Check if there's already an existing record for the selected leave type within the same date range
    $sqlCheck = mysqli_query($con, "SELECT * FROM leave_application 
                                 WHERE idno='$idno' 
                                 AND leavetype='$leavetype' 
                                 AND (
                                     (dayfrom <= '$startDate' AND dayto >= '$startDate') OR 
                                     (dayfrom <= '$endDate' AND dayto >= '$endDate')
                                 )");

    if (mysqli_num_rows($sqlCheck) > 0) {
        echo "<script>alert('Leave application already exists for the selected dates and leave type!');</script>";
    } else {
            // Insert the leave application
            $sqlInsertLeave = mysqli_query($con, "INSERT INTO leave_application 
            (idno, leavetype, numberofdays, dayfrom, dayto, reason, datearray, appstatus) 
            VALUES 
            ('$idno', '$leavetype', '$nofdays', '$startDate', '$endDate', '$reasons', '$datenow', 'Pending')");

            // Check if the leave application was successfully inserted
            if ($sqlInsertLeave) {
            // Update the used credits for the selected leave type in the leave_credits table
            switch ($leavetype) {
            case 'VL':
            $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits 
                          SET vlused = vlused + '$nofdays' 
                          WHERE idno = '$idno'");
            break;
            case 'SL':
            $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits 
                          SET slused = slused + '$nofdays' 
                          WHERE idno = '$idno'");
            break;
            case 'PTO':
            $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits 
                          SET ptoused = ptoused + '$nofdays' 
                          WHERE idno = '$idno'");
            break;
            case 'BLP':
            $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits 
                          SET blp_used = blp_used + '$nofdays' 
                          WHERE idno = '$idno'");
            break;
            case 'EO':
            $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits 
                          SET eo_used = eo_used + '$nofdays' 
                          WHERE idno = '$idno'");
            break;                                        
            default:
            echo "<script>alert('Leave type not recognized. No credits updated.');</script>";
            break;
            }

            // Check if credits were successfully updated
            if ($sqlUpdateCredits) {
            echo "<script>alert('Leave application submitted successfully and credits updated!');</script>";
            } else {
            echo "<script>alert('Failed to update leave credits. Please try again.');</script>";
            }
            } else {
            echo "<script>alert('Failed to submit leave application. Please try again.');</script>";
            }
        }
}
?>

<script>
function updateCredits(leaveType) {
    const credits = {
        VL: <?= isset($credits['VL']) ? $credits['VL'] : 0; ?>,
        SL: <?= isset($credits['SL']) ? $credits['SL'] : 0; ?>,
        PTO: <?= isset($credits['PTO']) ? $credits['PTO'] : 0; ?>,
        BLP: <?= isset($credits['BLP']) ? $credits['BLP'] : 0; ?>,
        EO: <?= isset($credits['EO']) ? $credits['EO'] : 0; ?>
    };

    let creditInfo = document.getElementById('credit-info');
    let nofdays = document.getElementById('nofdays');
    let startDate = document.getElementsByName('startDate')[0];
    let endDate = document.getElementsByName('endDate')[0];
    let reasonField = document.getElementsByName('reasons')[0];

    // Define leave types that should not be disabled even with 0 credits
    const excludedLeaveTypes = ['SPL', 'MTL', 'PTL', 'BL', 'MDL', 'LTL', 'EEO'];

    // Check if the selected leave type is in the excluded list
    if (excludedLeaveTypes.includes(leaveType)) {
        // Excluded leave type, enable all fields without restrictions
        creditInfo.textContent = ''; // Clear remaining credits message
        creditInfo.style.color = ''; // Reset color to default
        
        nofdays.disabled = false; // Enable the number of days field
        startDate.disabled = false; // Enable start date field
        endDate.disabled = false; // Enable end date field
        reasonField.disabled = false; // Enable reason field

        // Reset the attributes and styles
        nofdays.max = ''; // Remove max attribute
        nofdays.value = 1; // Default to 1 or set as required
        nofdays.style.backgroundColor = ''; // Reset background color to default
        startDate.style.backgroundColor = '';
        endDate.style.backgroundColor = '';
        reasonField.style.backgroundColor = '';
    } else if (credits[leaveType] !== undefined && credits[leaveType] > 0) {
        // Leave type has remaining credits, enable all fields and set max days
        creditInfo.textContent = `Remaining Credits: ${credits[leaveType]}`; // Update remaining credits message
        creditInfo.style.color = ''; // Reset color to default
        
        nofdays.disabled = false; // Enable the number of days field
        startDate.disabled = false; // Enable start date field
        endDate.disabled = false; // Enable end date field
        reasonField.disabled = false; // Enable reason field

        // Set max attribute of "No. of Days" to remaining credits
        nofdays.max = credits[leaveType];
        nofdays.value = 1;  // Default to 1
        nofdays.style.backgroundColor = ''; // Reset background color to default
        startDate.style.backgroundColor = '';
        endDate.style.backgroundColor = '';
        reasonField.style.backgroundColor = '';
    } else {
        // No remaining credits for selected leave type, disable all fields
        creditInfo.textContent = 'No available credits for this leave type.';
        creditInfo.style.color = 'red'; // Set text color to red

        nofdays.disabled = true; // Disable the number of days field
        startDate.disabled = true; // Disable start date field
        endDate.disabled = true; // Disable end date field
        reasonField.disabled = true; // Disable reason field

        // Optionally change the background color of disabled fields
        nofdays.style.backgroundColor = '#f0f0f0';
        startDate.style.backgroundColor = '#f0f0f0';
        endDate.style.backgroundColor = '#f0f0f0';
        reasonField.style.backgroundColor = '#f0f0f0';

        nofdays.max = 0; // Disable input for number of days
        nofdays.value = 0; // Set the value to 0
    }
}

function checkCredits() {
    console.log('Form submitted');
    console.log('Form data:', {
        leavetype: document.getElementsByName('leavetype')[0].value,
        nofdays: document.getElementById('nofdays').value,
        startDate: document.getElementsByName('startDate')[0].value,
        endDate: document.getElementsByName('endDate')[0].value,
        reasons: document.getElementsByName('reasons')[0].value,
    });
    let startDate = document.getElementsByName('startDate')[0];
    let endDate = document.getElementsByName('endDate')[0];
    let nofdays = document.getElementById('nofdays');
    let dateWarning = document.getElementById('date-warning');
    let endDateWarning = document.getElementById('end-date-warning');
    let selectedStartDate = new Date(startDate.value);
    let lastPossibleDate = new Date(selectedStartDate);
    let selectedLeaveType = document.querySelector('select[name="leavetype"]').value;

    // Check if startDate has a value
    /*if (!startDate.value) {
        dateWarning.style.display = 'inline';
        startDate.style.borderColor = 'red'; // Highlight input
        return false; // Prevent form submission
    }

     // Set current date and add 3 days to it
     let currentDate = new Date();
    let minStartDate = new Date(currentDate);
    minStartDate.setDate(minStartDate.getDate() + 3);
    
    let daysAdded = 0;
    while (daysAdded < 3) {
        lastPossibleDate.setDate(lastPossibleDate.getDate() - 1);
        if (lastPossibleDate.getDay() !== 0 && lastPossibleDate.getDay() !== 1) {
            daysAdded++;
        }
    }

    // Validate that the start date is at least 3 working days from today
    if (currentDate > lastPossibleDate) {
        dateWarning.style.display = 'inline';
        startDate.style.borderColor = 'red';
        return false;
    } else {
        // Hide the error message if the start date is valid
        dateWarning.style.display = 'none';
        startDate.style.borderColor = '';
    }*/

    // Check if number of days is 1
    if (nofdays.value == 1) {
        endDate.value = startDate.value;
        endDateWarning.style.display = 'none';
    } else {
        endDate.disabled = false; 
        updateEndDate(); 
    }

    let selectedMonth = selectedStartDate.getMonth() + 1; // JS months are 0-indexed
     // Check if it's a birthday leave (BLP) and if the application is within the user's birth month
    if (selectedLeaveType === 'BLP') {
        if (selectedMonth !== parseInt(userBirthdayMonth)) {
            creditInfo.textContent = 'Birthday Leave can only be applied within your birthday month.';
            creditInfo.style.color = 'red';
            return false;
        }
        
        if (<?= $credits['BLP'] ?? 0 ?> <= 0) { // Check if the user has birthday leave credits
            creditInfo.textContent = 'You do not have enough Birthday Leave credits.';
            creditInfo.style.color = 'red';
            return false;
        }
    }

    // Check if number of days is 1 (BLP is for one day only)
    if (nofdays.value == 1) {
        endDate.value = startDate.value;
        endDateWarning.style.display = 'none';
    } else {
        endDate.disabled = false;
        updateEndDate();
    }
    
    return true; // Proceed with form submission
}

function updateEndDate() {
    let startDate = document.getElementsByName('startDate')[0];
    let endDate = document.getElementsByName('endDate')[0];
    let nofdays = document.getElementById('nofdays');
    let endDateWarning = document.getElementById('end-date-warning');

    // Check if startDate has a value
    if (!startDate.value) {
        endDateWarning.textContent = '*Start date is required.';
        endDateWarning.style.display = 'inline';
        endDate.style.borderColor = 'red';
        return; // Exit the function if no value
    }

    // Convert start date value to a Date object
    let selectedStartDate = new Date(startDate.value);
    let totalDaysToAdd = parseInt(nofdays.value);

    let endDateValue = new Date(selectedStartDate); // Start from the selected start date
    let daysAdded = 0;

    // Start counting from the selected start date
    if (totalDaysToAdd > 0) {
        daysAdded = 1; // Count the first day (start date)

        // Loop to calculate the end date while skipping Sundays and Mondays
        while (daysAdded < totalDaysToAdd) {
            endDateValue.setDate(endDateValue.getDate() + 1); // Move to the next day
            // Check if it's a weekday (Tuesday to Saturday)
            if (endDateValue.getDay() !== 0 && endDateValue.getDay() !== 1) { // 0 = Sunday, 1 = Monday
                daysAdded++; // Count this day
            }
        }
    }

    // Convert expected end date to a format that matches the input type="date"
    let expectedEndDateStr = endDateValue.toISOString().split('T')[0]; // YYYY-MM-DD format

    // Set the calculated end date in the input field
    endDate.value = expectedEndDateStr;

    // Hide error if the end date is correctly calculated
    endDateWarning.style.display = 'none';
    endDate.style.borderColor = '';
}


function checkEndDate() {
    let startDate = document.getElementsByName('startDate')[0];
    let endDate = document.getElementsByName('endDate')[0];
    let endDateWarning = document.getElementById('end-date-warning');
    
    if (new Date(endDate.value) < new Date(startDate.value)) {
        endDateWarning.textContent = '*End date cannot be earlier than start date.';
        endDateWarning.style.display = 'inline';
        endDate.style.borderColor = 'red';
    } else {
        endDateWarning.style.display = 'none';
        endDate.style.borderColor = '';
    }
}
</script>

