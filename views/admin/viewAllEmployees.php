<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/Admin.php';
$adminObject = new Admin();

// print_r($_SESSION);
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../start/login.php');
}
if (isset($_GET['desiredUserId'])) {
    $desiredUserId = $_GET['desiredUserId'];
    $result = $adminObject->deleteEmployee($desiredUserId);
    if ($result) {
        $_SESSION['DeleteStatus'] = 'success';
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Employees details</title>
    <?php include('../common/favicon.php'); ?>
    <link rel="stylesheet" href="../../Styles/viewAll-Employees.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">

</head>

<body class='d-flex flex-column min-vh-100'>
    <nav class="navbar navbar-expand-lg bg-body-tertiary d-flex align-items-center justify-content-between px-5">
        <a href="../start/home.php" class="svg text-decoration-none text-success d-flex align-items-center">
            <img src="../../Images/mainIcon.gif" alt='svg here'>
            <span class='fw-bold text-success'>EmployeeTracker.com</span>
        </a>
        <ul class="navbar-nav mb-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="adminDashboard.php">Back</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="addEmployee.php">Add Employee</a>
            </li>
        </ul>
    </nav>
    <!-- nav ends -->

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


    <!-- toast after successful addEmployeeInfo professional -->
    <?php if (isset($_SESSION['addEmployeeInfoStatus']) && $_SESSION['addEmployeeInfoStatus'] == 'success') { ?>
        <div class="toast show m-auto hide">
            <div class="toast-header bg-success text-white ">
                <strong class="me-auto">Info added successfully!</strong>
                <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
            </div>
        </div>
    <?php }
    $_SESSION['addEmployeeInfoStatus'] = '' ?>
    <!-- toast after successful updateEmployeeInfo professional -->
    <?php if (isset($_SESSION['updateEmployeeInfoStatus']) && $_SESSION['updateEmployeeInfoStatus'] == 'success') { ?>
        <div class="toast show m-auto hide">
            <div class="toast-header bg-info text-white ">
                <strong class="me-auto">Info updated successfully!</strong>
                <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
            </div>
        </div>
    <?php }
    $_SESSION['updateEmployeeInfoStatus'] = '' ?>

    <div class="container mt-5 px-5">
        <div class="d-flex justify-content-between align-items-center">
            <div class="buttons d-flex justify-content-between gap-3 align-items-center">
                <!-- <form action="addEmployee.php" method="POST">
                    <input type="submit" name="add-submit" class="btn btn-success btn-lg" value="Add Employee">
                </form> -->
            </div>
        </div>
        <h2 class="text-center">Showing <span class='gradient-custom-1'>Employees'</span> details</h2>
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
        <!-- toast after successful addEmployeeInfo professional -->
        <?php if (isset($_SESSION['addEmployeeInfoStatus']) && $_SESSION['addEmployeeInfoStatus'] == 'success') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-success text-white ">
                    <strong class="me-auto">Info added successfully!</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php }
        $_SESSION['addEmployeeInfoStatus'] = '' ?>
        <!-- toast after successful updateEmployeeInfo professional -->
        <?php if (isset($_SESSION['updateEmployeeInfoStatus']) && $_SESSION['updateEmployeeInfoStatus'] == 'success') { ?>
            <div class="toast show m-auto hide">
                <div class="toast-header bg-info text-white ">
                    <strong class="me-auto">Info updated successfully!</strong>
                    <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php }
        $_SESSION['updateEmployeeInfoStatus'] = '' ?>

        <div class="mt-3">
            <table id="EmployeesTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Mobile</th>
                        <!-- <th>Date of Birth</th> -->
                        <th>Action</th>
                        <th>Action</th>
                        <th>Action</th>
                        <th>Action</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php
                $result = $adminObject->showEmployees();
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                        <tr>
                            <td><?php echo $row["id"] ?></td>
                            <td class="w-50">
                                <img src="<?php echo $row["profile_url"] ? "../../Images/" . $row["profile_url"] : "../../Images/defaultImg.webp" ?>" alt="No profile to show" class="w-75 img-thumbnail object-fit-contain border rounded-circle ">
                            </td>
                            <td class='w-50'><?php echo $row["name"] ?></td>
                            <td><?php echo $row["email"] ?></td>
                            <td><?php echo ucfirst($row["gender"]) ?></td>
                            <td><?php echo $row["mobile"] ?></td>
                            <td class='w-25'>
                                <a href="viewEmployeeAllDetails.php?id=<?php echo $row["id"] ?>" class="btn btn-success btn-sm">All Details</a>
                            </td>
                            <!-- <td class='w-25'><?php echo $row["date_of_birth"] ?></td> -->
                            <!-- <td> -->
                            <!-- <form action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="get">
                                    <input type="hidden" name="id" value="<?php echo $row["id"] ?>">
                                    <input type="submit" name='update' class="btn btn-primary btn-sm" value='Update' />
                                </form> -->
                            <!-- <a href="updateEmployee.php?id=<?php echo $row["id"] ?>" class="btn btn-primary btn-sm">Update</a>
                            </td> -->
                            <td>
                                <a href="employeePMSDetails.php?id=<?php echo $row["id"] ?>" class="btn btn-dark  btn-sm">PMS</a>
                            </td>
                            <td>
                                <a href="trackEmployee.php?id=<?php echo $row["id"] ?>" class="btn btn-warning text-muted btn-sm">Track</a>
                            </td>
                            <!-- <td>
                                <a href="workingHourEmployeeDetails.php?id=<?php echo $row["id"] ?>" class="btn btn-success btn-sm">Working</a>
                            </td> -->
                            <?php
                            $userID = $row["id"];
                            $adminObject->isEmployeeProfessionalInfoAdded($userID) ? $editEmployeeInfo = true : $editEmployeeInfo = false;
                            ?>
                            <td class='w-25'>
                                <a href="<?php echo $editEmployeeInfo ? 'updateEmployeeProfessionalInfo.php?id=' . $row["id"] : 'addEmployeeProfessionalInfo.php?id=' . $row["id"]; ?>" class="btn btn-primary btn-sm">Add/Edit Info</a>
                            </td>
                            <td>
                                <!-- <a href="deleteEmployee.php?id=<?php echo $row["id"] ?>" class="btn btn-danger btn-sm">Delete</a> -->
                                <a onclick="return confirm('Are you sure you want to delete this Employee?')" href="?desiredUserId=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>

                        </tr>
                <?php
                    }
                }
                ?>
            </table>
        </div>

    </div>

    <!-- footer here -->
    <?php include('../common/footer.php'); ?>
    <!-- footer ends -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script>
        new DataTable('#EmployeesTable', {
            stateSave: true
        });
    </script>
</body>

</html>