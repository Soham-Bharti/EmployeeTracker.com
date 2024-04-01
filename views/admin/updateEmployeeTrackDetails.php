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

if (isset($_GET['id'])) {
    $desiredUserId = $_GET['id'];
}
if (isset($_GET['trackId'])) {
    $desiredTrackId = $_GET['trackId'];
}

$checkInTime = $originalDate = $originDate = $uId = $tId = "";
$checkOutTime = null;
$checkInTimeErr = $checkOutTimeErr = "";

if (isset($desiredUserId) && isset($desiredTrackId)) {
    $result = $adminObject->showEmployeeTrackDetailsUsingTrackId($desiredUserId, $desiredTrackId);
    if (mysqli_num_rows($result) == 1) {
        while ($row = mysqli_fetch_assoc($result)) {
            // echo "created at: ". $row['created_at'] . "<br>";
            $originalDate = explode(' ', $row['check_in_time'])[0];
            // echo "$originalDate";
            $dateTime = strtotime($row['check_in_time']);
            $date = date('d M Y', $dateTime);
            $day = date('l', $dateTime);
            // echo "check in time: " . $row['check_in_time'] . "<br>";
            $inTime = explode(' ', $row['check_in_time']);
            $checkInTime = end($inTime);
            // echo "check out time: " . $row['check_out_time'] . "<br>";
            if (!is_null($row['check_out_time'])) {
                $outTime = explode(' ', $row['check_out_time']);
                $checkOutTime = end($outTime);
            }
        }
    }
}


