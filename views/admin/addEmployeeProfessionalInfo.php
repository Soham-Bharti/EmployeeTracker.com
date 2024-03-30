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

$techErr = $joiningDateErr = $salaryErr = $joiningDateErr = $bondPeriodErr = $noticePeriodErr = "";
$tech = $joiningDate = $salary = $joiningDate = $noticePeriodDays = $noticePeriodMonths = $bondPeriodYears = $bondPeriodMonths = "";

if (isset($_GET['id'])) $desiredUserId = $_GET['id'];
// echo $desiredUserId;

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$flag = true;
if (isset($_POST['add'])) {
    // print_r($_POST);
    $desiredUserId = test_input($_POST['userId']);
    $joiningDate = test_input($_POST['joiningDate']);
    $tech = test_input($_POST['tech']);
    $salary = test_input($_POST['salary']);
    $noticePeriodMonths = test_input($_POST['noticePeriodMonths']);
    $noticePeriodDays = test_input($_POST['noticePeriodDays']);


    if (isset($_POST['bondPeriodMonths']) || isset($_POST['bondPeriodYears'])) {
        $bondIsSet = true;
        $bondPeriodMonths = test_input($_POST['bondPeriodMonths']);
        $bondPeriodYears = test_input($_POST['bondPeriodYears']);
        if ($bondPeriodMonths >= 12) {
            $bondPeriodErr = '* Increase year when months are more than 11';
            $flag = false;
        }
    }

    if (empty($joiningDate) || $joiningDate == '') {
        $joiningDateErr = 'Required';
        $flag = false;
    } else {
        // $sql = "SELECT created_at from users where id = '$desiredUserId' and deleted_at is null order by created_at limit 1";
        $result = $adminObject->showEmployeeAllDetails($desiredUserId);
        if (mysqli_num_rows($result) == 1) {
            while ($row = mysqli_fetch_assoc($result)) {
                $registrationDateTime = $row['created_at'];
                $registrationTimeStamp = strtotime($registrationDateTime);
                $registrationDate = date('Y-m-d', $registrationTimeStamp);
                // echo $registrationDate; 
            }
        } else {
            echo "No record found for the user, may be user is no more existing in db";
        };
        if ($registrationDate > $joiningDate) {
            $joiningDateErr = "Joining Date is earlier than Registration Date";
            $flag = false;
        }
    }

    if (empty($salary)) {
        $salaryErr = 'Required';
        $flag = false;
    }

    if (empty($noticePeriodDays) && empty($noticePeriodMonths)) {
        $noticePeriodErr = 'Required';
        $flag = false;
    } else if ($noticePeriodDays >= 30) {
        $noticePeriodErr = 'Increase month when days are more than 29';
        $flag = false;
    }

    if ($flag) {
        // sending data to data base
        if ($noticePeriodDays == '') $noticePeriodDays = '0';
        if ($noticePeriodMonths == '') $noticePeriodMonths = '0';
        $noticePeriod = "$noticePeriodMonths months $noticePeriodDays days";

        if ($bondPeriodYears == '') $bondPeriodYears = '0';
        if ($bondPeriodMonths == '') $bondPeriodMonths = '0';
        $bondPeriod = "$bondPeriodYears years $bondPeriodMonths months";
        $result = $adminObject->addEmployeeProfessionalDetails($desiredUserId, $salary, $tech, $joiningDate, $noticePeriod, $bondPeriod);
        if ($result) {
            echo "<br>Record inserted successfully<br>";
        } else {
            echo "<br>Error occurred while inserting into table : " . mysqli_error($conn); // Print any errors returned by MySQL
        }

        // if everthing if well then redirecting the admin to ldashboard
        $_SESSION['addEmployeeInfoStatus'] = 'success';
        header("Location: viewAllEmployees.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee Professional Info</title>
    <link rel="stylesheet" href="../../Styles/updateemployee.css">
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
                    <a class="nav-link" href="viewAllEmployees.php?id=<?php echo $desiredUserId ?>">Back</a>
                </li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>

        </div>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-2"><span class='text-info'>Add</span> Employees' Professional Info</h2>
    <div class="container mt-3">
        <div class="col-md-7">
            <div class="my-3 d-flex align-items-center justify-content-around gap-4">
                <?php
                // $sql = "select name, profile_url, email from users where id = '$desiredUserId' and deleted_at is null";
                $result = $adminObject->showEmployeeAllDetails($desiredUserId);
                // var_dump($result);
                // exit();
                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_assoc($result);
                    $name = $row['name'];
                    $email = $row['email'];
                    $profile = $row['profile_url'];
                    if (empty($profile)) {
                        $profile = 'defaultImg.webp';
                    }
                }
                ?>
                <div class="d-inline-block profile-img w-25">
                    <img src="<?php echo "../../Images/" . $profile ?>" alt="No profile to show" class="img-thumbnail object-fit-contain border rounded-circle  mb-2">
                </div>
                <div class='text-center'>
                    <p>Emp ID: <span class="fw-bold text-secondary"><?php echo $desiredUserId ?></span></p>
                    <p class="fw-bold text-secondary"><?php echo $name ?></p>
                    <p class="fw-bold text-secondary"><?php echo $email ?></p>
                </div>

            </div>
            <hr>
            <form action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="post" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Joining Date <span>* <?php echo $joiningDateErr ?></span></label>
                    <input type="date" name="joiningDate" class="form-control" max="<?php echo $date = date("Y-m-d") ?>">
                </div>

                <div class="mb-3">
                    <div class="col-auto">
                        <label for="tech" class="col-form-label" checked='<?php echo $tech ?>'>Tech <span>* <?php echo $techErr ?></span></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tech" value="android" />
                        <label class="form-check-label" for="tech">Android</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tech" value="ios" />
                        <label class="form-check-label" for="tech">IOS</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tech" value="php" checked />
                        <label class="form-check-label" for="tech">PHP</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tech" value="reactNative" />
                        <label class="form-check-label" for="tech">React Native</label>
                    </div>

                </div>



                <div class="mb-3">
                    <label class="form-label">Salary <span>* <?php echo $salaryErr ?></span></label>
                    <input type="number" min="0.00" max="1000000.00" step="0.10" class="form-control" name="salary" value='<?php echo $dob ?>'>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bond Period <span><?php echo $bondPeriodErr ?></span></label>
                    <div class='d-flex gap-3 align-items-center justify-content-around'>
                        <div class='d-flex gap-1 align-items-center'>
                            <label class="form-label">Years:</label>
                            <input type="number" class="form-control" name="bondPeriodYears">
                        </div>
                        <div class='d-flex gap-1 align-items-center'>
                            <label class="form-label">Months:</label>
                            <input type="number" class="form-control" name="bondPeriodMonths">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notice Period <span>* <?php echo $noticePeriodErr ?></span></label>
                    <div class='d-flex gap-3 align-items-center justify-content-around'>
                        <div class='d-flex gap-1 align-items-center'>
                            <label class="form-label">Months:</label>
                            <input type="number" class="form-control" name="noticePeriodMonths">
                        </div>
                        <div class='d-flex gap-1 align-items-center'>
                            <label class="form-label">Days:</label>
                            <input type="number" class="form-control" name="noticePeriodDays">
                        </div>
                    </div>
                </div>
                <input type="hidden" name="userId" value="<?php echo $desiredUserId ?>">
                <div class="buttons">
                    <input type="submit" name="add" class="btn btn-dark btn-lg" value="Add">
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