<?php
session_start();
include '../config.php';

// Get the logged-in user ID
$userId = $_SESSION['idno'];

// Fetch user details (designation, department, and other relevant info)
$userQuery = mysqli_query($con, "SELECT ep.lastname, jt.jobtitle, ed.designation, ed.department 
                                 FROM employee_details ed 
                                 INNER JOIN employee_profile ep ON ep.idno = ed.idno 
                                 INNER JOIN jobtitle jt ON jt.id = ed.designation 
                                 WHERE ed.idno = '$userId'");

if (!$userQuery) {
    die("Error: " . mysqli_error($con));
}

$userDetails = mysqli_fetch_assoc($userQuery);

if (!$userDetails) {
    die("Error: No user details found");
}

// Extract user designation and department
$designation = $userDetails['designation'];
$department = $userDetails['department'];

// Find the corresponding requesting officers (employees under this approving officer)
$sqlProtocol = mysqli_query($con, "SELECT requestingofficer FROM leave_protocols WHERE approvingofficer = '$designation'");

if (!$sqlProtocol) {
    die("Error: " . mysqli_error($con));
}

$requestingOfficers = array();
while ($protocol = mysqli_fetch_assoc($sqlProtocol)) {
    $requestingOfficers[] = $protocol['requestingofficer']; // Collect requesting officers
}

// Convert requesting officers array into a string for SQL query
$requestingOfficersStr = implode("','", $requestingOfficers);

// Fetch the company of the logged-in user
$sqlCompany = mysqli_query($con, "SELECT company FROM employee_details WHERE idno='$userId'");

if (!$sqlCompany) {
    die("Error: " . mysqli_error($con));
}

$companyRow = mysqli_fetch_assoc($sqlCompany);

if (!$companyRow) {
    die("Error: No company found");
}

$companyFilter = $companyRow['company'];

// Count pending leave applications for the same company
$pendingLeaveCount = 0;
if (!empty($requestingOfficers)) {
    $leaveQuery = "SELECT COUNT(*) AS total FROM leave_application la
                   INNER JOIN employee_details ed ON la.idno = ed.idno
                   WHERE la.appstatus = 'Pending' AND ed.designation IN ('$requestingOfficersStr')";

    if ($designation == '17' || $designation == '6') {
        $sqlLeave = mysqli_query($con, $leaveQuery);
    } elseif ($designation == '50' || $designation == '89') {
        $sqlLeave = mysqli_query($con, $leaveQuery . " AND ed.company='$companyFilter'");
    } else {
        $sqlLeave = mysqli_query($con, $leaveQuery . " AND ed.company='$companyFilter' AND ed.department='$department'");
    }

    if (!$sqlLeave) {
        die("Error: " . mysqli_error($con));
    }

    $leaveRow = mysqli_fetch_assoc($sqlLeave);

    if (!$leaveRow) {
    die("Error: No leave data found");
}

$pendingLeaveCount = $leaveRow['total'];

// Count pending OT applications
$pendingOTCount = 0;
if (!empty($requestingOfficers)) {
    $otQuery = "SELECT COUNT(*) AS total FROM overtime_application ot
                INNER JOIN employee_details ed ON ot.idno = ed.idno
                WHERE ot.app_status = 'Pending' AND ed.designation IN ('$requestingOfficersStr')";

    if ($designation == '17' || $designation == '6') {
        $sqlOT = mysqli_query($con, $otQuery);
    } elseif ($designation == '50' || $designation == '89') {
        $sqlOT = mysqli_query($con, $otQuery . " AND ed.company='$companyFilter'");
    } else {
        $sqlOT = mysqli_query($con, $otQuery . " AND ed.company='$companyFilter' AND ed.department='$department'");
    }

    if (!$sqlOT) {
        die("Error: " . mysqli_error($con));
    }

    $otRow = mysqli_fetch_assoc($sqlOT);

    if (!$otRow) {
        die("Error: No OT data found");
    }

    $pendingOTCount = $otRow['total'];
}

// Count pending missed log applications
$pendingMLCount = 0;
if (!empty($requestingOfficers)) {
    $mlQuery = "SELECT COUNT(*) AS total FROM missed_log_application ml
                INNER JOIN employee_details ed ON ml.idno = ed.idno
                WHERE ml.applic_status = 'Pending' AND ed.designation IN ('$requestingOfficersStr')";

    if ($designation == '17' || $designation == '6') {
        $sqlML = mysqli_query($con, $mlQuery);
    } elseif ($designation == '50' || $designation == '89') {
        $sqlML = mysqli_query($con, $mlQuery . " AND ed.company='$companyFilter'");
    } else {
        $sqlML = mysqli_query($con, $mlQuery . " AND ed.company='$companyFilter' AND ed.department='$department'");
    }

    if (!$sqlML) {
        die("Error: " . mysqli_error($con));
    }

    $mlRow = mysqli_fetch_assoc($sqlML);

    if (!$mlRow) {
        die("Error: No ML data found");
    }

    $pendingMLCount = $mlRow['total'];
}

$totalCount = $pendingLeaveCount + $pendingOTCount + $pendingMLCount;

// Output the pending counts as JSON
header('Content-Type: application/json');
echo json_encode(array(
    'leave_count' => $pendingLeaveCount,
    'ot_count' => $pendingOTCount,
    'ml_count' => $pendingMLCount,
    'total_count' => $totalCount
));
}
?>