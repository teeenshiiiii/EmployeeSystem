<?php
if(!isset($_SESSION)) {
    session_start();
}

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Management System</title>
    <link rel="stylesheet" href="/employsystem/css/styles.css">
</head>
<body>    
    <div class="container">
        <header>
            <h1>Employee Management System</h1>
            <nav>
            <a href="/employsystem/index.php" class="btn">Dashboard</a>
            <a href="/employsystem/employees/index.php" class="btn">Employees</a> <!-- Assuming this file is in employees/ -->
            <a href="/employsystem/users/index.php" class="btn">Users</a>
            <a href="/employsystem/logout.php" class="btn btn-danger">Logout</a>
            </nav>
        </header>
        <hr>