<?php
// Get the logged-in user ID
$userId = $_SESSION['idno'];
// Fetch user details 
$userQuery = mysqli_query($con, "SELECT ep.lastname, jt.jobtitle, ed.designation, ed.department 
                                 FROM employee_details ed 
                                 INNER JOIN employee_profile ep ON ep.idno = ed.idno 
                                 INNER JOIN jobtitle jt ON jt.id = ed.designation 
                                 WHERE ed.idno = '$userId'");
$userDetails = mysqli_fetch_assoc($userQuery);
// Extract user designation
$designation = $userDetails['designation']; 
$department = $userDetails['department'];
// Find the corresponding requesting officers 
$sqlProtocol = mysqli_query($con, "SELECT requestingofficer FROM leave_protocols WHERE approvingofficer = '$designation'");
$requestingOfficers = [];
if (mysqli_num_rows($sqlProtocol) > 0) {
    while ($protocol = mysqli_fetch_assoc($sqlProtocol)) {
        $requestingOfficers[] = $protocol['requestingofficer'];
    }
}
// Convert requesting officers array into a string for SQL query
$requestingOfficersStr = implode("','", $requestingOfficers);

// Handle approval action for missed log
if (isset($_GET['approved']) && isset($_GET['id'])) {
    $id = intval($_GET['id']); 
    $approval = "{$userDetails['lastname']} ({$userDetails['jobtitle']})";
    $sqlUpdate = mysqli_query($con, "UPDATE missed_log_application SET applic_status='Approved - $approval' WHERE id='$id'");

    if ($sqlUpdate) {
        echo "<script>alert('Missed log application successfully approved!'); window.location='?managemissedlogapplication';</script>";
    } else {
        echo "<script>alert('Unable to approve missed log application!'); window.location='?managemissedlogapplication';</script>";
    }
}

// Handle disapproval action for missed log
if (isset($_GET['disapproved']) && isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize the ID
    $approval = "{$userDetails['lastname']} ({$userDetails['jobtitle']})";
    $sqlUpdate = mysqli_query($con, "UPDATE missed_log_application SET applic_status='Disapproved - $approval' WHERE id='$id'");

    if ($sqlUpdate) {
        echo "<script>alert('Missed log application successfully disapproved!'); window.location='?managemissedlogapplication';</script>";
    } else {
        echo "<script>alert('Unable to disapprove missed log application!'); window.location='?managemissedlogapplication';</script>";
    }
}
?>

<div class="col-lg-12">
    <div class="content-panel">
        <div class="panel-heading">
            <h4><a href="?main"><i class="fa fa-arrow-left"></i> HOME</a> | <i class="fa fa-file-text"></i> MANAGE MISSED LOG APPLICATION</h4>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped table-condensed" id="hidden-table-info">
                <thead>
                    <tr>
                        <th width="2%" style="text-align: center;">No.</th>
                        <th width="6%" style="text-align: center;">Employee ID</th>
                        <th width="7%" style="text-align: center;">Employee Name</th>
                        <th width="5%" style="text-align: center;">Department</th>
                        <th width="5%" style="text-align: center;">Work Area</th>
                        <th width="7%" style="text-align: center;">Date of Missed Time IN/OUT</th>
                        <th width="5%" style="text-align: center;">Incident</th>
                        <th width="5%" style="text-align: center;">Time</th>
                        <th style="text-align: center;">Reason</th>
                        <th width="10%" style="text-align: center;">Date and Time Applied</th>
                        <th width="10%" style="text-align: center;">Status</th>
                        <th style="text-align: center;">Remarks</th>
                        <th width="6%" style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $x = 1;

                    $sqlCompany = mysqli_query($con, "SELECT company FROM employee_details WHERE idno='{$_SESSION['idno']}'");
                    if ($companyRow = mysqli_fetch_assoc($sqlCompany)) {
                        $companyFilter = $companyRow['company'];
                    }

                    // If there are requesting officers, apply the filter in the query
                    if (!empty($requestingOfficers)) {
                        if($designation == '17' || $designation == '6'  || $designation == '31'){
                            $sqlEmployee = mysqli_query($con, "SELECT ml.*, ml.id as mlid, ep.*, ed.*, dpt.department
                            FROM missed_log_application ml 
                            INNER JOIN employee_profile ep ON ep.idno = ml.idno 
                            INNER JOIN employee_details ed ON ed.idno = ep.idno
                            INNER JOIN department dpt ON dpt.id = ed.department 
                            WHERE ed.designation IN ('$requestingOfficersStr')
                            AND ml.idno != '$userId'
                            ORDER BY 
                                CASE WHEN ml.applic_status='Pending' THEN 1 ELSE 2 END, 
                                ml.time_applied DESC");
                                
                        }else if($designation == '50' || $designation == '89'){
                        $sqlEmployee = mysqli_query($con, "SELECT ml.*, ml.id as mlid, ep.*, ed.*, dpt.department
                            FROM missed_log_application ml 
                            INNER JOIN employee_profile ep ON ep.idno = ml.idno 
                            INNER JOIN employee_details ed ON ed.idno = ep.idno
                            INNER JOIN department dpt ON dpt.id = ed.department 
                            WHERE ed.designation IN ('$requestingOfficersStr') AND ed.company='$companyFilter'
                            AND ml.idno != '$userId'
                            ORDER BY 
                                CASE WHEN ml.applic_status='Pending' THEN 1 ELSE 2 END, 
                                ml.time_applied DESC");
                        }else{
                            $sqlEmployee = mysqli_query($con, "SELECT ml.*, ml.id as mlid, ep.*, ed.*, dpt.department
                            FROM missed_log_application ml 
                            INNER JOIN employee_profile ep ON ep.idno = ml.idno 
                            INNER JOIN employee_details ed ON ed.idno = ep.idno 
                            INNER JOIN department dpt ON dpt.id = ed.department 
                            WHERE ed.designation IN ('$requestingOfficersStr') AND ed.company='$companyFilter' AND ed.department = '$department'
                            AND ml.idno != '$userId'
                            ORDER BY 
                                CASE WHEN ml.applic_status='Pending' THEN 1 ELSE 2 END, 
                                ml.time_applied DESC");
                        }
                        

                        if (mysqli_num_rows($sqlEmployee) > 0) {
                            while ($company = mysqli_fetch_array($sqlEmployee)) {
                                $appStatus = $company['applic_status'];
                                $statusText = $appStatus;

                                if (!empty($appStatus)) {
                                    if (strpos($appStatus, 'Approved-') === 0) {
                                        $statusParts = explode('-', $appStatus);
                                        $statusText = 'Approved by: ' . $statusParts[1];
                                    } elseif (strpos($appStatus, 'Disapproved') === 0) {
                                        $statusText = 'Disapproved';
                                    }
                                }
                                echo "<tr>";
                                echo "<td align='center'>$x.</td>";
                                echo "<td align='center'>{$company['idno']}</td>";
                                echo "<td align='center'>{$company['lastname']}, {$company['firstname']}</td>";
                                echo "<td align='center'>{$company['department']}</td>"; 
                                echo "<td align='center'>{$company['location']}</td>";
                                echo "<td align='center'>" . date('m/d/Y', strtotime($company['datemissed'])) . "</td>";
                                echo "<td align='center'>{$company['incident']}</td>";
                                echo "<td align='center'>" . date("g:i A", strtotime($company['mttime'])) . "</td>";
                                echo "<td align='left'>{$company['reason']}</td>";
                                echo "<td align='center'>" . date('m/d/Y', strtotime($company['date_applied'])) . " " . date('g:i:s A', strtotime($company['time_applied'])) . "</td>";
                                echo "<td align='center'>$statusText</td>";
                                echo "<td align='left'>{$company['remarks']}</td>";
                                ?>
                                <td align="center">
                                    <a href="?managemissedlogapplication&id=<?= $company['mlid']; ?>&approved" class="btn btn-success btn-xs" title="Approve" 
                                       onclick="return confirm('Do you wish to approve this missed log application?');">
                                       <i class='fa fa-thumbs-up'></i>
                                    </a>

                                    <a href="?managemissedlogapplication&id=<?= $company['mlid']; ?>&disapproved" class="btn btn-danger btn-xs" title="Disapprove" 
                                       onclick="return confirm('Do you wish to disapprove this missed log application?');">
                                       <i class='fa fa-thumbs-down'></i>
                                    </a>
                                    <a href="?managemissedlogapplication&addremarks&id=<?=$company['mlid'];?>&remarks=<?=$company['remarks'];?>" class="btn btn-primary btn-xs" title="Remarks">
                                        <i class='fa fa-edit'></i></a>
                                </td>

                                <?php
                                echo "</tr>";
                                $x++;
                            }
                        } else {
                            echo "<tr><td colspan='13' align='center'>No records found!</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='13' align='center'>No records found for the requesting officers!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

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
                    <a href="?managemissedlogapplication"><i class="fa fa-arrow-left"></i> Close</a> |
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
    $sqlUpdateRemarks = "UPDATE missed_log_application SET remarks = '$remarks' WHERE id = '$id'";
    if (mysqli_query($con, $sqlUpdateRemarks)) {
        echo "<script>alert('Remarks updated successfully.');</script>";
        echo "<script>window.location.href='?managemissedlogapplication';</script>"; // Redirect after update
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