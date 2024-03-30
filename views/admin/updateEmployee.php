<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/Admin.php';
require '../../Classes/FileUpload.php';
$adminObject = new Admin();
$fileUploadObject = new FileUpload();

// print_r($_SESSION);
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../start/login.php');
}
$nameErr = $emailErr = $dobErr = $genderErr = $mobileErr = $imageErr = $cityErr = $stateErr = $addressErr = "";
if (isset($_GET['id'])) $desiredUserId = $_GET['id'];
if (isset($desiredUserId)) {
    $result = $adminObject->showEmployeeAllDetails($desiredUserId);
    if (mysqli_num_rows($result) == 1) {
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['name'];
            $email = $row['email'];
            $dob = $row['date_of_birth'];
            $gender = $row['gender'];
            $mobile = $row['mobile'];
            $address = $row['address'];
            $city = $row['city'];
            $state = $row['state'];
            $profile = $row['profile_url'];
            if (empty($profile)) {
                $profile = 'defaultImg.webp';
            }
        }
    }
} else {
    // echo "Emp User ID parameter is missing - check your session";
}


function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$flag = true;
if (isset($_POST['submit'])) {
    // print_r($_POST);
    $name = test_input($_POST['name']);
    $email = test_input($_POST['email']);
    $dob = test_input($_POST['dob']);
    $gender = test_input($_POST['gender']);
    $mobile = test_input($_POST['mobile']);
    $address = test_input($_POST['address']);
    $city = test_input($_POST['city']);
    $state = test_input($_POST['state']);
    $profile = test_input($_POST['profile']);
    // var_dump($profile);
    $id = test_input($_POST['id']);

    if (empty($name)) {
        $nameErr = 'Required';
        $flag = false;
        $_SESSION['UpdateStatus'] = 'fail';
    } else {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
            $nameErr = "Only letters and white space allowed";
            $flag = false;
            $_SESSION['UpdateStatus'] = 'fail';
        }
    }

    if (empty($email)) {
        $emailErr = 'Required';
        $flag = false;
        $_SESSION['UpdateStatus'] = 'fail';
    } else {
        if (!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+(@gmail.com)$/", $email)) {
            $emailErr = "Invalid email format";
            $flag = false;
            $_SESSION['UpdateStatus'] = 'fail';
        }
    }

    if (empty($gender)) {
        $genderErr = 'Required';
        $flag = false;
        $_SESSION['UpdateStatus'] = 'fail';
    }

    if (empty($mobile)) {
        $mobileErr = 'Required';
        $flag = false;
        $_SESSION['UpdateStatus'] = 'fail';
    } else {
        if (!preg_match("/^[6-9]{1}\d{9}$/", $mobile)) {
            $mobileErr = "Invalid mobile number";
            $flag = false;
            $_SESSION['UpdateStatus'] = 'fail';
        }
    }

    if (empty($dob)) {
        $dobErr = 'Required';
        $flag = false;
        $_SESSION['UpdateStatus'] = 'fail';
    } else {
        $year = explode('-', $dob);
        if ($year[0] > 2010) {
            $dobErr = 'Choose a DoB before 2010';
            $flag = false;
            $_SESSION['UpdateStatus'] = 'fail';
        }
    }

    if (empty($city)) {
        $cityErr = 'Required';
        $flag = false;
        $_SESSION['UpdateStatus'] = 'fail';
    } else {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $city)) {
            $cityErr = "Only letters and white space allowed";
            $flag = false;
            $_SESSION['UpdateStatus'] = 'fail';
        }
    }
    if (empty($state)) {
        $stateErr = 'Required';
        $flag = false;
        $_SESSION['UpdateStatus'] = 'fail';
    } else {
        if (!preg_match("/^[a-zA-Z-' ]*$/", $state)) {
            $stateErr = "Only letters and white space allowed";
            $flag = false;
            $_SESSION['UpdateStatus'] = 'fail';
        }
    }

    if (empty($address)) {
        $addressErr = 'Required';
        $flag = false;
        $_SESSION['UpdateStatus'] = 'fail';
    }

    if ($flag && $_FILES['image']['name'] !== '') {
        $desiredResult = $fileUploadObject->upload($name);
        if (!strpos($desiredResult, ' ')) {
            $fileNameNew = $desiredResult;
        } else {
            $imageErr = $desiredResult;
            $flag = false;
        }
    } else $fileNameNew = $profile;


    // print_r($_POST);

    if ($flag) {
        // sending data to data base
        $result = $adminObject->updateEmployeeBasicDetails($name, $email, $mobile, $address, $gender, $dob, $city, $state, $fileNameNew, $id);
        if ($result) {
            echo "<br>Record updated successfully<br>";
            $_SESSION['UpdateStatus'] = 'success';
        } else {
            echo "<br>Error occured while inserting into table : " . mysqli_error($conn);
            $_SESSION['UpdateStatus'] = 'fail';
        }
        header("Location: viewEmployeeAllDetails.php?id=$id");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Employee</title>
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
                    <a class="nav-link" href="viewEmployeeAllDetails.php?id=<?php echo $desiredUserId ?>">Back</a>
                </li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>

        </div>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-2"><span class='text-info'>Update</span> Employees' Personal Details</h2>
    <div class="container mt-3">
        <div class="col-md-7">
            <!-- toast after fail updation -->
            <?php if (isset($_SESSION['UpdateStatus']) && $_SESSION['UpdateStatus'] == 'fail') { ?>
                <div class="toast show m-auto hide">
                    <div class="toast-header bg-danger text-white ">
                        <strong class="me-auto">Something went wrong!</strong>
                        <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            <?php }
            $_SESSION['UpdateStatus'] = '' ?>
            <form action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="post" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="col-form-label">Name <span>* <?php echo $nameErr ?></span></label>
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Joey Tribbiani" value='<?php echo $name ?>'>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email address <span>* <?php echo $emailErr ?></span></label>
                    <input type="email" class="form-control" name="email" placeholder="name@example.com" value='<?php echo $email ?>'>
                </div>

                <div class="mb-3">
                    <div class="col-auto">
                        <label for="gender" class="col-form-label" checked='<?php echo $gender ?>'>Gender <span>* <?php echo $genderErr ?></span></label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="female" <?php echo $gender == 'female' ? 'checked' : '' ?> />
                        <label class="form-check-label" for="gender">Female</label>
                    </div>


                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="male" <?php echo $gender == 'male' ? 'checked' : '' ?> />
                        <label class="form-check-label" for="gender">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" value="others" <?php echo $gender == 'others' ? 'checked' : '' ?> />
                        <label class="form-check-label" for="gender">Others</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mobile <span>* <?php echo $mobileErr ?></span></label>
                    <input type="tel" name="mobile" class="form-control" placeholder="9876543210" maxlength="10" value='<?php echo $mobile ?>'>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date of Birth <span>* <?php echo $dobErr ?></span></label>
                    <input type="date" class="form-control" name="dob" value='<?php echo $dob ?>'>
                </div>
                <div class="mb-3">
                    <label class="form-label">Profile Picture <span>* (displaying your current profile, CHOOSE only if you want to change) <?php echo $imageErr ?></span></label>
                    <div class="d-inline-block profile-img w-25">
                        <img src="<?php echo "../../Images/" . $profile ?>" alt="No profile to show" class="img-thumbnail object-fit-contain border rounded-circle  mb-2">
                    </div>
                    <input class="form-control" type="file" name="image">
                </div>

                <div class="mb-3">
                    <label class="form-label">Address <span>* <?php echo $addressErr ?></span></label>
                    <input type="text" class="form-control" name="address" placeholder="628 Iskon Emporio" value='<?php echo $address ?>'>
                </div>
                <div class="mb-3">
                    <label class="form-label">City <span>* <?php echo $cityErr ?></span></label>
                    <input type="text" class="form-control" name="city" placeholder="Ahemdabad" value='<?php echo $city ?>'>
                </div>
                <div class="mb-3">
                    <label class="form-label">State <span>* <?php echo $stateErr ?></span></label>
                    <input type="text" class="form-control" name="state" placeholder="Gujarat" value='<?php echo $state ?>'>
                </div>
                <div class="mb-3">
                    <input type="hidden" class="form-control" name="id" value='<?php echo $desiredUserId ?>'>
                    <input type="hidden" class="form-control" name="profile" value='<?php echo $profile ?>'>
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