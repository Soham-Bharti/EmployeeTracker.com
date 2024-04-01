<?php
session_start();
require_once '../../config/dbConnection.php';
session_destroy();
$conn = new dbConnection();
$conn->connect();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ET.com | Home</title>
    <?php include('../common/favicon.php'); ?>
    <link rel="stylesheet" href="../../Styles/home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class='d-flex flex-column min-vh-100'>
    <nav class="navbar navbar-expand-lg bg-body-tertiary d-flex align-items-center justify-content-between px-5">
        <a href="home.php" class="svg text-decoration-none d-flex align-items-center">
            <img src="../../Images/mainIcon.gif" alt='svg here'>
            <span class='text-success fw-bold'>EmployeeTracker.com</span>
        </a>
        <ul class="navbar-nav mb-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="login.php">Login</a>
            </li>
        </ul>
    </nav>
    <marquee direction="left" class='marquee display-5'>Welcome to Employee Tracker Web App</marquee>

    <!-- footer here -->
    <?php include('../common/footer.php'); ?>
    <!-- footer ends -->

</body>

</html>