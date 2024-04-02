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
    // $date = '2024-03-28';
    $date = $_POST['newDate'];
    $dateObject = new DateTime($date);
    $dateObject->sub(new DateInterval('P1D'));
    $yesterdayDate = $dateObject->format('Y-m-d');
    $resultantJSONArray = [];

    $result = $adminObject->totalEmployeesCount($date);
    $totalEmployeesCountPresent = 0;
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $totalEmployeesCountPresent =  $row['totalEmployeesCount'];
    }
    $resultantJSONArray['totalEmployeesCountPresent'] = $totalEmployeesCountPresent;

    $result = $adminObject->totalCheckedInUsersOnDate($date);
    $totalCheckedInUsersTodayCountPresent  = mysqli_num_rows($result);
    $resultantJSONArray['totalCheckedInUsersTodayCountPresent'] = $totalCheckedInUsersTodayCountPresent;

    $absentEmployeesPresent = [];
    $result = $adminObject->showAbsentEmployeesOnDate($date);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $val = [$row['id'] => $row['name']];
            array_push($absentEmployeesPresent,  $val);
        }
    }
    $resultantJSONArray['absentEmployeesPresent'] = $absentEmployeesPresent;
    // first card data ends

    $result = $adminObject->showEmployeesWithLessWorkingHoursYesterday($date);
    $showEmployeesWithLessWorkingHoursYesterdayArrayPast = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $hours = floor($row['total_seconds'] / 3600);
            $minutes = floor(($row['total_seconds'] % 3600) / 60);
            $seconds =  $row['total_seconds'] % 60;
            $formatted_time = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
            $val = ['id' => $row['id'], 'name' => $row['name'], 'formatted_time' => $formatted_time];
            array_push($showEmployeesWithLessWorkingHoursYesterdayArrayPast, $val);
        }
    }
    $showEmployeesWithLessWorkingHoursYesterdayArrayCountPast = sizeof($showEmployeesWithLessWorkingHoursYesterdayArrayPast);
    $resultantJSONArray['showEmployeesWithLessWorkingHoursYesterdayArrayCountPast'] = $showEmployeesWithLessWorkingHoursYesterdayArrayCountPast;
    $resultantJSONArray['showEmployeesWithLessWorkingHoursYesterdayArrayPast'] = $showEmployeesWithLessWorkingHoursYesterdayArrayPast;
    // 2nd card adata ends

    $result = $adminObject->totalEmployeesCount($yesterdayDate);
    $totalEmployeesYesterdayCountPast = 0;
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $totalEmployeesYesterdayCountPast =  $row['totalEmployeesCount'];
    }
    $resultantJSONArray['totalEmployeesYesterdayCountPast'] = $totalEmployeesYesterdayCountPast;


    $result = $adminObject->totalCheckedInUsersOnDate($yesterdayDate);
    $totalCheckedInUsersYesterdayCountPast  = mysqli_num_rows($result);
    $resultantJSONArray['totalCheckedInUsersYesterdayCountPast'] = $totalCheckedInUsersYesterdayCountPast;

    $absentEmployeesPast = [];
    $result = $adminObject->showAbsentEmployeesOnDate($yesterdayDate);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $val = [$row['id'] => $row['name']];
            array_push($absentEmployeesPast,  $val);
        }
    }
    $resultantJSONArray['absentEmployeesPast'] = $absentEmployeesPast;
    // 3rd card data ends

    $result = $projectObject->employeesWithNoPMSYesterday($date);
    $employeesWithNoPMSYesterdayArrayPast = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $employeesWithNoPMSYesterdayArrayPast[] = ['id' => $row['id'], 'name' => $row['name']];
        }
    }
    $employeesWithNoPMSYesterdayArrayCountPast = sizeof($employeesWithNoPMSYesterdayArrayPast);
    $resultantJSONArray['employeesWithNoPMSYesterdayArrayCountPast'] = $employeesWithNoPMSYesterdayArrayCountPast;
    $resultantJSONArray['employeesWithNoPMSYesterdayArrayPast'] = $employeesWithNoPMSYesterdayArrayPast;

    echo json_encode($resultantJSONArray);
}
