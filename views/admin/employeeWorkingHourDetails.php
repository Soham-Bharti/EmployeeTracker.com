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
    <title>Emplpoyee Working | Admin</title>
    <?php include('../common/favicon.php'); ?>
    <link rel="stylesheet" href="../../Styles/track-Employee.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class='d-flex flex-column min-vh-100'>
    <nav class="navbar navbar-expand-lg bg-body-tertiary d-flex align-items-center justify-content-between px-5">
        <a href="../start/home.php" class="svg text-decoration-none text-success d-flex align-items-center">
            <img src="../../Images/mainIcon.gif" alt='svg here'>
            <span class='fw-bold text-success'>EmployeeTracker.com</span>
        </a>
        <ul class="navbar-nav mb-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="trackEmployee.php?id=<?php echo $desiredUserId ?>">Back</a>
            </li>
        </ul>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-3">Employee <span class='gradient-custom-2'>Working Hours'</span> dashboard</h2>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <div class='fs-4'>
                <?php
                $result = $adminObject->showEmployeeAllDetails($desiredUserId);
                if (mysqli_num_rows($result) === 1) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $userName = $row['name'];
                    }
                }
                echo "Emp Id: <b>" . $desiredUserId . "</b> | " . $userName;
                ?>
            </div>
            <div>
                <?php
                $showStatus = '';
                $result = $adminObject->showEmployeeTrackDetailsWithGroupByDate($desiredUserId);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $checkOut = $row["check_out_time"];
                        is_null($checkOut) ? $showStatus = 'check-in' : $showStatus = 'check-out';
                        break; // as we only need top 1
                    }
                }
                ?>
                <?php
                if ($showStatus === 'check-in') {
                ?>
                    <h4 class="text-center mt-3">Emp is currently <span class='text-success fw-bold'><?php echo $showStatus ?></span></h4>
                <?php } else if ($showStatus === 'check-out') {
                ?>
                    <h4 class="text-center mt-3">Emp is currently <span class='text-danger fw-bold'><?php echo $showStatus ?></span></h4>
                <?php } else { ?>
                    <h4 class="text-center mt-3">Emp will do <span class='text-warning fw-bold'>FIRST</span> check-in!</h4>
                <?php } ?>
            </div>
        </div>

        <h2 class="text-center mt-5">Showing <span class='gradient-custom-3'>LAST 10</span> tracks</h2>
        <div class="mt-3">
            <table>
                <tr class="text-dark">
                    <th>S.Number</th>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Working Hours</th>
                </tr>
                <?php
                $result = $adminObject->showEmployeeTrackDetailsWithGroupByDate($desiredUserId);
                $seialNumber = 1;
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($seialNumber == 11) break; // we have to show only last 10 records
                        $dateTime = $row["date"];
                        $time = strtotime($dateTime);
                        $total_seconds = $row['total_seconds'];
                ?>
                        <?php
                        // seconds to hours, minutes and seconds
                        $hours = floor($total_seconds / 3600);
                        $minutes = floor(($total_seconds % 3600) / 60);
                        $seconds = $total_seconds % 60;
                        $formatted_time = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                        ?>
                        <tr>
                            <td><?php echo $seialNumber++ ?></td>
                            <td><?php
                                echo date('d M Y', $time);
                                ?></td>
                            <td><?php
                                echo date('l', $time);
                                ?>
                            </td>
                            <td class='fw-bold <?php echo $formatted_time > '08:45:00' ? 'text-success' : 'text-danger' ?>'>
                                <?php
                                echo "$formatted_time";
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