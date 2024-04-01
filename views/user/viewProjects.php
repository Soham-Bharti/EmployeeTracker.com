<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/Project.php';
$projectObject = new Project();

// print_r($_SESSION);
if ($_SESSION['role'] !== 'emp') {
    header('Location: ../start/login.php');
}

if (isset($_SESSION["id"])) $desiredUserId = $_SESSION["id"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Projects</title>
    <?php include('../common/favicon.php'); ?>
    <link rel="stylesheet" href="../../Styles/view-Projects.css">
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
                <a class="nav-link" href="pms.php">Back</a>
            </li>
        </ul>
    </nav>
    <!-- nav ends -->
    <div class="container mt-5 px-5">
        <h2 class="text-center mt-5">Showing your <span class='gradient-custom-2'>assigned Projects'</span> details</h2>
        <div class="mt-3">
            <table id="projectsTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Project Id</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Assigned on</th>
                    </tr>
                </thead>
                <?php
                $result = $projectObject->showProjectsByUserId($desiredUserId);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $dateTime = $row["assigned_on"];
                        $time = strtotime($dateTime);
                ?>
                        <tr>
                            <td><?php echo $row["id"] ?></td>
                            <td><?php echo $row["title"] ?></td>
                            <td><?php echo $row["description"] == '' ? 'N/A' : $row["description"] ?></td>
                            <td><?php echo date('d M Y', $time); ?></td>
                        </tr>
                    <?php
                    }
                } else { ?><span class='fw-bold text-center d-block h3 text-danger'><?php echo "No Projects are assigned yet!"; ?></span><?php
                                                                                                                                        } ?>
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
        // new DataTable('#projectsTable');
        // Disable ordering on the first column AND set the default ordering for the table (the default would still be to order on column index 0 otherwise):
        // setting assigned on column in desc order by default
        new DataTable('#projectsTable', {
            columnDefs: [{
                orderable: false,
                targets: 0
            }],
            order: [
                [3, 'desc']
            ]
        });
    </script>
</body>

</html>