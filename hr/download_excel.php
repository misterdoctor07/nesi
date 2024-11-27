<?php
include('../config.php');

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="attendance_summary_' . date('Y-m-d') . '.xls"');

$dept = $_GET['dept'];
$startdate = $_GET['startdate'];
$enddate = $_GET['enddate'];

// Fetch company name
$sqlCompany = mysqli_query($con, "SELECT companyname FROM settings WHERE companycode='$dept'");
$comp = mysqli_fetch_array($sqlCompany);

// Start outputting the Excel file
echo "{$comp['companyname']}\n";
echo "Attendance Summary for " . date('F Y', strtotime($startdate)) . "\n";

// Create the header for the attendance summary
echo "No.\tEmployee Name\tDepartment\tRemarks\n";

// Fetch employee data
$sqlEmployee = mysqli_query($con, "SELECT ep.*, ed.* FROM employee_profile ep 
LEFT JOIN employee_details ed ON ed.idno = ep.idno 
WHERE ed.status NOT LIKE '%RESIGNED%' AND ed.company='$dept' 
ORDER BY ed.department ASC");

$x = 1;

while ($company = mysqli_fetch_array($sqlEmployee)) {
    $department = ""; // Initialize department variable
    $sqlDepartment = mysqli_query($con, "SELECT * FROM department WHERE id='$company[department]'");
    if (mysqli_num_rows($sqlDepartment) > 0) {
        $d = mysqli_fetch_array($sqlDepartment);
        $department = $d['department'];
    }

    // Output the employee data
    echo "{$x}\t{$company['lastname']}, {$company['firstname']}\t{$department}\tRemarks Here\n"; // Replace with actual remarks
    $x++;
}
?>