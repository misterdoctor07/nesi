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

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs">
            <?php
            $active = 'active'; // Set the first tab as active
            while ($company = mysqli_fetch_array($sqlCompanies)) {
                $companyCode = $company['company'];
                
                // Fetch count of pending leave applications for the company
                $sqlCount = mysqli_query($con, "SELECT COUNT(*) AS total FROM leave_application la
                    INNER JOIN employee_details ed ON la.idno = ed.idno
                    WHERE ed.company = '$companyCode' AND appstatus = 'Pending'");
                $count = mysqli_fetch_assoc($sqlCount)['total'];
                
                // Display company name with the badge showing pending count
                echo "<li class='$active'><a data-toggle='tab' href='#tab-$companyCode'>$companyCode <span class='badge' style='color: white; background-color: red;'>$count</span></a></li>";
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

        // Fetch employees based on company
        $sqlEmployee = mysqli_query($con, "SELECT la.*, la.id as laid, ep.*, ed.*, d.department 
            FROM leave_application la
            INNER JOIN employee_profile ep ON ep.idno = la.idno 
            INNER JOIN employee_details ed ON ed.idno = ep.idno
            INNER JOIN department d ON d.id = ed.department 
            WHERE ed.company = '$companyCode' 
            ORDER BY 
                CASE 
                    WHEN la.appstatus = 'Pending' THEN 1 
                    ELSE 2 
                END,
            la.datearray DESC");

        ?>
        <table class="table table-bordered table-striped table-condensed">
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
                // Output the SQL error message
                echo "Error: " . mysqli_error($con);
                exit; // Stop the script if the query failed
            }

            if (mysqli_num_rows($sqlEmployee) > 0) {
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
                            <a href="?leaveapplication&post&id=<?=$emp['laid'];?>&remarks=<?=$emp['remarks'];?>" class="btn btn-success btn-xs" title="Post">
                                <i class='fa fa-upload'></i>
                            </a>
                            <a href="?leaveapplication&addremarks&id=<?=$emp['laid'];?>&remarks=<?=$emp['remarks'];?>" class="btn btn-primary btn-xs" title="Remarks">
                                <i class='fa fa-edit'></i>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='12' style='text-align: center'>No records found!</td></tr>";
            }
            ?>
            </tbody>
        </table>
        <?php
        echo "</div>"; // End of tab content
        $active = ''; // Remove active class from subsequent contents
    }
    ?>
</div>

    </div>
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     Modal for editing leave application -->
<!-- <div class="modal fade" id="editLeaveModal" tabindex="-1" aria-labelledby="editLeaveModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="editLeaveModalLabel">OVERRIDE LEAVE APPLICATION</h3>
            </div>
            <div class="modal-body" id="editLeaveModalContent">
                 Content will be loaded dynamically here -->
            <!-- </div>
        </div>
    </div>
</div> -->

<!-- Script to load edit leave form in modal -->
<!-- <script>
    $(document).ready(function() {
        // When the edit button is clicked, load the form from an external file
        $('.editLeaveBtn').on('click', function(event) {
            event.preventDefault(); // Prevent the default anchor tag behavior
            // Get the leave ID from the button's data attribute
            var leaveId = $(this).data('leave-id');

            // Open the modal
            $('#editLeaveModal').modal('show');

            // Use AJAX to load content from an external file
            $.ajax({
                url: 'overrideleaveapp.php?id=' + leaveId,  // Path to the existing file
                method: 'GET',
                success: function(response) {
                    // Load the response (the form) into the modal body
                    $('#editLeaveModalContent').html(response);
                },
                error: function() {
                    $('#editLeaveModalContent').html("<p>Error loading form.</p>");
                }
            });
        });
    });
</script> --> 

<?php
if (isset($_GET['post'])) {
    $id = $_GET['id'];
    
    // Sanitize the input
    $id = mysqli_real_escape_string($con, $id);
    
    // Retrieve the leave type and number of days before deletion
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
        foreach ($dateRange as $date) {
            $dateArray[] = $date->format('Y-m-d');  // Store each date in 'Y-m-d' format
        }

        // Now proceed to cancel the leave application
        $sqlUpdate = mysqli_query($con, "UPDATE leave_application SET remarks='POSTED' WHERE id='$id'");

        if ($sqlUpdate) {
            foreach ($dateArray as $leaveDate) {
                // Check if the date exists in the attendance table
                $sqlCheckAttendance = mysqli_query($con, 
                    "SELECT * FROM attendance WHERE idno = '$idno' AND logindate = '$leaveDate'");
                
                if (mysqli_num_rows($sqlCheckAttendance) == 0) {
                    // Date doesn't exist, so insert a new row
                    $sqlInsertAttendance = mysqli_query($con, 
                        "INSERT INTO attendance (idno, logindate, loginam, logoutam, loginpm, logoutpm, remarks) 
                        VALUES ('$idno', '$leaveDate', '00:00:00', '00:00:00', '00:00:00', '00:00:00', '$leaveType')");
                    
                    if (!$sqlInsertAttendance) {
                        echo "<script>alert('Error inserting new attendance record for date: $leaveDate');</script>";
                    }
                } else {
                    // If the date exists, update the remarks column
                    $sqlUpdateAttendRem = mysqli_query($con, 
                        "UPDATE attendance SET remarks = '$leaveType' WHERE idno = '$idno' AND logindate = '$leaveDate'");
                    
                    if (!$sqlUpdateAttendRem) {
                        echo "<script>alert('Error updating attendance for date: $leaveDate');</script>";
                    }
                }
                switch ($leaveType) {
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

</style>

