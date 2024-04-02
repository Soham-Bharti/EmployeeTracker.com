<?php
session_start();
require_once '../../config/dbConnection.php';
session_destroy();
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

    <!-- Old Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top bg-body-tertiary d-flex align-items-center justify-content-between px-5">
        <a href="home.php" class="svg text-decoration-none d-flex align-items-center">
            <img src="../../Images/mainIcon.gif" alt='svg here'>
            <span class='text-success fw-bold'>EmployeeTracker.com</span>
        </a>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section gradient-custom-2">
        <video autoplay muted loop class="hero-video">
            <source src="../../Images/bg.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="container">
            <h1>Welcome to EmployeeTracker.com</h1>
            <p>Efficiently manage your employees with our powerful tools.</p>
            <a href="login.php" class="btn btn-primary btn-lg">Get Started</a>
        </div>
    </div>

    <!-- Feature Section -->
    <div class="container my-5">
        <div>
            <div class='my-5'>
                <h2 class="text-center my-4"> <span class='gradient-custom-1'>Key Features</span> for <span class='gradient-custom-3'>Users</span></h2>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card feature-card p-3">
                            <img src="../../Images/check.png" class="card-img-top object-fit-contain mx-auto" alt="Feature 1">
                            <hr>
                            <div class="card-body text-center">
                                <h5 class="card-title">Check-In</h5>
                                <p class="card-text">Check yourself In to add your attendance.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card feature-card p-3">
                            <img src="../../Images/project.png" class="card-img-top object-fit-contain py-3 mx-auto" alt="Feature 1">
                            <hr>
                            <div class="card-body text-center">
                                <h5 class="card-title">My Projects</h5>
                                <p class="card-text">See your assigned Projects.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card feature-card p-3">
                            <img src="../../Images/done.png" class="card-img-top object-fit-contain py-3 mx-auto" alt="Feature 1">
                            <hr>
                            <div class="card-body text-center">
                                <h5 class="card-title">PMS</h5>
                                <p class="card-text">Maintain your collaborations.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card feature-card p-3">
                            <img src="../../Images/cross.png" class="card-img-top object-fit-contain py-3 mx-auto" alt="Feature 1">
                            <hr>
                            <div class="card-body text-center">
                                <h5 class="card-title">Check-Out</h5>
                                <p class="card-text">Check yourself Out to add your workings.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class='my-5'>
                <h2 class="text-center my-4"><span class='gradient-custom-1'>Key Features</span> for <span class='gradient-custom-3'>Admin</span></h2>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card feature-card p-3">
                            <img src="../../Images/project-management.png" class="card-img-top object-fit-contain py-3 mx-auto" alt="Feature 1">
                            <hr>
                            <div class="card-body text-center">
                                <h5 class="card-title">Project Assignment</h5>
                                <p class="card-text">Manage project members.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card feature-card p-3">
                            <img src="../../Images/process.png" class="card-img-top object-fit-contain mx-auto" alt="Feature 1">
                            <hr>
                            <div class="card-body text-center">
                                <h5 class="card-title">PMS access</h5>
                                <p class="card-text">Check yourself Employees workings.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card feature-card p-3">
                            <img src="../../Images/fingerprint.png" class="card-img-top object-fit-contain py-3 mx-auto" alt="Feature 1">
                            <hr>
                            <div class="card-body text-center">
                                <h5 class="card-title">Tracking</h5>
                                <p class="card-text">See Check-In's and Check-Out's.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card feature-card p-3">
                            <img src="../../Images/salary.png" class="card-img-top object-fit-contain py-3 mx-auto" alt="Feature 1">
                            <hr>
                            <div class="card-body text-center">
                                <h5 class="card-title">Salary Management</h5>
                                <p class="card-text">Easily calculated salary management.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class='my-5'>
                <h2 class="text-center my-4"><span class='gradient-custom-1'>NOT</span> finished yet we have <span class='gradient-custom-3'>More...</span></h2>
                <div class="row d-flex justify-content-center">
                    <div class="col-md-2">
                        <img src="../../Images/ellipsis.png" class="card-img-top object-fit-contain mx-auto" alt="Feature 1">
                        <hr>
                        <div class="text-center">
                            <a href="login.php" class="btn btn-primary btn-lg">Get Started</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- footer here -->
    <?php include('../common/footer.php'); ?>
    <!-- footer ends -->

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-TywWbglXGg4djtc6VfB8sEgyI9AaUpc7g+FzVCsBqDlCMynkFdw8ynD+DfI9rx7C" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-vBNO3kNc82PIrjvE2sXHaT36Jw2xFKvL6ICTwGvA//PjZL/2I2K99gR6eCW0wo1D" crossorigin="anonymous"></script>
</body>

</html>