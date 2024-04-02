<?php
session_start();
require_once '../../config/dbConnection.php';
require_once '../../Classes/Project.php';
require_once '../../Classes/Admin.php';
$projectObject = new Project();
$adminObject = new Admin();

// print_r($_SESSION);
$desiredUserId = $_SESSION['id'];
$result = $adminObject->showEmployeeAllDetails($desiredUserId);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $role =  $row["role"];
}

if ($role !== 'admin') {
    header('Location: ../start/login.php');
}
$date = date('Y-m-d');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Dashboard</title>
    <?php include('../common/favicon.php'); ?>
    <link rel="stylesheet" href="../../Styles/admin-Dashboard.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">

</head>

<body class='d-flex flex-column min-vh-100'>
    <nav class="navbar navbar-expand-lg bg-body-tertiary d-flex justify-content-between px-5">
        <a href="../start/home.php" class="svg text-decoration-none text-success d-flex align-items-center">
            <img src="../../Images/mainIcon.gif" alt='svg here'>
            <span class='fw-bold text-success'>EmployeeTracker.com</span>
        </a>
        <ul class="navbar-nav mb-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="viewAllEmployees.php">Employees</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="viewAllProjects.php">Projects</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../start/login.php" onclick="<?php $_SESSION['isLogout'] = true;?>">Logout</a>
            </li>
        </ul>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-3">Welcome to the <span class='gradient-custom-1'>admin</span> dashboard</h2>

    <!-- toast after successful logged in -->
    <?php if (isset($_SESSION['isLoggedIN']) && $_SESSION['isLoggedIN'] == 'success') { ?>
        <div class="toast show m-auto hide">
            <div class="toast-header bg-success text-white ">
                <strong class="me-auto">Logged In successfully!</strong>
                <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
            </div>
        </div>
    <?php }
    $_SESSION['isLoggedIN'] = '' ?>
    <!-- toast after successful added -->
    <?php if (isset($_SESSION['AddStatus']) && $_SESSION['AddStatus'] == 'success') { ?>
        <div class="toast show m-auto hide">
            <div class="toast-header bg-success text-white ">
                <strong class="me-auto">Record added successfully!</strong>
                <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
            </div>
        </div>
    <?php }
    $_SESSION['AddStatus'] = '' ?>
    <!-- toast after successful update -->
    <?php if (isset($_SESSION['UpdateStatus']) && $_SESSION['UpdateStatus'] == 'success') { ?>
        <div class="toast show m-auto hide">
            <div class="toast-header bg-warning text-white">
                <strong class="me-auto">Record updated successfully!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
        </div>
    <?php }
    $_SESSION['UpdateStatus'] = '' ?>
    <!-- toast after successful delete -->
    <?php if (isset($_SESSION['DeleteStatus']) && $_SESSION['DeleteStatus'] == 'success') { ?>
        <div class="toast show m-auto hide">
            <div class="toast-header bg-danger text-white ">
                <strong class="me-auto">Record deleted successfully!</strong>
                <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
            </div>
        </div>
    <?php }
    $_SESSION['DeleteStatus'] = '' ?>

    <div class="container-fluid mt-5 px-5">
        <div class="d-flex justify-content-between align-items-center">
            <div class='fs-4'>
                <?php
                if (isset($_SESSION['userName'])) {
                    $user = $_SESSION['userName'];
                    echo "Admin: <b>" . ucwords($user) . "</b>";
                } else {
                    echo "Session expired - login again to see your details";
                    header("Location: ../start/login.php");
                }
                ?>
            </div>
        </div>
        <div class="d-flex gap-4 justify-content-between align-items-center">
            <div class="">
                <h2 class="text-center p-0 m-0">Showing <span class='gradient-custom-2'>analytical</span> details</h2>
            </div>
            <div>
                <label>Choose Date:</label>
                <input type="date" class='p-2 mx-2 border border-info focus-ring rounded-2' name="inputDate" id='inputDate' onchange="changeAnalytics(this.value)" max="<?= date('Y-m-d'); ?>" value='<?php echo $date; ?>'>
            </div>
        </div>
        <div class="my-5 d-flex justify-content-evenly" id="updatedContent">
            <div>
                <div class="card" style="width: 17rem;">
                    <img class="card-img-top p-3 w-75 m-auto" src="../../Images/calendar.png" alt="Card image cap">
                    <div class="card-body">
                        <div class='d-flex justify-content-around align-items-center p-0 m-0'>
                            <p class="card-title fs-6">Attendance on <br />(<span id='todayAttendanceDate'><?php echo $date ?></span>): </p>
                            <?php
                            $result = $adminObject->totalEmployeesCount($date);
                            if (mysqli_num_rows($result) > 0) {
                                $row = mysqli_fetch_assoc($result);
                                $totalEmployeesCount =  $row['totalEmployeesCount'];
                            }
                            $result = $adminObject->totalCheckedInUsersOnDate($date);
                            $totalCheckedInUsersTodayCount  = mysqli_num_rows($result);
                            ?>
                            <p id='d-none' class='fw-bold display-6'><span class="<?php echo $totalCheckedInUsersTodayCount >= 0.75 * $totalEmployeesCount ? "text-success" : "text-danger" ?>"><?php echo "$totalCheckedInUsersTodayCount"; ?></span>/<?php echo "$totalEmployeesCount"; ?></p>
                            <p id='d-block' class='d-none fw-bold display-6'>
                                <span id='totalCheckedInUsersTodayCountPresent'></span>/<span id='totalEmployeesCountPresent'></span>
                            </p>
                        </div>
                        <?php
                        $absentEmployeesPresent = [];
                        $result = $adminObject->showAbsentEmployeesOnDate($date);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $absentEmployeesPresent[] = ['id' => $row['id'], 'name' => $row['name']];
                            }
                        }
                        ?>
                        <div class="accordion accordion-flush" id="accordionAbsentees">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed text-primary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseAbsentees" aria-expanded="false" aria-controls="flush-collapseAbsentees">
                                        Show Absentees
                                    </button>
                                </h2>
                                <div id="flush-collapseAbsentees" class="accordion-collapse collapse" data-bs-parent="#accordionAbsentees">
                                    <div id='absentEmployeesPresent' class="accordion-body">
                                        <?php foreach ($absentEmployeesPresent as $employee) : ?>
                                            <p>
                                                <a href="http://<?php echo $_SERVER['HTTP_HOST'] . "/" . trim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>/trackEmployee.php?id=<?php echo urlencode($employee['id']); ?>" class="link-danger link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">
                                                    <?php echo htmlspecialchars($employee['name'], ENT_QUOTES, 'UTF-8'); ?>
                                                </a>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="card" style="width: 17rem;">
                    <img class="card-img-top p-3 w-75 m-auto" src="../../Images/working-time.png" alt="Card image cap">
                    <div class="card-body">
                        <div class='d-flex justify-content-around align-items-center p-0 m-0'>
                            <p class="card-title fs-6">Present & Working
                                < 8.45 hrs. on (<span id='yesterdayWorkingDate'><?php echo date("Y-m-d", strtotime("-1 day", strtotime($date))); ?></span>):
                            </p>
                            <?php
                            $showEmployeesWithLessWorkingHoursYesterdayArray = [];
                            $result = $adminObject->showEmployeesWithLessWorkingHoursYesterday($date);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $hours = floor($row['total_seconds'] / 3600);
                                    $minutes = floor(($row['total_seconds'] % 3600) / 60);
                                    $seconds = $row['total_seconds'] % 60;
                                    $formatted_time = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                                    $showEmployeesWithLessWorkingHoursYesterdayArray[] = ['id' => $row['id'], 'name' => $row['name'], 'formatted_time' => $formatted_time];
                                }
                            }
                            ?>
                            <p id='showEmployeesWithLessWorkingHoursYesterdayArrayCountPast' class='fw-bold display-6'><?php echo sizeof($showEmployeesWithLessWorkingHoursYesterdayArray); ?></p>
                        </div>
                        <div class="accordion accordion-flush" id="showEmployeesWithLessWorkingHoursYesterday">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed text-primary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-showEmployeesWithLessWorkingHoursYesterday" aria-expanded="false" aria-controls="flush-showEmployeesWithLessWorkingHoursYesterday">
                                        Show List
                                    </button>
                                </h2>
                                <div id="flush-showEmployeesWithLessWorkingHoursYesterday" class="accordion-collapse collapse" data-bs-parent="#showEmployeesWithLessWorkingHoursYesterday">
                                    <div id='showEmployeesWithLessWorkingHoursYesterdayArrayPast' class="accordion-body">
                                        <?php foreach ($showEmployeesWithLessWorkingHoursYesterdayArray as $employee) : ?>
                                            <p>
                                                <a href="http://<?php echo $_SERVER['HTTP_HOST'] . "/" . trim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>/employeeWorkingHourDetails.php?id=<?php echo urlencode($employee['id']); ?>" class="link-danger link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">
                                                    <?php echo htmlspecialchars($employee['name'], ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($employee['formatted_time'], ENT_QUOTES, 'UTF-8'); ?>
                                                </a>

                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="card" style="width: 17rem;">
                    <img class="card-img-top p-2 w-75 m-auto" src="../../Images/past-calendar.png" alt="Card image cap">
                    <div class="card-body">
                        <div class='d-flex justify-content-around align-items-center p-0 m-0'>
                            <p class="card-title fs-6">Attendance on <br />(<span id='yesterdayAttendanceDate'><?php echo date("Y-m-d", strtotime("-1 day", strtotime($date))); ?></span>):
                            </p>
                            <?php
                            $yesterdayDate = date('Y-m-d', strtotime("-1 days"));
                            $result = $adminObject->totalEmployeesCount($yesterdayDate);
                            if (mysqli_num_rows($result) > 0) {
                                $row = mysqli_fetch_assoc($result);
                                $totalEmployeesYesterdayCount =  $row['totalEmployeesCount'];
                            }
                            $result = $adminObject->totalCheckedInUsersOnDate($yesterdayDate);
                            $totalCheckedInUsersYesterdayCount  = mysqli_num_rows($result);
                            ?>
                            <p id='d-none1' class='fw-bold display-6'><span class="<?php echo $totalCheckedInUsersYesterdayCount >= 0.75 * $totalEmployeesYesterdayCount ? "text-success" : "text-danger" ?>"><?php echo "$totalCheckedInUsersYesterdayCount"; ?></span>/<?php echo "$totalEmployeesYesterdayCount"; ?></p>
                            <p id='d-block1' class='d-none fw-bold display-6'>
                                <span id='totalCheckedInUsersYesterdayCountPast'></span>/<span id='totalEmployeesYesterdayCountPast'></span>
                            </p>
                        </div>
                        <?php
                        $absentEmployeesPast = [];
                        $result = $adminObject->showAbsentEmployeesOnDate($yesterdayDate);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $absentEmployeesPast[] = ['id' => $row['id'], 'name' => $row['name']];
                            }
                        }
                        ?>
                        <div class="accordion accordion-flush" id="showEmployeesYesterdayAttendance">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed text-primary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-showEmployeesYesterdayAttendance" aria-expanded="false" aria-controls="flush-showEmployeesYesterdayAttendance">
                                        Show Absentees
                                    </button>
                                </h2>
                                <div id="flush-showEmployeesYesterdayAttendance" class="accordion-collapse collapse" data-bs-parent="#showEmployeesYesterdayAttendance">
                                    <div id='absentEmployeesPast' class="accordion-body">
                                        <?php foreach ($absentEmployeesPast as $employee) : ?>
                                            <p>
                                                <a href="http://<?php echo $_SERVER['HTTP_HOST'] . "/" . trim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>/trackEmployee.php?id=<?php echo urlencode($employee['id']); ?>" class="link-danger link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">
                                                    <?php echo $employee['name']; ?>
                                                </a>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="card" style="width: 17rem;">
                    <img class="card-img-top w-75 m-auto" src="../../Images/pms.png" alt="Card image cap">
                    <div class="card-body">
                        <div class='d-flex justify-content-around align-items-center p-0 m-0'>
                            <p class="card-title fs-6">Missed PMS on <br />(<span id='yesterdayPMSDate'><?php echo date("Y-m-d", strtotime("-1 day", strtotime($date))); ?></span>): </p>
                            <?php
                            $result = $projectObject->employeesWithNoPMSYesterday($date);
                            $employeesWithNoPMSYesterdayArrayPast = [];
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $employeesWithNoPMSYesterdayArrayPast[] = ['id' => $row['id'], 'name' => $row['name']];
                                }
                            }
                            ?>
                            <p id='employeesWithNoPMSYesterdayArrayCountPast' class='fw-bold display-6'><?php echo sizeof($employeesWithNoPMSYesterdayArrayPast); ?></p>
                        </div>
                        <div class="accordion accordion-flush" id="employeesWithNoPMSYesterday">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed text-primary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-employeesWithNoPMSYesterday" aria-expanded="false" aria-controls="flush-employeesWithNoPMSYesterday">
                                        Show Members
                                    </button>
                                </h2>
                                <div id="flush-employeesWithNoPMSYesterday" class="accordion-collapse collapse" data-bs-parent="#employeesWithNoPMSYesterday">
                                    <div id='employeesWithNoPMSYesterdayArrayPast' class="accordion-body">
                                        <?php foreach ($employeesWithNoPMSYesterdayArrayPast as $employee) : ?>
                                            <p>
                                                <a href="http://<?php echo $_SERVER['HTTP_HOST'] . "/" . trim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>/employeePMSDetails.php?id=<?php echo urlencode($employee['id']); ?>" class="link-danger link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">
                                                    <?php echo $employee['name']; ?>
                                                </a>
                                            </p>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="my-5 d-flex justify-content-evenly">
            <div class="card" style="width: 17rem;">
                <img class="card-img-top w-75 m-auto" src="../../Images/project.gif" alt="Card image cap">
                <div class="card-body">
                    <div class='d-flex justify-content-around align-items-center p-0 m-0'>
                        <p class="card-title fs-6">Total Projects: </p>
                        <?php
                        $result = $projectObject->totalProjectsCount();
                        if (mysqli_num_rows($result) > 0) {
                            $row = mysqli_fetch_assoc($result);
                            $totalProjects =  $row['totalProjectsCount'];
                        }
                        ?>
                        <p class='fw-bold display-6'><?php echo $totalProjects; ?></p>
                    </div>
                    <a href="viewAllProjects.php" class="btn btn-primary w-100">Go to Projects</a>
                </div>
            </div>
            <div class="card" style="width: 17rem;">
                <img class="card-img-top w-75 m-auto" src="../../Images/management.gif" alt="Card image cap">
                <div class="card-body">
                    <div class='d-flex justify-content-around align-items-center p-0 m-0'>
                        <p class="card-title fs-6">Idle Employee/s: </p>
                        <?php
                        $result = $adminObject->totalEmployeesWithNoProjects();
                        $totalEmployeesWithNoProjectsCount = 0;
                        if (mysqli_num_rows($result) > 0) {
                            $totalEmployeesWithNoProjectsArray = [];
                            while ($row = mysqli_fetch_assoc($result)) {
                                array_push($totalEmployeesWithNoProjectsArray,  $row['name']);
                                $totalEmployeesWithNoProjectsCount++;
                            }
                        }
                        ?>
                        <p class='fw-bold display-6'><?php echo $totalEmployeesWithNoProjectsCount; ?></p>
                    </div>
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed text-primary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                    Show List
                                </button>
                            </h2>
                            <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <?php
                                    if (!empty($totalEmployeesWithNoProjectsArray)) {
                                        foreach ($totalEmployeesWithNoProjectsArray as $name) {
                                    ?><p class='link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover'><?php echo $name ?></p><?php
                                                                            }
                                                                        }
                                                                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- footer here -->
    <?php include('../common/footer.php'); ?>
    <!-- footer ends -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script>
        new DataTable('#adminDashboardEmployeesTable');
    </script>
    <script>
        $(document).ready(function() {
            $('#inputDate').change(function() {
                var selectedDate = $(this).val();
                // alert(selectedDate);
                if (selectedDate != '') {
                    $.ajax({
                        url: 'ajaxContent.php',
                        type: 'POST',
                        data: {
                            newDate: selectedDate
                        },
                        dataType: "JSON",
                        success: function(response) {
                            // $('#updatedContent').html(response);
                            // alert(response);
                            $('#d-none').css("display", "none");
                            $('#d-block').removeClass("d-none");
                            $('#d-none1').css("display", "none");
                            $('#d-block1').removeClass("d-none");
                            $('#totalEmployeesCountPresent').text(response.totalEmployeesCountPresent);
                            $('#totalCheckedInUsersTodayCountPresent').text(response.totalCheckedInUsersTodayCountPresent);
                            if (response.totalCheckedInUsersTodayCountPresent >= 0.75 * response.totalEmployeesCountPresent) {
                                $('#totalCheckedInUsersTodayCountPresent').removeClass('text-danger');
                                $('#totalCheckedInUsersTodayCountPresent').addClass('text-success');
                            } else {
                                $('#totalCheckedInUsersTodayCountPresent').removeClass('text-success');
                                $('#totalCheckedInUsersTodayCountPresent').addClass('text-danger');
                            }

                            var absentEmployeesList = response.absentEmployeesPresent.map(function(employee) {
                                var id = Object.keys(employee)[0];
                                var name = employee[id];
                                var profileUrl = "http://<?php echo $_SERVER['HTTP_HOST'] . "/" . trim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>" + "/trackEmployee.php?id=" + id;
                                var $p = $('<p></p>');
                                var $x = $('<a></a>').attr("href", profileUrl).text(name).addClass("link-danger link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover");
                                $p.append($x);
                                return $p;
                            });
                            $('#absentEmployeesPresent').empty().append(absentEmployeesList);

                            $('#showEmployeesWithLessWorkingHoursYesterdayArrayCountPast').text(response.showEmployeesWithLessWorkingHoursYesterdayArrayCountPast);

                            var lessWorkingEmployeesList = response.showEmployeesWithLessWorkingHoursYesterdayArrayPast.map(function(employee) {
                                var id = employee['id'];
                                var name = employee['name'];
                                var formattedTime = employee['formatted_time'];
                                var profileUrl = "http://<?php echo $_SERVER['HTTP_HOST'] . "/" . trim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>" + "/employeeWorkingHourDetails.php?id=" + id;
                                var $p = $('<p></p>');
                                var $x = $('<a></a>').attr("href", profileUrl).text(name + " - " + formattedTime).addClass("link-danger link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover");
                                $p.append($x);
                                return $p;
                            });
                            $('#showEmployeesWithLessWorkingHoursYesterdayArrayPast').empty().append(lessWorkingEmployeesList);

                            $('#totalEmployeesYesterdayCountPast').text(response.totalEmployeesYesterdayCountPast);
                            $('#totalCheckedInUsersYesterdayCountPast').text(response.totalCheckedInUsersYesterdayCountPast);
                            if (response.totalCheckedInUsersYesterdayCountPast >= 0.75 * response.totalEmployeesYesterdayCountPast) {
                                $('#totalCheckedInUsersYesterdayCountPast').removeClass('text-danger');
                                $('#totalCheckedInUsersYesterdayCountPast').addClass('text-success');
                            } else {
                                $('#totalCheckedInUsersYesterdayCountPast').removeClass('text-success');
                                $('#totalCheckedInUsersYesterdayCountPast').addClass('text-danger');
                            }

                            var absentEmployeesListPast = response.absentEmployeesPast.map(function(employee) {
                                var id = Object.keys(employee)[0];
                                var name = employee[id];
                                var profileUrl = "http://<?php echo $_SERVER['HTTP_HOST'] . "/" . trim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>" + "/trackEmployee.php?id=" + id;
                                var $p = $('<p></p>');
                                var $x = $('<a></a>').attr("href", profileUrl).text(name).addClass("link-danger link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover");
                                $p.append($x);
                                return $p;
                            });
                            $('#absentEmployeesPast').empty().append(absentEmployeesListPast);

                            $('#employeesWithNoPMSYesterdayArrayCountPast').text(response.employeesWithNoPMSYesterdayArrayCountPast);

                            var employeesWithNoPMSYesterdayListPast = response.employeesWithNoPMSYesterdayArrayPast.map(function(employee) {
                                var id = employee['id'];
                                var name = employee['name'];
                                var profileUrl = "http://<?php echo $_SERVER['HTTP_HOST'] . "/" . trim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>" + "/employeePMSDetails.php?id=" + id;
                                var $p = $('<p></p>');
                                var $x = $('<a></a>').attr("href", profileUrl).text(name).addClass("link-danger link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover");
                                $p.append($x);
                                return $p;
                            });
                            $('#employeesWithNoPMSYesterdayArrayPast').empty().append(employeesWithNoPMSYesterdayListPast);
                        }
                    });
                }
            });
        });



        function changeAnalytics(val) {
            var tempDate = new Date(val);
            tempDate.setDate(tempDate.getDate() - 1);
            var formattedYesterdayDate = formatDate(tempDate);
            document.getElementById("inputDate").values = val;
            document.getElementById("todayAttendanceDate").innerHTML = val;
            document.getElementById("yesterdayWorkingDate").innerHTML = formattedYesterdayDate;
            document.getElementById("yesterdayAttendanceDate").innerHTML = formattedYesterdayDate;
            document.getElementById("yesterdayPMSDate").innerHTML = formattedYesterdayDate;
        }

        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        }
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</body>

</html>