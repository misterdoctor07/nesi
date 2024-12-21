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
                <i class="fa fa-file-text"></i> MISSED LOGIN APPLICATION
            </h4>
        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs">
            <?php
            $active = 'active'; // Set the first tab as active
            while ($company = mysqli_fetch_array($sqlCompanies)) {
                $companyCode = $company['company'];
                
                // Fetch count of pending overtime applications for the company
                $sqlCount = mysqli_query($con, "SELECT COUNT(*) AS total FROM missed_log_application ml
                    INNER JOIN employee_details ed ON ml.idno = ed.idno
                    WHERE ed.company = '$companyCode' AND applic_status = 'Pending'");
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
        $sqlEmployee = mysqli_query($con, "SELECT ml.*, ml.id as mlid, ep.*, ed.*, d.department 
            FROM missed_log_application ml
            INNER JOIN employee_profile ep ON ep.idno = ml.idno 
            INNER JOIN employee_details ed ON ed.idno = ep.idno
            INNER JOIN department d ON d.id = ed.department 
            WHERE ed.company = '$companyCode' 
            ORDER BY 
                CASE 
                    WHEN ml.applic_status = 'Pending' THEN 1 
                    ELSE 2 
                END, 
            ml.date_applied DESC");

        ?>
        <table class="table table-bordered table-striped table-condensed">
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

            if (!$sqlEmployee) {
                // Output the SQL error message
                echo "Error: " . mysqli_error($con);
                exit; // Stop the script if the query failed
            }

            if (mysqli_num_rows($sqlEmployee) > 0) {
                while ($emp = mysqli_fetch_array($sqlEmployee)) {
                    ?>
                    <tr>
                        <td align='center'><?= $x++; ?>.</td>
                        <td align='center'><?= $emp['idno']; ?></td>
                        <td align='center'><?= $emp['lastname'] . ', ' . $emp['firstname']; ?></td>
                        <td align='center'><?= $emp['department']?></td>
                        <td align='center'><?= $emp['location']?></td>
                        <td align='center'><?= date('m/d/Y', strtotime($emp['datemissed'])); ?></td>
                        <td align='center'><?= $emp['incident'] ?></td>
                        <td align='center'><?= date("g:i A", strtotime($emp['mttime'])); ?></td>
                        <td align='left'><?= $emp['reason'] ?></td>
                        <td align='center'><?= date('m/d/Y', strtotime($emp['date_applied'])) . " " . date('g:i:s A', strtotime($emp['time_applied'])); ?></td>
                        <td align='center'><?= $emp['applic_status'] ?></td>
                        <td align='left'><?= $emp['remarks'] ?></td>
                        <td align="center">
                            <a href="?overridemissedlog&id=<?= $emp['mlid']; ?>&approved" class="btn btn-success btn-xs" title="Override">
                               <i class='fa fa-gear'></i>
                            </a>
                            <a href="?missedloginapplication&addremarks&id=<?=$emp['mlid'];?>&remarks=<?=$emp['remarks'];?>" class="btn btn-primary btn-xs" title="Remarks">
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
                    <a href="?missedloginapplication"><i class="fa fa-arrow-left"></i> Close</a> |
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
        echo "<script>window.location.href='?missedloginapplication';</script>"; // Redirect after update
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
