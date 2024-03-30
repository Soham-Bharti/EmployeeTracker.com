<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/Project.php';
$projectObject = new Project();

// print_r($_SESSION);
if ($_SESSION['role'] !== 'emp') {
    header('Location: ../start/login.php');
}
if (isset($_SESSION['id'])) $desiredUserId = $_SESSION['id'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMS | <?php echo $_SESSION['userName']; ?> </title>
    <?php include('../common/favicon.php');?>
    <link rel="stylesheet" href="../../Styles/view-pms.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class='d-flex flex-column min-vh-100'>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid d-flex align-items-center justify-content-between">

            <a href="../start/home.php" class="svg text-decoration-none d-flex align-items-center">
                <img src="../../Images/mainIcon.gif" alt='svg here'>
                <span class='text-success fw-bold'>EmployeeTracker.com</span>
            </a>


            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="userDashboard.php">Back</a>
                </li>
                <li class="nav-item">
                    <a href="viewProjects.php?id=<?php echo $desiredUserId ?>" class="nav-link">My Projects</a>
                </li>
                <li class="nav-item">
                    <a href="addProjectDailyTask.php?id=<?php echo $desiredUserId ?>" class="nav-link">Add Daily Task</a>
                </li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>

        </div>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-3">Welcome to your <span class='gradient-custom-2'>PMS</span> dashboard</h2>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <div class='fs-4'>
                <?php
                $user = $_SESSION['userName'];
                $userId = $_SESSION['id'];
                echo "Emp Id: <b>" . $userId . "</b> | " . ucwords($user);
                ?>
            </div>
        </div>
        <!-- toast after successful change of password -->
        <?php if ($_SESSION['userChangePasswordStatus'] == 'success') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-success text-white ">
                    <strong class="me-auto">Password changed successfully!</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php } $_SESSION['userChangePasswordStatus'] = '' ?>
        <!-- toast ends -->
        <!-- toast after successful check-in -->
        <?php if (isset($_SESSION['checkInMessage']) && $_SESSION['checkInMessage'] == 'success') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-success text-white ">
                    <strong class="me-auto">You are Checked-in successfully!</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php } $_SESSION['checkInMessage'] = '' ?>
        <!-- toast ends -->
        <!-- toast after successful check-in -->
        <?php if (isset($_SESSION['checkOutMessage']) && $_SESSION['checkOutMessage'] == 'success') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-warning text-white ">
                    <strong class="me-auto">You are Checked-out successfully!</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php } $_SESSION['checkOutMessage'] = '' ?>
        <!-- toast ends -->
        <!-- toast after fail check-in -->
        <?php if (isset($_SESSION['checkInLimitMessage']) && $_SESSION['checkInLimitMessage'] == 'success') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-danger text-white ">
                    <strong class="me-auto">Check-in limit reached!</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php } $_SESSION['checkInLimitMessage'] = ''; ?>
        <!-- toast ends -->
        <!-- toast after successfully adding daily task -->
        <?php if (isset($_SESSION['AddDailyTaskStatus']) && $_SESSION['AddDailyTaskStatus'] == 'success') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-success text-white ">
                    <strong class="me-auto">PMS filled successfully!</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php } $_SESSION['AddDailyTaskStatus'] = ''; ?>
        <!-- toast ends -->
        <h2 class="text-center mt-5">Showing your <span class='gradient-custom-1'>LAST 10</span> activity</h2>
        <div class="mt-3">
            <table>
                <tr>
                    <th>S.Number</th>
                    <th>Project Title</th>
                    <th>Project Id</th>
                    <th>Created Day - Date - Time</th>
                    <th>Summary</th>
                </tr>
                <?php
                $result = $projectObject -> showPMSbyUserId($desiredUserId);
                $seialNumber = 1;
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        if( $seialNumber == 11) break;
                        $time = strtotime($row["created_dateTime"]);
                        ?>
                        <tr>
                            <td><?php echo $seialNumber++ ?></td>
                            <td class='fw-bold'>
                                <?php
                                echo $row['projectTitle'];
                                ?>
                            </td>
                            <td>
                                <?php
                                echo $row['projectId'];
                                ?>
                            </td>
                            <td>
                                <?php
                                echo date('D - d M Y - h:ia', $time);
                                ?>
                            </td>
                            
                            <td class='w-50'>
                                <?php
                               echo $row['summary'];
                                ?>
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