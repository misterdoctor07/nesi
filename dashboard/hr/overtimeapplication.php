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
                <i class="fa fa-clock-o"></i> OVERTIME APPLICATION
            </h4>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs">
            <?php
            $active = 'active'; // Set the first tab as active
            while ($company = mysqli_fetch_array($sqlCompanies)) {
                $companyCode = $company['company'];
                
                // Fetch count of pending overtime applications for the company
                $sqlCount = mysqli_query($con, "SELECT COUNT(*) AS total FROM overtime_application ot
                    INNER JOIN employee_details ed ON ot.idno = ed.idno
                    WHERE ed.company = '$companyCode' AND app_status = 'Pending'");
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
        $sqlEmployee = mysqli_query($con, "SELECT ot.*, ot.id as otid, ep.*, ed.* 
            FROM overtime_application ot 
            INNER JOIN employee_profile ep ON ep.idno = ot.idno 
            INNER JOIN employee_details ed ON ed.idno = ep.idno 
            WHERE ed.company = '$companyCode' 
            ORDER BY 
                CASE 
                    WHEN ot.app_status = 'Pending' THEN 1 
                    ELSE 2 
                END, 
            ot.datearray DESC");

        ?>
        <table class="table table-bordered table-striped table-condensed">
            <thead>
                <tr>
                    <th width="2%" style="text-align: center;">No.</th>
                    <th width="6%" style="text-align: center;">Employee ID</th>
                    <th width="8%" style="text-align: center;">Employee Name</th>
                    <th width="5%" style="text-align: center;">OT Date</th>
                    <th width="5%" style="text-align: center;">OT Time</th>
                    <th style="text-align: center;">Reason</th>
                    <th width="9%" style="text-align: center;">Date/Time Applied</th>
                    <th width="9%" style="text-align: center;">Status</th>
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
                        <td align='center'><?=$x;?>.</td>
                        <td align='center'><?=$emp['idno'];?></td>
                        <td align='center'><?=$emp['lastname']. ','.$emp['firstname'];?></td>
                        <td align='center'><?=date('m/d/Y', strtotime($emp['otdate']));?></td>
                        <td align='center'><?=$emp['ottime'];?></td>
                        <td align='left'><?=$emp['reasons'];?></td>
                        <td align='center'><?=$emp['datearray']."".$emp['timearray'];?></td>
                        <td align='center'><?=$emp['app_status'];?></td>
                        <td align='left'><?=$emp['remarks'];?></td>
                        <td align="center">
                            <a href="?overtimeapplication&id=<?= $emp['otid']; ?>&approved" class="btn btn-success btn-xs" title="Approve" 
                               onclick="return confirm('Do you wish to approve this overtime application?');">
                               <i class='fa fa-thumbs-up'></i>
                            </a>

                            <a href="?overtimeapplication&id=<?= $emp['otid']; ?>&disapproved" class="btn btn-danger btn-xs" title="Disapprove" 
                               onclick="return confirm('Do you wish to disapprove this overtime application?');">
                               <i class='fa fa-thumbs-down'></i>
                            </a>
                            <a href="?overtimeapplication&addremarks&id=<?=$emp['otid'];?>&remarks=<?=$emp['remarks'];?>" class="btn btn-primary btn-xs" title="Remarks">
                                <i class='fa fa-edit'></i></a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='10' align='center'>No records found!</td></tr>";
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
</div>

<?php 
// Handle approval action for overtime
if (isset($_GET['approved']) && isset($_GET['id'])) {
    $id = intval($_GET['id']); 

    // Update query to approve only the specific overtime application
    $approval = "{$userDetails['lastname']} ({$userDetails['jobtitle']})";
    $sqlUpdate = mysqli_query($con, "UPDATE overtime_application SET app_status='Approved - $approval' WHERE id='$id'");

    if ($sqlUpdate) {
        echo "<script>alert('Overtime application successfully approved!'); window.location='?manageovertimeapplication';</script>";
    } else {
        echo "<script>alert('Unable to approve overtime application!'); window.location='?manageovertimeapplication';</script>";
    }
}

// Handle disapproval action for overtime
if (isset($_GET['disapproved']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Update query to disapprove only the specific overtime application
    $approval = "{$userDetails['lastname']} ({$userDetails['jobtitle']})";
    $sqlUpdate = mysqli_query($con, "UPDATE overtime_application SET app_status='Disapproved - $approval' WHERE id='$id'");

    if ($sqlUpdate) {
        echo "<script>alert('Overtime application successfully disapproved!'); window.location='?manageovertimeapplication';</script>";
    } else {
        echo "<script>alert('Unable to disapprove overtime application!'); window.location='?manageovertimeapplication';</script>";
    }
}

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
                    <a href="?overtimeapplication"><i class="fa fa-arrow-left"></i> Close</a> |
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
        echo "<script>window.location.href='?manageleaveapplication';</script>"; // Redirect after update
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
