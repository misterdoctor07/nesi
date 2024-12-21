<?php
include '../config.php'; // Include your database connection file

if (isset($_GET['idno'])) {
    $idno = $_GET['idno'];

    // Prepare the SQL statements
    $deleteProfile = "DELETE FROM employee_profile WHERE idno = ?";
    $deleteDetails = "DELETE FROM employee_details WHERE idno = ?";

    // Create a prepared statement for employee_profile
    if ($stmt = $con->prepare($deleteProfile)) {
        $stmt->bind_param("s", $idno);
        $stmt->execute();
        $stmt->close();
    }

    // Create a prepared statement for employee_details
    if ($stmt = $con->prepare($deleteDetails)) {
        $stmt->bind_param("s", $idno);
        $stmt->execute();
        $stmt->close();
    }
        
    // Redirect back to the employee list page
    header("Location: ?manageemployee");
    exit();
} else {
    // Redirect back if no idno is provided
    header("Location: ?manageemployee");
    exit();
}
?>