<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/Project.php';
$projectObject = new Project();

// print_r($_SESSION);
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../start/login.php');
}

if (isset($_GET['id'])) $desiredProjectId = $_GET['id'];
// echo $desiredUserId;
if (isset($_GET['desiredUserDeletionId']) && isset($_GET['desiredProjectId'])) {
    $desiredUserDeletionId = $_GET['desiredUserDeletionId'];
    $desiredProjectId = $_GET['desiredProjectId'];
    $result = $projectObject->deleteMember($desiredProjectId, $desiredUserDeletionId);
    if ($result) {
        // $_SESSION['DeleteStatus'] = 'success';
    }
    header('Location: viewAllProjects.php');
}
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Memeber/s in the Project</title>
    <?php include('../common/favicon.php'); ?>
    <link rel="stylesheet" href="../../Styles/view-ProjectMembers.css">
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
                <a class="nav-link" href="viewAllProjects.php">Back</a>
            </li>
        </ul>
    </nav>
    <!-- nav ends -->

    <?php
    $result = $projectObject->showProjectDetails($desiredProjectId);
    if (mysqli_num_rows($result) == 1) {
        while ($row = mysqli_fetch_assoc($result)) {
            $title = $row['title'];
            $description = $row['description'];
            if ($description == '') {
                $description = 'N/A';
            }
        }
    }
    ?>

    <h2 class="text-center mt-5"><span class='gradient-custom-2'>All</span> Member/s in <span class="gradient-custom-1"><?php echo $title ?></span> Project</h2>
    <div class="container mt-5 px-5 w-100">
        <table>
            <thead>
                <tr>
                    <th>User Id</th>
                    <th>Name</th>
                    <th>Added on</th>
                    <th>Action</th>
                </tr>
            </thead>
            <?php
            $result = $projectObject->showProjectMembers($desiredProjectId);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $dateTime = $row["addedOn"];
                    $time = strtotime($dateTime);
            ?>
                    <tr>
                        <td><?php echo $row["id"] ?></td>
                        <td><?php echo $row["name"] ?></td>
                        <td><?php echo date('d M Y', $time); ?></td>
                        <td> <a onclick="return confirm('Are you sure you want to delete this Employee?')" href="?desiredUserDeletionId=<?php echo $row['id']; ?>&desiredProjectId=<?php echo $desiredProjectId; ?>" class="btn btn-danger btn-sm">Delete</a></td>
                    </tr>
                <?php
                }
            } else { ?><span class='fw-bold text-center d-block h3 text-danger'><?php echo "No members are assigned yet!"; ?></span><?php
                                                                                                                                } ?>
        </table>

    </div>

    <!-- footer here -->
    <?php include('../common/footer.php'); ?>
    <!-- footer ends -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.2/js/dataTables.js"></script>
    <script>
        new DataTable('#allMembersInProjectTable');
    </script>
</body>

</html>