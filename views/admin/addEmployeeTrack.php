<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/Admin.php';
date_default_timezone_set("Asia/Kolkata");
$adminObject = new Admin();

// print_r($_SESSION);
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../start/login.php');
}

if (isset($_GET['id'])) $desiredUserId = $_GET['id'];

$checkInTime = $date = $day = "";
$checkOutTime = null;
$checkInTimeErr = $dateErr = $checkOutTimeErr = $Err = "";

$flag = true;
if (isset($_POST['submit'])) {
    $date = $_POST['date'];
    $checkInTime = $_POST['checkInTime'];
    $checkOutTime = $_POST['checkOutTime'];

    if (empty($date)) {
        $dateErr = 'Required';
        $flag = false;
    }

    if (empty($checkInTime)) {
        $checkInTimeErr = 'Required';
        $flag = false;
    }
    if ($flag) {
        $desiredLocation = "trackEmployee.php?id=$desiredUserId";
        // getting the number of check-in's on a specific day 
        $result = $adminObject->employeeTrackCheckInCounts($desiredUserId, $date);
        if (mysqli_num_rows($result) > 0) {
            $row1 = mysqli_fetch_assoc($result);
            $totalCheckInCountToday = $row1['count'];

            if ($totalCheckInCountToday >= 4) {
                // check in limit reached
                $_SESSION['checkInLimitMessage'] = 'success';
                header("Location: $desiredLocation");
            } else {
                $trackResult = $adminObject->employeeTrackDetailsOnDate($desiredUserId, $date);
                if (is_null($checkOutTime) || $checkOutTime == '') {
                    $flag = true;
                    // checkout is not null -> filled
                    // checking if there is already a track in between this time period
                    if (mysqli_num_rows($trackResult) > 0) {
                        while ($row2 = mysqli_fetch_assoc($trackResult)) {
                            $dbCheckInDateTime = explode(' ', $row2['check_in_time']);
                            $dbCheckInTime = end($dbCheckInDateTime);
                            $dbCheckOutDateTime = explode(' ', $row2['check_out_time']);
                            $dbCheckOutTime = end($dbCheckOutDateTime);
                            if (($checkInTime <= $dbCheckOutTime) || ($checkInTime < $dbCheckInTime) || $dbCheckOutTime == '') {
                                // error
                                $Err = "* Track already present for this time range *";
                                $flag = false;
                            }
                        }
                    }
                    if ($flag) {
                        if ($date == date("Y-m-d") && $checkInTime <= date("H:i")) {
                            $result = $adminObject->addEmployeeTrackDetails($desiredUserId, $date, $checkInTime, NULL);
                            if ($result) {
                                // echo "Success 1";
                                $_SESSION['AddEmpTrackStatus'] = 'success';
                                header("Location: $desiredLocation");
                            } else echo "<br>Error occured while inserting into table";
                        } else {
                            // echo "Either you are only adding check-in time in past day without check-out time or check in time is greater than current time.";
                            // $_SESSION['AddEmpTrackStatus'] = 'failure';
                            if ($checkInTime < $checkOutTime) {
                                $result = $adminObject->addEmployeeTrackDetails($desiredUserId, $date, $checkInTime, NULL);
                                if ($result) {
                                    // echo "Success 1";
                                    $_SESSION['AddEmpTrackStatus'] = 'success';
                                    header("Location: $desiredLocation");
                                } else echo "<br>Error occured while inserting into table";
                            } else {
                                $checkOutTimeErr = "* Can not be empty for past tracks";
                            }
                        }
                    }
                } else {
                    // check out is filled
                    $flag = true;
                    // checking if there is already a track in between this time period
                    if (mysqli_num_rows($trackResult) > 0) {
                        while ($row2 = mysqli_fetch_assoc($trackResult)) {
                            $dbCheckInDateTime = explode(' ', $row2['check_in_time']);
                            $dbCheckInTime = end($dbCheckInDateTime);
                            $dbCheckOutDateTime = explode(' ', $row2['check_out_time']);
                            $dbCheckOutTime = end($dbCheckOutDateTime);
                            if (($checkInTime < $dbCheckInTime && $checkOutTime >= $dbCheckInTime) || ($checkOutTime > $dbCheckOutTime && $checkInTime <= $dbCheckOutTime) || ($checkInTime >= $dbCheckInTime && $checkInTime <= $dbCheckOutTime) || ($checkOutTime <= $dbCheckOutTime && $checkOutTime >= $dbCheckInTime)) {
                                // error
                                $Err = "* Track already present for this time range *";
                                $flag = false;
                            }
                        }
                    }
                    if ($flag) {
                        if ($date == date("Y-m-d")) {
                            if ($checkInTime < $checkOutTime && $checkOutTime <= date("H:i")) {
                                $result = $adminObject->addEmployeeTrackDetails($desiredUserId, $date, $checkInTime, $checkOutTime);
                                if ($result) {
                                    // echo "Success 2";
                                    $_SESSION['AddEmpTrackStatus'] = 'success';
                                    header("Location: $desiredLocation");
                                } else echo "<br>Error occured while inserting into table : " . mysqli_error($conn);
                            } else {
                                if ($checkOutTime > date("H:i")) {
                                    $checkOutTimeErr = '* You are checking-out in future!';
                                } else $checkInTimeErr = 'Check-in time is greater than Check-out-time!';
                            }
                        } else {
                            if ($checkInTime < $checkOutTime) {
                                $result = $adminObject->addEmployeeTrackDetails($desiredUserId, $date, $checkInTime, $checkOutTime);
                                if ($result) {
                                    $_SESSION['AddEmpTrackStatus'] = 'success';
                                    header("Location: $desiredLocation");
                                } else echo "<br>Error occured while inserting into table : " . mysqli_error($conn);
                            } else {
                                $checkInTimeErr = 'Check-in time is greater than Check-out-time!';
                            }
                        }
                    }
                }
            }
        } else echo "There was an error fetching the data";
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Employee Track</title>
    <link rel="stylesheet" href="../../Styles/addEmployee.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class='d-flex flex-column min-vh-100'>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <a href="../start/home.php" class="svg text-decoration-none text-success d-flex align-items-center">
                <img src="../../Images/mainIcon.gif" alt='svg here'>
                <span class='fw-bold text-success'>EmployeeTracker.com</span>
            </a>

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="trackEmployee.php?id=<?php echo $desiredUserId ?>">Back</a>
                </li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>

        </div>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-2">New <span class='text-info'>Employee Track</span></h2>
    <div class="container mt-3">
        <div class="col-md-7">
            <span class="fw-bold d-flex justify-content-center"><?php echo $Err; ?></span>
            <!-- toast after unsuccessful track added -->
            <?php if (isset($_SESSION['AddEmpTrackStatus']) && $_SESSION['AddEmpTrackStatus'] == 'failure') { ?>
                <div class="toast show m-auto hide">
                    <div class="toast-header bg-danger text-white ">
                        <strong class="me-auto">Oops Something went wrong!</strong>
                        <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body bg-warning text-muted">
                        <p>Either - <b>'you are only adding check-in time in past day without check-out time'</b> OR <b>'check in time is greater than current time!</b></p>
                    </div>
                </div>
            <?php }
            unset($_SESSION['AddEmpTrackStatus']) ?>
            <!-- toast ends -->
            <!-- toast after unsuccessful track added -->
            <?php if (isset($_SESSION['checkInTimeBigErrorStatus']) && $_SESSION['checkInTimeBigErrorStatus'] == 'failure') { ?>
                <div class="toast show m-auto hide">
                    <div class="toast-header bg-danger text-white ">
                        <strong class="me-auto">Oops Something went wrong!</strong>
                        <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body bg-warning text-muted">
                        <p>Either - <b>'Check-in time is greater than Check-out-time'</b> - OR - <b>'You are checking-out in future'</b> - <br>Please correct it!</p>
                    </div>
                </div>
            <?php }
            unset($_SESSION['checkInTimeBigErrorStatus']) ?>
            <!-- toast ends -->
            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="col-form-label">Date<span>* <?php echo $dateErr ?></span></label>
                    <div class="mb-3">
                        <input type="date" name="date" class="form-control user-select-none" max="<?php echo $date = date("Y-m-d") ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Check In Time<span>* <?php echo $checkInTimeErr ?></span></label>
                    <input type="time" name="checkInTime" class="form-control">
                </div>
                <div class="mb-3 timepicker">
                    <label class="form-label">Check Out Time<span><?php echo $checkOutTimeErr ?></span></label>
                    <input type="time" name="checkOutTime" class="form-control">
                </div>

                <div class="buttons">
                    <input type="submit" name="submit" class="btn btn-dark btn-lg" value="Add Track">
                    <input type="reset" name="reset" class="btn btn-dark btn-lg">
                </div>

            </form>
        </div>
    </div>


    <!-- footer here -->
    <?php include('../common/footer.php'); ?>
    <!-- footer ends -->

</body>

</html>