if (isset($_POST['submit'])) {
    // print_r($_POST);
    $checkInTime = $_POST['checkInTime'];
    $checkOutTime = $_POST['checkOutTime'];
    $originDate = $_POST['originDate'];
    $uId = $_POST['uId'];
    $uId = $_POST['uId'];
    $tId = $_POST['tId'];
    $desiredLocation = "trackEmployee.php?id=$uId";
    if ($checkOutTime != '') {
        $trackResult = $adminObject->employeeTrackDetailsOnDate($uId, $originDate);
        // check out is filled

        $flag = true;
        // checking if there is already a track in between this time period
        if (mysqli_num_rows($trackResult) > 1) {
            while ($row2 = mysqli_fetch_assoc($trackResult)) {
                $dbCheckInDateTime = explode(' ', $row2['check_in_time']);
                $dbCheckInTime = end($dbCheckInDateTime);
                $dbCheckOutDateTime = explode(' ', $row2['check_out_time']);
                $dbCheckOutTime = end($dbCheckOutDateTime);
                if (($checkInTime < $dbCheckInTime && $checkOutTime >= $dbCheckInTime) || ($checkOutTime > $dbCheckOutTime && $checkInTime <= $dbCheckOutTime) || ($checkInTime >= $dbCheckInTime && $checkInTime <= $dbCheckOutTime) || ($checkOutTime <= $dbCheckOutTime && $checkOutTime >= $dbCheckInTime)) {
                    // error
                    $_SESSION['trackAlreadyPresent'] = "success";
                    $flag = false;
                    header("location: $desiredLocation");
                }
            }
        }
        if ($flag) {
            if ($originDate != date("Y-m-d")) {
                if ($checkInTime < $checkOutTime) {
                    // sending data to data base  
                    $result = $adminObject->updateEmployeeTrackDetails($uId, $tId, $originDate, $checkInTime, $checkOutTime);
                    if ($result) {
                        echo "<br>Record updated successfully<br>";
                    } else echo "<br>Error occured while inserting into table : " . mysqli_error($conn);
                    echo "Successfully updated with check out time";
                    $_SESSION['UpdateEmpTrackStatus'] = 'success';
                    header("Location: $desiredLocation");
                } else {
                    $_SESSION['checkInTimeBigErrorStatus'] = 'failure';
                    header("Location: $desiredLocation");
                    // echo "Either - 'Check-in time is greater than Check-out-time!' - OR  - 'You are checking-out in future!' - Please correct it.";
                }
            } else {
                // aaj ka he date h
                if ($checkInTime < $checkOutTime && $checkOutTime < date("H:i")) {
                    $result = $adminObject->updateEmployeeTrackDetails($uId, $tId, $originDate, $checkInTime, $checkOutTime);
                    if ($result) {
                        echo "<br>Record updated successfully<br>";
                        echo "Successfully updated with check out time";
                        $_SESSION['UpdateEmpTrackStatus'] = 'success';
                        header("Location: $desiredLocation");
                    } else echo "<br>Error occured while inserting into table : " . mysqli_error($conn);
                } else {
                    if ($checkOutTime > date("H:i")) {
                        $_SESSION['UpdateEmpTrackStatusFail'] = 'failure';
                        // $checkOutTimeErr = '* You are checking-out in future!';
                        header("Location: $desiredLocation");
                    } else {
                        // $checkInTimeErr = 'Check-in time is greater than Check-out-time!';
                        $_SESSION['checkInTimeBigErrorStatus'] = 'failure';
                        header("Location: $desiredLocation");
                    }
                }
            }
        }
    } else {
        // check-out empty h
        if ($originDate == date("Y-m-d") && $checkInTime <= date("H:i")) {
            $result = $adminObject->updateEmployeeTrackDetails($desiredUserId, $desiredTrackId, $date, $checkInTime, NULL);
            if ($result) {
                echo "<br>Record updated successfully<br>";
            } else echo "<br>Error occured while inserting into table : " . mysqli_error($conn);
            echo "Successfully updated without checkout time";
            $_SESSION['UpdateEmpTrackStatus'] = 'success';
            header("Location: $desiredLocation");
        } else {
            $_SESSION['UpdateEmpTrackStatusFail'] = 'failure';
            header("Location: $desiredLocation");
            // $checkOutTimeErr = "You can't be checked-in in future. Please select check-out time as well!";
            // echo "You can't be checked-in in future. Please select check-out time as well!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Employee Track</title>
    <?php include('../common/favicon.php'); ?>
    <link rel="stylesheet" href="../../Styles/update-Employee.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class='d-flex flex-column min-vh-100'>
    <nav class="navbar navbar-expand-lg bg-body-tertiary d-flex align-items-center justify-content-between px-5">
        <a href="../start/home.php" class="svg text-decoration-none text-success d-flex align-items-center">
            <img src="../../Images/mainIcon.gif" alt='svg here'>
            <span class='fw-bold text-success'>EmployeeTracker.com</span>
        </a>
        <ul class="navbar-nav mb-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="trackEmployee.php?id=<?php echo $desiredUserId ?>">Back</a>
            </li>
        </ul>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-2"><span class='text-info'>Update</span> Employee Tracking Details</h2>
    <div class="container mt-3">
        <div class="col-md-7">
            <form action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="post">
                <div class="mb-3">
                    <label class="col-form-label">Date</label>
                    <div class="mb-3">
                        <input type="text" name="date" class="form-control user-select-none" value='<?php echo $date ?>' disabled>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Day</label>
                    <input type="text" class="form-control user-select-none" name="day" value='<?php echo $day ?>' disabled>
                </div>
                <input type="hidden" class="form-control user-select-none" name="originDate" value='<?php echo $originalDate ?>'>
                <input type="hidden" class="form-control user-select-none" name="uId" value='<?php echo $desiredUserId ?>'>
                <input type="hidden" class="form-control user-select-none" name="tId" value='<?php echo $desiredTrackId ?>'>

                <div class="mb-3">
                    <label class="form-label">Check In Time<span>* <?php echo $checkInTimeErr ?></span></label>
                    <input type="time" name="checkInTime" class="form-control" placeholder="10:10:10" maxlength="8" value='<?php echo $checkInTime ?>'>
                </div>
                <div class="mb-3 timepicker">
                    <label class="form-label">Check Out Time<span>* <?php echo $checkOutTimeErr ?></span></label>
                    <input type="time" name="checkOutTime" class="form-control" placeholder="10:10:10" maxlength="8" value='<?php echo is_null($checkOutTime) ? '--' : $checkOutTime ?>'>
                </div>

                <div class="buttons">
                    <input type="submit" name="submit" class="btn btn-dark btn-lg" value="Save">
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