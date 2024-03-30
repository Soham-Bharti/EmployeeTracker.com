<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/Admin.php';
$adminObject = new Admin();

// print_r($_SESSION);
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../start/login.php');
}

if (isset($_GET['id'])) $desiredUserId = $_GET['id'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMS | Admin<?php echo $_SESSION['userName']; ?> </title>
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
                    <a class="nav-link" href="viewAllEmployees.php">Back</a>
                </li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>

        </div>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-3">Showing <span class='gradient-custom-2'>PMS</span> dashboard</h2>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <div class='fs-4'>
                <?php
                $result1 = $adminObject -> showEmployeeAllDetails($desiredUserId);
                if (mysqli_num_rows($result1) == 1) {
                    $row = mysqli_fetch_assoc($result1);
                    $user = $row['name'];
                    $userId = $row['id'];
                } else echo "User not found!";
                echo "Emp Id: <b>" . $userId . "</b> | " . ucwords($user);
                ?>
            </div>
        </div>

        <h2 class="text-center mt-5">Showing <span class='gradient-custom-1'>LAST 10</span> activity</h2>
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
                $result = $adminObject -> showEmployeePMSdetails($desiredUserId);
                $seialNumber = 1;
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
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