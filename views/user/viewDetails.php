<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/User.php';

// print_r($_SESSION);
if ($_SESSION['role'] !== 'emp') {
    header('Location: ../common/login.php');
    exit();
}

if (isset($_GET['id'])) $desiredUserId = $_GET['id'];
$userObject = new User($desiredUserId);

if (isset($desiredUserId)) {
    $result = $userObject->showDetails();
    if (mysqli_num_rows($result) == 1) {
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['name'];
            $email = $row['email'];
            $mobile = $row['mobile'];
            $profile = $row['profile_url'];
            if (empty($profile)) {
                $profile = 'defaultImg.webp';
            }
            $gender = $row['gender'];
            $city = $row['city'];
            $state = $row['state'];
            $dob = $row['date_of_birth'];
            $tech = $row['technology_assigned'];
            $total_seconds = $row['total_seconds'];
            $joining_date = $row['joining_date'];
            $salary = $row['salary'];
            if (empty($salary)) $salary = 'N/A';
            $bond_period = $row['bond_period'];
            if (empty($bond_period)) {
                $bond_period_years = 'N/A';
                $bond_period_months = 'N/A';
            } else {
                $bond_period_years = explode(' ', $bond_period)[0];
                $bond_period_months = explode(' ', $bond_period)[2];
            }
            $notice_period = $row['notice_period'];
            if (empty($notice_period)) {
                $notice_period_months = 'N/A';
                $notice_period_days = 'N/A';
            } else {
                $notice_period_months = explode(' ', $notice_period)[0];
                $notice_period_days = explode(' ', $notice_period)[2];
            }

            // seconds to hours, minutes and seconds
            $hours = floor($total_seconds / 3600);
            $minutes = floor(($total_seconds % 3600) / 60);
            $seconds = $total_seconds % 60;
            $formatted_time = sprintf("%02d Hours %02d Minutes %02d Seconds", $hours, $minutes, $seconds);
        }
    }
} else {
    echo "User Id not set!";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo explode(' ', $name)[0] ?> - Details</title>
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
                <a class="nav-link" href="userDashboard.php">Back</a>
            </li>
        </ul>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-2"><span class='gradient-custom-2'><?php echo $name ?></span> - All Details</h2>
    <div class="container mt-3">
        <div class="col-md-7">
            <div class="my-3 d-flex align-items-center justify-content-around gap-4">
                <div class="d-inline-block profile-img w-25">
                    <img src="<?php echo "../../Images/" . $profile ?>" alt="No profile to show" class="img-thumbnail object-fit-contain border rounded-circle  mb-2">
                </div>
                <div class='text-center'>
                    <p class='d-flex justify-content-center align-items-center gap-2'><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-badge" viewBox="0 0 16 16">
                            <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                            <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492z" />
                        </svg><span class="fw-bold text-secondary"><?php echo $desiredUserId ?></span>
                    </p>
                    <p class='d-flex justify-content-center align-items-center gap-2'><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                        </svg><span class="fw-bold text-secondary"><?php echo $name ?></span>
                    </p>
                    <p class='d-flex justify-content-center align-items-center gap-2'><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
                        </svg><span class="fw-bold text-secondary">+91 <?php echo $mobile ?></span>
                    </p>
                    <p class='d-flex justify-content-center align-items-center gap-2'><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-at-fill" viewBox="0 0 16 16">
                            <path d="M2 2A2 2 0 0 0 .05 3.555L8 8.414l7.95-4.859A2 2 0 0 0 14 2zm-2 9.8V4.698l5.803 3.546zm6.761-2.97-6.57 4.026A2 2 0 0 0 2 14h6.256A4.5 4.5 0 0 1 8 12.5a4.49 4.49 0 0 1 1.606-3.446l-.367-.225L8 9.586zM16 9.671V4.697l-5.803 3.546.338.208A4.5 4.5 0 0 1 12.5 8c1.414 0 2.675.652 3.5 1.671" />
                            <path d="M15.834 12.244c0 1.168-.577 2.025-1.587 2.025-.503 0-1.002-.228-1.12-.648h-.043c-.118.416-.543.643-1.015.643-.77 0-1.259-.542-1.259-1.434v-.529c0-.844.481-1.4 1.26-1.4.585 0 .87.333.953.63h.03v-.568h.905v2.19c0 .272.18.42.411.42.315 0 .639-.415.639-1.39v-.118c0-1.277-.95-2.326-2.484-2.326h-.04c-1.582 0-2.64 1.067-2.64 2.724v.157c0 1.867 1.237 2.654 2.57 2.654h.045c.507 0 .935-.07 1.18-.18v.731c-.219.1-.643.175-1.237.175h-.044C10.438 16 9 14.82 9 12.646v-.214C9 10.36 10.421 9 12.485 9h.035c2.12 0 3.314 1.43 3.314 3.034zm-4.04.21v.227c0 .586.227.8.581.8.31 0 .564-.17.564-.743v-.367c0-.516-.275-.708-.572-.708-.346 0-.573.245-.573.791" />
                        </svg><span class="fw-bold text-secondary"><?php echo $email ?></span>
                    </p>
                </div>
            </div>
            <hr>
            <form action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3 d-flex align-items-center justify-content-center">
                    <label class="form-label w-50 text-center">Total Working (current month) - </label>
                    <label class="form-label w-50 text-center fw-bold text-primary"><?php echo $formatted_time ?></label>
                </div>

                <?php
                if ($salary != 'N/A') {
                    $result = $userObject->daysInCurrentMonth();
                    $row = mysqli_fetch_assoc($result);
                    $numberOfDays = $row['days_in_current_month'];
                    $perDaySalary = $salary / $numberOfDays;
                    $perHourSalary = $perDaySalary / 24;
                    $perMinuteSalary = $perHourSalary / 60;
                    $desiredSalaryToPay = round($perMinuteSalary * ($total_seconds / 60), 2);
                }
                ?>
                <div class="mb-3 d-flex align-items-center justify-content-center">
                    <label class="form-label w-50 text-center">Salary to debit (till today) - </label>
                    <label class="form-label w-50 text-center fw-bold text-success"><?php echo $salary != 'N/A' ? "Rs. $desiredSalaryToPay/-" : 'Salary not set' ?></label>
                </div>

                <div class="mb-3 d-flex align-items-center justify-content-center">
                    <label class="form-label w-25">DoB - </span></label>
                    <input type="text" name="dob" class="form-control w-25" disabled value='<?php echo $dob ?>'>
                </div>
                <div class="mb-3 d-flex align-items-center justify-content-center">
                    <label class="form-label w-25">City - </span></label>
                    <input type="text" name="city" class="form-control w-25" disabled value='<?php echo ucfirst($city) ?>'>
                </div>
                <div class="mb-3 d-flex align-items-center justify-content-center">
                    <label class="form-label w-25">State - </span></label>
                    <input type="text" name="state" class="form-control w-25" disabled value='<?php echo ucfirst($state) ?>'>
                </div>
                <div class="mb-3 d-flex align-items-center justify-content-center">
                    <label class="form-label w-25">Gender - </span></label>
                    <input type="text" name="dob" class="form-control w-25" disabled value='<?php echo ucfirst($gender) ?>'>
                </div>
                <div class="mb-3 d-flex align-items-center justify-content-center">
                    <label class="form-label w-25">Joining Date - </span></label>
                    <input type="date" name="joiningDate" class="form-control w-25" disabled value='<?php echo $joining_date ?>'>
                </div>

                <div class="mb-3 d-flex align-items-center justify-content-center gap-3">
                    <div class="col-auto">
                        <label for="tech" class="col-form-label" checked='<?php echo $tech ?>'>Tech Assigned:</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tech" value="android" <?php echo $tech == 'android' ? 'checked' : 'disabled' ?> />
                        <label class="form-check-label" for="tech">Android</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tech" value="ios" <?php echo $tech == 'ios' ? 'checked' : 'disabled' ?> />
                        <label class="form-check-label" for="tech">IOS</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tech" value="php" <?php echo $tech == 'php' ? 'checked' : 'disabled' ?> />
                        <label class="form-check-label" for="tech">PHP</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tech" value="reactNative" <?php echo $tech == 'reactNative' ? 'checked' : 'disabled' ?> />
                        <label class="form-check-label" for="tech">React Native</label>
                    </div>

                </div>



                <div class="mb-3 d-flex align-items-center justify-content-center">
                    <label class="form-label w-25">Salary (in Rupees) - </span></label>
                    <input type="text" min="0.00" max="1000000.00" step="0.10" class="form-control w-25" name="salary" disabled value='<?php echo $salary ?>'>
                </div>

                <div class="mb-3 d-flex align-items-center justify-content-center gap-2">
                    <label class="form-label">Bond Period - </label>
                    <div class='d-flex gap-3 align-items-center justify-content-around'>
                        <div class='d-flex gap-1 align-items-center'>
                            <label class="form-label">Years:</label>
                            <input type="text" class="form-control" disabled name="bondPeriodYears" value="<?php echo $bond_period_years ?>">
                        </div>
                        <div class='d-flex gap-1 align-items-center'>
                            <label class="form-label">Months:</label>
                            <input type="text" class="form-control" disabled name="bondPeriodMonths" value="<?php echo $bond_period_months ?>">
                        </div>
                    </div>
                </div>

                <div class="mb-3 d-flex align-items-center justify-content-center gap-2">
                    <label class="form-label">Notice Period - </label>
                    <div class='d-flex gap-3 align-items-center justify-content-around'>
                        <div class='d-flex gap-1 align-items-center'>
                            <label class="form-label">Months:</label>
                            <input type="text" class="form-control" disabled name="noticePeriodMonths" value="<?php echo $notice_period_months ?>">
                        </div>
                        <div class='d-flex gap-1 align-items-center'>
                            <label class="form-label">Days:</label>
                            <input type="text" class="form-control" disabled name="noticePeriodDays" value="<?php echo $notice_period_days ?>">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="hidden" class="form-control" name="id" value='<?php echo $desiredUserId ?>'>
                </div>

            </form>
        </div>
    </div>

    <!-- footer here -->
    <?php include('../common/footer.php'); ?>
    <!-- footer ends -->

</body>

</html>