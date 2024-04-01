<?php
session_start();
require_once '../../config/dbConnection.php';
require_once '../../Classes/Project.php';
require_once '../../Classes/Admin.php';
$projectObject = new Project();
$adminObject = new Admin();

// print_r($_SESSION);
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../start/login.php');
}

if (isset($_POST['newDate'])) {
    $date = $_POST['newDate'];
   echo "<div>
    <div class='card' style='width: 17rem;'>
        <img class='card-img-top p-4 w-75 m-auto' src='../../Images/calendar.png' alt='Card image cap'>
        <div class='card-body'>
            <div class='d-flex justify-content-around align-items-center p-0 m-0'>
                <p class='card-title fs-6'>Attendance on <br />(<span id='todayAttendanceDate'>".$date; echo "</span>): </p>";
                $result = $adminObject->totalEmployeesCount($date);
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $totalEmployeesCount =  $row['totalEmployeesCount'];
                }
                $result = $adminObject->totalCheckedInUsersOnDate($date);
                $totalCheckedInUsersTodayCount  = mysqli_num_rows($result);
                echo "<p class='fw-bold display-6'><span class='";$totalCheckedInUsersTodayCount >= 0.75 * $totalEmployeesCount ? 'text-success' : 'text-danger'; echo "'>";echo $totalCheckedInUsersTodayCount; echo "</span>/"; echo $totalEmployeesCount; echo "</p>
            </div>";
            $absentEmployees = [];
            $result = $adminObject->showAbsentEmployeesOnDate($date);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    array_push($absentEmployees,  $row['name']);
                }
            }
            echo "<div class='accordion accordion-flush' id='accordionAbsentees'>
                <div class='accordion-item'>
                    <h2 class='accordion-header'>
                        <button class='accordion-button collapsed text-primary fw-bold' type='button' data-bs-toggle='collapse' data-bs-target='#flush-collapseAbsentees' aria-expanded='false' aria-controls='flush-collapseAbsentees'>
                            Show Absentees
                        </button>
                    </h2>
                    <div id='flush-collapseAbsentees' class='accordion-collapse collapse' data-bs-parent='#accordionAbsentees'>
                        <div class='accordion-body'>";
                            foreach ($absentEmployees as $name) {
                            echo "<p class='m-0 p-0'>"; echo $name; echo "</p>";}
                        echo "</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div>
    <div class='card' style='width: 17rem;'>
        <img class='card-img-top p-3 w-75 m-auto' src='../../Images/working-time.png' alt='Card image cap'>
        <div class='card-body'>
            <div class='d-flex justify-content-around align-items-center p-0 m-0'>
                <p class='card-title fs-6'>Work Time
                    < 8.45Hrs. on <br />(<span id='yesterdayWorkingDate'>";echo date('Y-m-d', strtotime('-1 day', strtotime($date))); echo "</span>):
                </p>";
                $result = $adminObject->showEmployeesWithLessWorkingHoursYesterday($date);
                $showEmployeesWithLessWorkingHoursYesterdayArray = [];
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $showEmployeesWithLessWorkingHoursYesterdayArray[$row['name']] = $row['total_seconds'];
                    }
                }
              
                echo "<p class='fw-bold display-6'>";echo sizeof($showEmployeesWithLessWorkingHoursYesterdayArray); echo"</p>
            </div>
            <div class='accordion accordion-flush' id='showEmployeesWithLessWorkingHoursYesterday'>
                <div class='accordion-item'>
                    <h2 class='accordion-header'>
                        <button class='accordion-button collapsed text-primary fw-bold' type='button' data-bs-toggle='collapse' data-bs-target='#flush-showEmployeesWithLessWorkingHoursYesterday' aria-expanded='false' aria-controls='flush-showEmployeesWithLessWorkingHoursYesterday'>
                            Show List
                        </button>
                    </h2>
                    <div id='flush-showEmployeesWithLessWorkingHoursYesterday' class='accordion-collapse collapse' data-bs-parent='#showEmployeesWithLessWorkingHoursYesterday'>
                        <div class='accordion-body'>";
                            foreach ($showEmployeesWithLessWorkingHoursYesterdayArray as $name => $total_seconds) {
                                $hours = floor($total_seconds / 3600);
                                $minutes = floor(($total_seconds % 3600) / 60);
                                $seconds = $total_seconds % 60;
                                $formatted_time = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                             echo "<p>";echo explode(' ', $name)[0] . " - $formatted_time"; echo"</p>
                                <hr>";
                                }
                                
                        echo "</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div>
    <div class='card' style='width: 17rem;'>
        <img class='card-img-top p-3 w-75 m-auto' src='../../Images/working-time.png' alt='Card image cap'>
        <div class='card-body'>
            <div class='d-flex justify-content-around align-items-center p-0 m-0'>
                <p class='card-title fs-6'>Attendance on <br />(<span id='yesterdayWorkingDate'>";echo date('Y-m-d', strtotime('-1 day', strtotime($date))); echo"</span>):
                </p>";
                $dateObject = new DateTime($date);
                $dateObject->sub(new DateInterval('P1D'));
                $yesterdayDate = $dateObject->format('Y-m-d');
                $result = $adminObject->totalEmployeesCount($yesterdayDate);
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $totalEmployeesYesterdayCount =  $row['totalEmployeesCount'];
                }
                $result = $adminObject->totalCheckedInUsersOnDate($yesterdayDate);
                $totalCheckedInUsersYesterdayCount  = mysqli_num_rows($result);
                echo "<p class='fw-bold display-6'><span class=";echo $totalCheckedInUsersYesterdayCount >= 0.75 * $totalEmployeesYesterdayCount ? 'text-success' : 'text-danger'; echo ">"; echo $totalCheckedInUsersYesterdayCount; echo"</span>/";echo $totalEmployeesYesterdayCount; echo"</p>
            </div>";
            $absentEmployees = [];
            $result = $adminObject->showAbsentEmployeesOnDate($yesterdayDate);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    array_push($absentEmployees,  $row['name']);
                }
            }
            
            echo "<div class='accordion accordion-flush' id='showEmployeesYesterdayAttendance'>
                <div class='accordion-item'>
                    <h2 class='accordion-header'>
                        <button class='accordion-button collapsed text-primary fw-bold' type='button' data-bs-toggle='collapse' data-bs-target='#flush-showEmployeesYesterdayAttendance' aria-expanded='false' aria-controls='flush-showEmployeesYesterdayAttendance'>
                            Show Absentees
                        </button>
                    </h2>
                    <div id='flush-showEmployeesYesterdayAttendance' class='accordion-collapse collapse' data-bs-parent='#showEmployeesYesterdayAttendance'>
                        <div class='accordion-body'>";
                            foreach ($absentEmployees as $name) {
                            echo "<p class='m-0 p-0'>";echo $name; echo "</p>";}
                        echo "</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div>
    <div class='card' style='width: 17rem;'>
        <img class='card-img-top w-75 m-auto' src='../../Images/pms.png' alt='Card image cap'>
        <div class='card-body'>
            <div class='d-flex justify-content-around align-items-center p-0 m-0'>
                <p class='card-title fs-6'>Missed PMS on <br />(<span id='yesterdayPMSDate'>";echo date('Y-m-d', strtotime('-1 day', strtotime($date))); echo "</span>): </p>";
                $result = $projectObject->employeesWithNoPMSYesterday($date);
                $employeesWithNoPMSYesterdayArray = [];
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        array_push($employeesWithNoPMSYesterdayArray,  $row['name']);
                    }
                }
                
                echo "<p class='fw-bold display-6'>";echo sizeof($employeesWithNoPMSYesterdayArray); echo "</p>
            </div>
            <div class='accordion accordion-flush' id='employeesWithNoPMSYesterday'>
                <div class='accordion-item'>
                    <h2 class='accordion-header'>
                        <button class='accordion-button collapsed text-primary fw-bold' type='button' data-bs-toggle='collapse' data-bs-target='#flush-employeesWithNoPMSYesterday' aria-expanded='false' aria-controls='flush-employeesWithNoPMSYesterday'>
                            Show Members
                        </button>
                    </h2>
                    <div id='flush-employeesWithNoPMSYesterday' class='accordion-collapse collapse' data-bs-parent='#employeesWithNoPMSYesterday'>
                        <div class='accordion-body'>";
                  
                            foreach ($employeesWithNoPMSYesterdayArray as $name) {
                            echo "<p class='m-0 p-0'>";echo $name; echo "</p>";}
                        echo "</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>";
}
