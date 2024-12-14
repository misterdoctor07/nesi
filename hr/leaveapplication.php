<?php
    // Fetch unique companies from the employee_details table
    $sqlCompanies = mysqli_query($con, "SELECT DISTINCT company FROM employee_details ORDER BY company");

    if (!$sqlCompanies) {
        echo "Query error: " . mysqli_error($con);
    }
?>

<div class="col-lg-12">
    <div class="content-panel">
        <div class="panel-heading">
            <h4>
                <a href="?main"><i class="fa fa-arrow-left"></i> HOME</a> | 
                <i class="fa fa-suitcase"></i> LEAVE APPLICATION
            </h4>
        </div>

        <!-- Company Tabs -->
        <ul class="nav nav-tabs">
            <?php
                $active = 'active'; // Set the first tab as active
                while ($company = mysqli_fetch_array($sqlCompanies)) {
                    $companyCode = $company['company'];
                    
                    // Fetch count of pending leave applications for the company
                    $sqlCount = mysqli_query($con, "SELECT COUNT(*) AS total FROM leave_application la
                        INNER JOIN employee_details ed ON la.idno = ed.idno
                        WHERE ed.company = '$companyCode' 
                        AND la.appstatus NOT IN ('Pending', 'Cancelled', 'Disapproved') 
                        AND la.remarks != 'POSTED'");
                    $count = mysqli_fetch_assoc($sqlCount)['total'];
                    
                    echo "<li class='$active' style='position: relative;'>
                            <a data-toggle='tab' href='#tab-$companyCode'>$companyCode";
                    if ($count > 0) {
                        echo "<span class='badge badge-right'>$count</span>";
                    }
                    echo "</a></li>";
                    $active = ''; // Remove active class from subsequent tabs
                }
            ?>
        </ul>


        <div class="tab-content">
            <?php
                // Reset the result pointer to reuse it
                mysqli_data_seek($sqlCompanies, 0);
                $active = 'in active'; // Set the first tab content as active
                while ($company = mysqli_fetch_array($sqlCompanies)) {
                    $companyCode = $company['company'];
                    echo "<div id='tab-$companyCode' class='tab-pane fade $active'>";

                    // Fetch unique departments for the company
                    $sqlDepartments = mysqli_query($con, "SELECT DISTINCT d.department FROM employee_details ed
                        INNER JOIN department d ON d.id = ed.department
                        WHERE ed.company = '$companyCode' ORDER BY d.department");

                    echo "<ul class='nav nav-pills' style='margin-top: 10px;'>";
                    $deptActive = 'active';
                    
                    while ($department = mysqli_fetch_array($sqlDepartments)) {
                        $departmentName = $department['department'];
                        
                        // Fetch count of pending leave applications for the department
                        $sqlDeptCount = mysqli_query($con, "SELECT COUNT(*) AS total FROM leave_application la
                            INNER JOIN employee_details ed ON la.idno = ed.idno
                            INNER JOIN department d ON d.id = ed.department
                            WHERE ed.company = '$companyCode' 
                            AND d.department = '$departmentName'
                            AND la.appstatus NOT IN ('Pending', 'Cancelled', 'Disapproved') 
                            AND la.remarks != 'POSTED'");
                        $deptCount = mysqli_fetch_assoc($sqlDeptCount)['total'];

                        // Assign unique ID using company and department names
                        $deptId = preg_replace('/[^A-Za-z0-9\-]/', '', $departmentName); // Remove special characters

                        // Add department tab with badge in top-right corner
                        echo "<li class='$deptActive' style='position: relative;'><a data-toggle='pill' href='#dept-$companyCode-$deptId'>$departmentName";
                        if ($deptCount > 0) {
                            echo "<span class='badge badge-right'>$deptCount</span>";
                        }
                        echo "</a></li>";
                        $deptActive = ''; // Remove active class from subsequent department tabs
                    }
                    echo "</ul>";

                    echo "<div class='tab-content' style='margin-top: 10px;'>";
                    mysqli_data_seek($sqlDepartments, 0);
                    $deptActive = 'in active';

                    // Department Content
                    while ($department = mysqli_fetch_array($sqlDepartments)) {
                        $departmentName = $department['department'];
                        $deptId = preg_replace('/[^A-Za-z0-9\-]/', '', $departmentName); // Remove special characters
                        // Use unique ID for department tabs
                        echo "<div id='dept-$companyCode-$deptId' class='tab-pane fade $deptActive'>";

                        // Fetch employees based on company and department
                        $sqlEmployee = mysqli_query($con, "SELECT la.*, la.id as laid, ep.*, ed.*, d.department 
                            FROM leave_application la
                            INNER JOIN employee_profile ep ON ep.idno = la.idno 
                            INNER JOIN employee_details ed ON ed.idno = ep.idno
                            INNER JOIN department d ON d.id = ed.department 
                            WHERE ed.company = '$companyCode' AND d.department = '$departmentName' 
                            ORDER BY 
                                CASE 
                                    WHEN la.appstatus NOT IN ('Pending', 'Cancelled', 'Disapproved') AND la.remarks != 'POSTED' THEN 1 
                                    ELSE 2 
                                END,
                            la.datearray DESC");

            ?>
                    <table class="table table-bordered table-striped table-condensed" id="hidden-table-info">
                        <thead>
                            <tr>
                                <th width="2%" style="text-align: center;">No.</th>
                                <th width="6%" style="text-align: center;">Employee ID</th>
                                <th width="8%" style="text-align: center;">Employee Name</th>
                                <th width="6%" style="text-align: center;">Leave Type</th>
                                <th width="6%" style="text-align: center;">No. of Days</th>
                                <th width="5%" style="text-align: center;">From</th>
                                <th width="5%" style="text-align: center;">To</th>
                                <th style="text-align: center;">Reason</th>
                                <th width="7%" style="text-align: center;">Date Applied</th>
                                <th width="6%" style="text-align: center;">Status</th>
                                <th style="text-align: center;">Remarks</th>
                                <th width="6%" style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $x = 1;

                        if (!$sqlEmployee) {
                            echo "Error: " . mysqli_error($con);
                        } elseif (mysqli_num_rows($sqlEmployee) > 0) {
                            while ($emp = mysqli_fetch_array($sqlEmployee)) {
                                ?>
                                <tr>
                                    <td align="center"><?= $x++; ?>.</td>
                                    <td align="center"><?= $emp['idno']; ?></td>
                                    <td align="center"><?= $emp['lastname'] . ', ' . $emp['firstname']; ?></td>
                                    <td align="center"><?= $emp['leavetype']?></td>
                                    <td align="center"><?= $emp['numberofdays']?></td>
                                    <td align="center"><?= date('m/d/Y', strtotime($emp['dayfrom'])); ?></td>
                                    <td align="center"><?= date('m/d/Y', strtotime($emp['dayto'])); ?></td>
                                    <td align='left'><?= $emp['reason'] ?></td>
                                    <td align='left'><?= date('m/d/Y', strtotime($emp['datearray'])) ?></td>
                                    <td align='left'><?= $emp['appstatus'] ?></td>
                                    <td align='left'><?=$emp['remarks'];?></td>
                                    <td align="center">
                                        <?php if ($emp['remarks'] != 'POSTED'): ?>
                                            <?php if ($emp['appstatus'] != 'Disapproved' && $emp['appstatus'] != 'Cancelled' && $emp['appstatus'] != 'Pending'): ?>
                                                <a href="?leaveapplication&post&id=<?=$emp['laid'];?>&remarks=<?=$emp['remarks'];?>" 
                                                   class="btn btn-success btn-xs confirm-post" 
                                                   title="Post">
                                                    <i class='fa fa-upload'></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="?leaveapplication&addremarks&id=<?=$emp['laid'];?>&remarks=<?=$emp['remarks'];?>" 
                                               class="btn btn-primary btn-xs"
                                               title="Remarks">
                                                <i class='fa fa-edit'></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='12' align='center'>No leave applications found.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>

                    <?php
                        echo "</div>"; // End of department tab content
                        $deptActive = ''; // Remove active class from subsequent department contents
                    }
                    echo "</div>"; // End of department tabs content

                    echo "</div>"; // End of company tab content
                    $active = ''; // Remove active class from subsequent company contents
                }
                    ?>
        </div>
    </div>
</div>

<!-- Ensure Bootstrap JS and jQuery are included -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<script>
    // Select all buttons with the "confirm-action" class
    const confirmButtons = document.querySelectorAll('.confirm-post');

    // Loop through each button and add a click event listener
    confirmButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            // Display the confirmation dialog
            const confirmAction = confirm("Are you sure you want to POST this leave?");
            
            // If the user clicks "Cancel", prevent the link's default action
            if (!confirmAction) {
                event.preventDefault();
            }
        });
    });
</script>

<?php
if (isset($_GET['post'])) {
    $id = $_GET['id'];
    
    // Sanitize the input
    $id = mysqli_real_escape_string($con, $id);
    
    // Initialize $startshift with a default value
    $startshift = null;
    
    // Retrieve shift
    $sqlShift = mysqli_query($con, "SELECT startshift FROM employee_details ed 
    INNER JOIN leave_application la ON ed.idno = la.idno WHERE la.id = '$id'");




    if ($sqlShift && mysqli_num_rows($sqlShift) > 0) {
        $startshift = mysqli_fetch_assoc($sqlShift)['startshift'];
    } else {
        echo "Error: Could not retrieve start shift for employee with ID $id.";
    }

    // Retrieve leave credits
    $sqlCredits = mysqli_query($con, "SELECT * FROM leave_credits lc INNER JOIN leave_application la ON lc.idno = la.idno WHERE la.id='$id'");
    if ($sqlCredits && mysqli_num_rows($sqlCredits) > 0) {
        $credits = mysqli_fetch_assoc($sqlCredits)['credits'];
    }

    // Retrieve leave application details
    $sqlRetrieve = mysqli_query($con, "SELECT * FROM leave_application WHERE id='$id'");
    if ($sqlRetrieve && mysqli_num_rows($sqlRetrieve) > 0) {
        $leaveData = mysqli_fetch_array($sqlRetrieve);
        $leaveType = $leaveData['leavetype'];
        $numberOfDays = $leaveData['numberofdays'];
        $idno = $leaveData['idno']; 
        $startdate = $leaveData['dayfrom'];
        $enddate = $leaveData['dayto'];

        $start = new DateTime($startdate);
        $end = new DateTime($enddate);

        // Add 1 day to the end date to make it inclusive
        $end->modify('+1 day');

        $interval = new DateInterval('P1D');  // 1-day interval
        $dateRange = new DatePeriod($start, $interval, $end);

        $dateArray = [];
        $daysAdded = 0;

        // Define if night shift (add check for $startshift to avoid undefined error)
        $isNightShift = ($startshift && ($startshift == '23:00:00' || $startshift == '00:00:00'));
        
        foreach ($dateRange as $date) {
            if ($daysAdded >= $numberOfDays) {
                break;
            }

            $dayOfWeek = $date->format('N');

            // Skip Sundays for all shifts
            if ($dayOfWeek == 7) {
                continue;
            }

            // Skip Mondays if it’s a night shift
            if ($dayOfWeek == 1 && $isNightShift) {
                continue;
            }

            // Add valid date to array
            $dateArray[] = $date->format('Y-m-d');
            $daysAdded++;
        }


        // Update leave application status
        $sqlUpdate = mysqli_query($con, "UPDATE leave_application SET remarks='POSTED' WHERE id='$id'");

        if ($sqlUpdate) {
            foreach ($dateArray as $leaveDate) {
                // Check if the date exists in attendance
                $sqlCheckAttendance = mysqli_query($con, 
                    "SELECT * FROM attendance WHERE idno = '$idno' AND logindate = '$leaveDate'");
                
                if (mysqli_num_rows($sqlCheckAttendance) == 0) {
                    // Insert new attendance row if date doesn't exist
                    $sqlInsertAttendance = mysqli_query($con, 
                        "INSERT INTO attendance (idno, logindate, loginam, logoutam, loginpm, logoutpm, remarks) 
                        VALUES ('$idno', '$leaveDate', '0', '0', '0', '0', '$leaveType')");
                    
                    if (!$sqlInsertAttendance) {
                        echo "<script>alert('Error inserting new attendance record for date: $leaveDate');</script>";
                    }
                } else {
                    // Update existing attendance row
                    $sqlUpdateAttendRem = mysqli_query($con, 
                        "UPDATE attendance SET remarks = '$leaveType' WHERE idno = '$idno' AND logindate = '$leaveDate'");
                    
                    if (!$sqlUpdateAttendRem) {
                        echo "<script>alert('Error updating attendance for date: $leaveDate');</script>";
                    }
                }

                // Update leave credits based on leave type
                switch ($leaveType) {
                    case 'VL':
                        $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits SET vlused = vlused + 1 WHERE idno = '$idno'");
                        break;
                    case 'SL':
                        $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits SET slused = slused + 1 WHERE idno = '$idno'");
                        break;
                    case 'PTO':
                        $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits SET ptoused = ptoused + 1 WHERE idno = '$idno'");
                        break;
                    case 'BLP':
                        $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits SET blp_used = blp_used + 1 WHERE idno = '$idno'");
                        break;
                    case 'EO':
                        $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits SET eo_used = eo_used + 1 WHERE idno = '$idno'");
                        break;
                    case 'SPL':
                        $sqlUpdateCredits = mysqli_query($con, "UPDATE leave_credits SET spl_used = spl_used + 1 WHERE idno = '$idno'");
                        break;
                    default:
                        echo "<script>alert('Leave type not recognized. No credits updated.');</script>";
                        break;
                }

                // Update attendance status to 'leave'
                $sqlupdateStatus = mysqli_query($con, "UPDATE attendance SET status='leave' WHERE idno='$idno'");
            }

            // Final success message
            echo "<script>alert('Leave application successfully posted!'); window.location='?leaveapplication';</script>";
        } else {
            echo "<script>alert('Unable to post leave application!'); window.location='?leaveapplication';</script>";
        }
    }
}
?>


<?php 
// Check if the user clicked 'Add Remarks'
if (isset($_GET['addremarks'])) {
    $id = $_GET['id'];
    $remarks = urldecode($_GET['remarks']); // Use urldecode to handle special characters
?>
    <!-- Remarks Form -->
    <div class="modal-overlay">
    <div class="modal-container">
        <div class="content-panel">
            <div class="panel-heading-">
                <h4>
                    <a href="?leaveapplication"><i class="fa fa-arrow-left"></i> Close</a> |
                    <i class="fa fa-file-text"></i> REMARKS
                </h4>
            </div>
            <div class="panel-body">
                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?= $id; ?>">
                    <div class="form-group">
                        <textarea name="remarks" class="form-control" rows="5" placeholder="Add Remarks"><?= htmlspecialchars($remarks); ?></textarea>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submitRemarks" class="btn btn-primary" value="Save">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
}

// Handle form submission for updating remarks
if (isset($_POST['submitRemarks'])) {
    $id = $_POST['id'];
    $remarks = mysqli_real_escape_string($con, $_POST['remarks']); // Sanitize input

    // Update remarks in the database
    $sqlUpdateRemarks = "UPDATE leave_application SET remarks = '$remarks' WHERE id = '$id'";
    if (mysqli_query($con, $sqlUpdateRemarks)) {
        echo "<script>alert('Remarks updated successfully.');</script>";
        echo "<script>window.location.href='?leaveapplication';</script>"; // Redirect after update
    } else {
        echo "<script>alert('Error updating remarks: " . mysqli_error($con) . "');</script>";
    }
}
?>

<style>
/* Modal Overlay to Blur Background */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); /* Semi-transparent overlay */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999;
}

/* Modal Container */
.modal-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
    width: 400px;
    max-width: 90%;
    z-index: 1000;
}

/* Panel Heading Styling */
.panel-heading- {
    text-align: left;
    margin-bottom: 20px;
}

/* Close Button */
.panel-heading- a {
    color: #333;
    text-decoration: none;
}

/* Form Input and Button Styling */
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-group input[type="submit"] {
    width: 100%;
    padding: 10px;
    border: none;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    border-radius: 4px;
    font-size: 16px;
}

/* Change button color on hover */
.form-group input[type="submit"]:hover {
    background-color: #0056b3;
}

.modal-dialog {
    width: auto; /* adjust the width to fit your content */
    max-width: 500px; /* set a maximum width */
}

.modal-content {
    width: 100%;
    padding:0;
    overflow-y: auto; /* add a scrollbar if the content is too long */
}

.modal-body form {
    width: 300%; /* adjust the width to fit your content */
    margin: 0 auto; /* center the form horizontally */
}

/* Panel Heading Styling */
.panel-heading- {
    text-align: center;
    margin-bottom: 20px;
}

/* Close Button */
.panel-heading- a {
    color: #333;
    text-decoration: none;
}

/* Form Input and Button Styling */
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-group input[type="submit"] {
    width: 100%;
    padding: 10px;
    border: none;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    border-radius: 4px;
    font-size: 16px;
}

/* Change button color on hover */
.form-group input[type="submit"]:hover {
    background-color: #0056b3;
}

.badge-right {
    position: absolute;
    top: 0;
    right: 0;
    transform: translate(50%, -50%);
    color: white;
    background-color: red;
    border-radius: 50%;
    padding: 4px 8px;
    font-size: 12px;
}
</style>

