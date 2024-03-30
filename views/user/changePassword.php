<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/User.php';

// print_r($_SESSION);
if ($_SESSION['role'] !== "emp") {
    header("Location: ../start/login.php");
}

$confirmNewPassword = $newPassword = $oldPassword =  "";
$confirmNewPasswordErr = $newPasswordErr = $oldPasswordErr = "";

$desiredUserId = $_SESSION['id'];
$userObject = new User($desiredUserId);
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$flag = true;
if (isset($_POST['change'])) {
    $oldPassword = test_input($_POST['oldPassword']);
    $newPassword = test_input($_POST['newPassword']);
    $confirmNewPassword = test_input($_POST['confirmNewPassword']);

    if (empty($oldPassword)) {
        $oldPasswordErr = 'Required';
        $flag = false;
    } else {
        $hashedPassword = md5($oldPassword);
    }

    if (empty($newPassword)) {
        $newPasswordErr = 'Required';
        $flag = false;
    } else {
        //     $password_regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/";
        //     if (!preg_match($password_regex, $password)) {
        //         $passwordErr = "Check that a password:<br>
        //         Has minimum 8 characters in length<br>
        //         At least one uppercase English letter<br>
        //         At least one lowercase English letter<br>
        //         At least one digit<br>
        //         At least one special character";
        //         $flag = false;
        //     }

    }

    if (empty($confirmNewPassword)) {
        $confirmNewPasswordErr = 'Required';
        $flag = false;
    } else {
        if ($confirmNewPassword !== $newPassword) {
            $confirmNewPasswordErr = "Password Mismatch";
            $flag = false;
        }
    }

    if ($flag) {
       $result = $userObject -> showDetails();
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
                $userOldHashedPassword = $row['password'];
            
        }
        if ($hashedPassword == $userOldHashedPassword) {
            // echo 'Password matched';
            $newHashedPassword = md5($newPassword);
            if ($newHashedPassword == $userOldHashedPassword) {
                $newPasswordErr = "New password can not be same as old password!";
            } else {
               $result = $userObject -> changePassword($newHashedPassword);
                if ($result) {
                    echo "Password changed successfully!";
                    $_SESSION['userChangePasswordStatus'] = 'success';
                    header("Location: userDashboard.php");
                } else {
                    $newPasswordErr =  "There was some Database Issue while updating your password!";
                    $oldPasswordErr =  "There was some Database Issue while updating your password!";
                    $confirmNewPasswordErr =  "There was some Database Issue while updating your password!";
                }
            }
        } else {
            $oldPasswordErr = "Password doesn't match";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changing Password...</title>
    <?php include('../common/favicon.php');?>
    <link rel="stylesheet" href="../../Styles/update-Employee.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class = 'd-flex flex-column min-vh-100'>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <a href="../start/home.php" class="svg text-decoration-none text-success d-flex align-items-center">
                <img src="../../Images/mainIcon.gif" alt='svg here'>
                <span class='fw-bold text-success'>EmployeeTracker.com</span>
            </a>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="userDashboard.php">Back</a>
                </li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>

        </div>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-2">Change your current <span class='gradient-custom-1'>Password</span></h2>
    <div class="container mt-3">
        <div class="col-md-7">
            <form action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="post">
                <div class="mb-3">
                    <div class="col-auto">
                        <label class="col-form-label">Curent Password <span>* <?php echo $oldPasswordErr ?></span></label>
                    </div>
                    <div class="col-auto">
                        <input type="password" name="oldPassword" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="col-auto">
                        <label class="col-form-label">New Password <span>* <?php echo $newPasswordErr ?></span></label>
                    </div>
                    <div class="col-auto">
                        <input type="password" name="newPassword" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="col-auto">
                        <label class="col-form-label">Confirm Password <span>* <?php echo $confirmNewPasswordErr ?></span></label>
                    </div>
                    <div class="col-auto">
                        <input type="password" name="confirmNewPassword" class="form-control">
                    </div>
                </div>
                <div class="buttons">
                    <input type="submit" name="change" class="btn btn-dark btn-lg" value="Confirm">
                    <input type="reset" name="reset" class="btn btn-dark btn-lg" value="Clear">
                </div>
            </form>
        </div>
    </div>

    <!-- footer here -->
    <?php include('../common/footer.php');?>
    <!-- footer ends -->

</body>

</html>