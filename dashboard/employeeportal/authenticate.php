<?php
include('../config.php');
$idno=$_POST['empid'];
$birtdate=date('Y-m-d',strtotime($_POST['birthdate']));
$sql="SELECT * FROM employee_profile WHERE idno='$idno' AND birthdate='$birtdate'";
$UserLogin=mysqli_query($con,$sql);
if(mysqli_num_rows($UserLogin)>0){
    $user=mysqli_fetch_array($UserLogin);
    session_start();
    $_SESSION['idno']=$idno;
    echo "<script>alert('Hello, $user[firstname]!'); window.location='dashboard.php?main';</script>";
}else{
    echo "<script>alert('You are not authorized to access this portal!'); window.location='../employeeportal/';</script>";
}
?>
