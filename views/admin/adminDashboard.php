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
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <a href="../start/home.php" class="svg text-decoration-none text-success d-flex align-items-center">
                <img src="../../Images/mainIcon.gif" alt='svg here'>
                <span class='fw-bold text-success'>EmployeeTracker.com</span>
            </a>

            <ul class="navbar-nav mb-2 me-auto mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="../start/login.php">Logout</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="viewAllEmployees.php">Employees</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="viewAllProjects.php">Projects</a>
                </li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
        </div>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-3">Welcome to the <span class='gradient-custom-1'>admin</span> dashboard</h2>

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
                <input type="date" class='p-2 mx-2 border border-info focus-ring rounded-2' name="inputDate" id='inputDate' onchange="changeAnalytics(this.value)" min='2024-03-16' max="<?= date('Y-m-d'); ?>" value='<?php echo $date; ?>'>
            </div>
        </div>
        <div class="my-5 d-flex justify-content-evenly" id="updatedContent">
            <div>
                <div class="card" style="width: 17rem;">
                    <img class="card-img-top p-4 w-75 m-auto" src="../../Images/calendar.png" alt="Card image cap">
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
                            <p class='fw-bold display-6'><span class="<?php echo $totalCheckedInUsersTodayCount >= 0.75 * $totalEmployeesCount ? "text-success" : "text-danger" ?>"><?php echo "$totalCheckedInUsersTodayCount"; ?></span>/<?php echo "$totalEmployeesCount"; ?></p>
                        </div>
                        <?php
                        $absentEmployees = [];
                        $result = $adminObject->showAbsentEmployeesOnDate($date);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                array_push($absentEmployees,  $row['name']);
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
                                    <div class="accordion-body">
                                        <?php
                                        foreach ($absentEmployees as $name) {
                                        ?><p class='m-0 p-0'><?php echo $name ?></p><?php
                                                                                } ?>
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
                            <p class="card-title fs-6">Work Time
                                < 8.45Hrs. on <br />(<span id='yesterdayWorkingDate'><?php echo date("Y-m-d", strtotime("-1 day", strtotime($date))); ?></span>):
                            </p>
                            <?php
                            $result = $adminObject->showEmployeesWithLessWorkingHoursYesterday($date);
                            $showEmployeesWithLessWorkingHoursYesterdayArray = [];
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $showEmployeesWithLessWorkingHoursYesterdayArray[$row['name']] = $row['total_seconds'];
                                }
                            }
                            ?>
                            <p class='fw-bold display-6'><?php echo sizeof($showEmployeesWithLessWorkingHoursYesterdayArray); ?></p>
                        </div>
                        <div class="accordion accordion-flush" id="showEmployeesWithLessWorkingHoursYesterday">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed text-primary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-showEmployeesWithLessWorkingHoursYesterday" aria-expanded="false" aria-controls="flush-showEmployeesWithLessWorkingHoursYesterday">
                                        Show List
                                    </button>
                                </h2>
                                <div id="flush-showEmployeesWithLessWorkingHoursYesterday" class="accordion-collapse collapse" data-bs-parent="#showEmployeesWithLessWorkingHoursYesterday">
                                    <div class="accordion-body">
                                        <?php
                                        foreach ($showEmployeesWithLessWorkingHoursYesterdayArray as $name => $total_seconds) {
                                            $hours = floor($total_seconds / 3600);
                                            $minutes = floor(($total_seconds % 3600) / 60);
                                            $seconds = $total_seconds % 60;
                                            $formatted_time = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                                        ?><p><?php echo explode(' ', $name)[0] . " - $formatted_time" ?></p>
                                            <hr><?php
                                            }
                                                ?>
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
                            <p class="card-title fs-6">Attendance on <br />(<span id='yesterdayWorkingDate'><?php echo date("Y-m-d", strtotime("-1 day", strtotime($date))); ?></span>):
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
                            <p class='fw-bold display-6'><span class="<?php echo $totalCheckedInUsersYesterdayCount >= 0.75 * $totalEmployeesYesterdayCount ? "text-success" : "text-danger" ?>"><?php echo "$totalCheckedInUsersYesterdayCount"; ?></span>/<?php echo "$totalEmployeesYesterdayCount"; ?></p>
                        </div>
                        <?php
                        $absentEmployees = [];
                        $result = $adminObject->showAbsentEmployeesOnDate($yesterdayDate);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                array_push($absentEmployees,  $row['name']);
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
                                    <div class="accordion-body">
                                        <?php
                                        foreach ($absentEmployees as $name) {
                                        ?><p class='m-0 p-0'><?php echo $name ?></p><?php
                                                                                } ?>
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
                            $employeesWithNoPMSYesterdayArray = [];
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    array_push($employeesWithNoPMSYesterdayArray,  $row['name']);
                                }
                            }
                            ?>
                            <p class='fw-bold display-6'><?php echo sizeof($employeesWithNoPMSYesterdayArray); ?></p>
                        </div>
                        <div class="accordion accordion-flush" id="employeesWithNoPMSYesterday">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed text-primary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#flush-employeesWithNoPMSYesterday" aria-expanded="false" aria-controls="flush-employeesWithNoPMSYesterday">
                                        Show Members
                                    </button>
                                </h2>
                                <div id="flush-employeesWithNoPMSYesterday" class="accordion-collapse collapse" data-bs-parent="#employeesWithNoPMSYesterday">
                                    <div class="accordion-body">
                                        <?php
                                        foreach ($employeesWithNoPMSYesterdayArray as $name) {
                                        ?><p class='m-0 p-0'><?php echo $name ?></p><?php
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
            <!-- fws -->

            <!-- more -->
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
                                    // print_r($totalEmployeesWithNoProjectsArray);
                                    if (!empty($totalEmployeesWithNoProjectsArray)) {
                                        foreach ($totalEmployeesWithNoProjectsArray as $name) {
                                    ?><p class='m-0 p-0'><?php echo $name ?></p><?php
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
                        url: 'dynamicContent.php',
                        type: 'POST',
                        data: {
                            newDate: selectedDate
                        },
                        success: function(response) {
                            $('#updatedContent').html(response);
                            // alert(response);
                        }
                    });
                }
            });
        });



        function changeAnalytics(val) {
            var pastDate = new Date(val);
            var yesterdayDate = new Date(new Date().setDate(pastDate.getDate() - 1));
            var formattedYesterdayDate = formatDate(yesterdayDate);
            // console.log(formattedYesterdayDate);
            document.getElementById("inputDate").values = val;
            document.getElementById("todayAttendanceDate").innerHTML = val;
            document.getElementById("yesterdayWorkingDate").innerHTML = formattedYesterdayDate;
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