<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/Admin.php';
$adminObject = new Admin();

// print_r($_SESSION);
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../start/login.php');
}

if (isset($_GET['id'])) $desiredUserId = $_GET['id'];
else header('Location: ../start/login.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emplpoyee Track | Admin</title>
    <?php include('../common/favicon.php'); ?>
    <link rel="stylesheet" href="../../Styles/track-Employee.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class='d-flex flex-column min-vh-100'>
    <nav class="navbar navbar-expand-lg bg-body-tertiary d-flex align-items-center justify-content-between px-5">
        <a href="../start/home.php" class="svg text-decoration-none text-success d-flex align-items-center">
            <img src="../../Images/mainIcon.gif" alt='svg here'>
            <span class='fw-bold text-success'>EmployeeTracker.com</span>
        </a>
        <ul class="navbar-nav mb-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="viewAllEmployees.php">Back</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="addEmployeeTrack.php?id=<?php echo $desiredUserId ?>">Add Track</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="employeeWorkingHourDetails.php?id=<?php echo $desiredUserId ?>">Working Hours</a>
            </li>
        </ul>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-3">Employee <span class='gradient-custom-1'>Tracking</span> dashboard</h2>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <div class='fs-4'>
                <?php
                $result = $adminObject->showEmployeeAllDetails($desiredUserId);
                if (mysqli_num_rows($result) === 1) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $userName = $row['name'];
                    }
                }
                echo "Emp Id: <b>" . $desiredUserId . "</b> | " . $userName;
                ?>
            </div>
            <div>
                <?php
                $showStatus = '';
                $result = $adminObject->showEmployeeTrackDetailsWithGroupByCheckInTime($desiredUserId);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $checkOut = $row["check_out_time"];
                        is_null($checkOut) ? $showStatus = 'check-in' : $showStatus = 'check-out';
                        // as we only need one most recent check-in time
                        break;
                    }
                }
                ?>
                <?php
                if ($showStatus === 'check-in') {
                ?>
                    <h4 class="text-center mt-3">Emp is currently <span class='gradient-custom-3 fw-bold'><?php echo $showStatus ?></span></h4>
                <?php } else if ($showStatus === 'check-out') {
                ?>
                    <h4 class="text-center mt-3">Emp is currently <span class='gradient-custom-1 fw-bold'><?php echo $showStatus ?></span></h4>
                <?php } else { ?>
                    <h4 class="text-center mt-3">Emp will do <span class='gradient-custom-2 fw-bold'>FIRST</span> check-in!</h4>
                <?php } ?>
            </div>
        </div>

        <!-- toast after successful added -->
        <?php if (isset($_SESSION['trackAlreadyPresent']) && $_SESSION['trackAlreadyPresent'] == 'success') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-danger text-white">
                    <strong class="me-auto">Oops! Track already present between this time range.</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php }
        $_SESSION['trackAlreadyPresent'] = '' ?>
        <!-- toast ends -->

        <!-- toast after successful added -->
        <?php if (isset($_SESSION['UpdateEmpTrackStatus']) && $_SESSION['UpdateEmpTrackStatus'] == 'success') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-warning text-muted ">
                    <strong class="me-auto">Track updated successfully!</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php }
        $_SESSION['UpdateEmpTrackStatus'] = '' ?>
        <!-- toast ends -->
        <!-- toast after check-in limit reached -->
        <?php if (isset($_SESSION['checkInLimitMessage']) && $_SESSION['checkInLimitMessage'] == 'success') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-danger text-white ">
                    <strong class="me-auto">Oops! Check-in limit was reached!</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php }
        $_SESSION['checkInLimitMessage'] = '' ?>
        <!-- toast after successful track added -->
        <?php if (isset($_SESSION['AddEmpTrackStatus']) && $_SESSION['AddEmpTrackStatus'] == 'success') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-success text-white ">
                    <strong class="me-auto">Track added successfully!</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php }
        $_SESSION['AddEmpTrackStatus'] = '' ?>
        <!-- toast ends -->
        <!-- toast after unsuccessful track added -->
        <?php if (isset($_SESSION['UpdateEmpTrackStatusFail']) && $_SESSION['UpdateEmpTrackStatusFail'] == 'failure') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-danger text-white ">
                    <strong class="me-auto">Oops Something went wrong!</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    <p>Either you are checking out in future or You are trying to be checked-in in future!</p>
                </div>
            </div>
        <?php }
        $_SESSION['UpdateEmpTrackStatusFail'] = '' ?>
        <!-- toast ends -->
        <!-- toast after unsuccessful track added -->
        <?php if (isset($_SESSION['checkInTimeBigErrorStatus']) && $_SESSION['checkInTimeBigErrorStatus'] == 'failure') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-danger text-white ">
                    <strong class="me-auto">Oops Something went wrong!</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    <p>Check-in time is greater than Check-out-time!' - OR - 'You are checking-out in future!</p>
                </div>
            </div>
        <?php }
        $_SESSION['checkInTimeBigErrorStatus'] = '' ?>
        <!-- toast ends -->
        <!-- toast after successful deletion of track -->
        <?php if (isset($_SESSION['DeleteTrackStatus']) && $_SESSION['DeleteTrackStatus'] == 'success') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-danger text-white ">
                    <strong class="me-auto">Track deleted successfully!</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php }
        $_SESSION['DeleteTrackStatus'] = '' ?>
        <h2 class="text-center mt-5">Showing <span class='gradient-custom-3'>LAST 10</span> tracks</h2>
        <div class="mt-3">
            <table>
                <tr class="text-dark">
                    <th>S.Number</th>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Check In Time</th>
                    <th>Check Out Time</th>
                    <th>Action</th>
                    <th>Action</th>
                </tr>
                <?php
                $result = $adminObject->showEmployeeTrackDetailsWithGroupByCheckInTime($desiredUserId);
                $seialNumber = 1;
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($seialNumber == 11) break; // to ensure we are dsplaying only last 10 records
                        $flag = false;
                        $checkOut = $row["check_out_time"];
                        $trackId = $row['id'];
                ?>
                        <?php
                        $time = strtotime($row["check_in_time"]);
                        $checkInTime = strtotime($row["check_in_time"]);
                        if (is_null($checkOut)) {
                            $checkOutIsNull = true;
                        } else {
                            $checkOutTime = strtotime($row["check_out_time"]);
                            $checkOutIsNull = false;
                        };
                        ?>
                        <tr>
                            <td><?php echo $seialNumber++ ?></td>
                            <td><?php
                                echo date('d M Y', $time);
                                ?></td>
                            <td><?php
                                echo date('l', $time);
                                ?>
                            </td>
                            <td>
                                <?php
                                echo date('H:i:s', $checkInTime);
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($checkOutIsNull) {
                                    echo " -- ";
                                } else {
                                    echo date('H:i:s', $checkOutTime);
                                }
                                ?>
                            </td>
                            <td>
                                <a href="updateEmployeeTrackDetails.php?trackId=<?php echo $row["id"] ?>&id=<?php echo $desiredUserId ?>" class="btn btn-primary">Edit</a>
                            </td>
                            <td>
                                <a onclick="return confirm('Are you sure you want to delete this track?')" href="deleteEmployeeTrackDetails.php?trackId=<?php echo $row["id"] ?>&id=<?php echo $desiredUserId ?> " class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php
                    }
                } else { ?>
                    <h2 class="text-center mt-5">Oops! <span class='text-danger text-center'>NO</span> track found!</h2>
                <?php }
                ?>
            </table>
        </div>
    </div>

    <!-- footer here -->
    <?php include('../common/footer.php'); ?>
    <!-- footer ends -->

</body>

</html>