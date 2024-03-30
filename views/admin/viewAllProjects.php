<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/Project.php';
$projectObject = new Project();

// print_r($_SESSION);
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../start/login.php');
}

if (isset($_GET['delete_id'])) {
    $projectId = $_GET['delete_id'];
    $result = $projectObject->delete($projectId);
    if ($result) {
        $_SESSION['ProjectDeleteStatus'] = 'success';
    } else {
        $_SESSION['ProjectDeleteStatus'] = 'failure';
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
    <title>All Projects</title>
    <?php include('../common/favicon.php');?>
    <link rel="stylesheet" href="../../Styles/view-Projects.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">

</head>

<body class='d-flex flex-column min-vh-100'>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <a href="../start/home.php" class="svg text-decoration-none text-success d-flex align-items-center">
                <img src="../../Images/mainIcon.gif" alt='svg here'>
                <span class='fw-bold text-success'>EmployeeTracker.com</span>
            </a>

            <ul class="navbar-nav mb-2 me-auto mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="adminDashboard.php">Back</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="addProject.php">Add Project</a>
                </li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
        </div>
    </nav>
    <!-- nav ends -->
    <div class="container mt-5 px-5">
        <h2 class="text-center mt-5">Showing <span class='gradient-custom-2'>Projects'</span> details</h2>
        <div class="mt-3">
            <!-- toast after successful project updation -->
            <?php if (isset($_SESSION['updateProjectDetailsStatus']) && $_SESSION['updateProjectDetailsStatus'] == 'success') { ?>
                <div class="toast show m-auto hide">
                    <div class="toast-header bg-success text-white ">
                        <strong class="me-auto">Project details updated successfully!</strong>
                        <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            <?php }
            $_SESSION['updateProjectDetailsStatus'] = '' ?>
            <!-- toast after successful added -->
            <?php if (isset($_SESSION['ProjectDeleteStatus']) && $_SESSION['ProjectDeleteStatus'] == 'success') { ?>
                <div class="toast show m-auto hide">
                    <div class="toast-header bg-danger text-white ">
                        <strong class="me-auto">Project removed successfully!</strong>
                        <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            <?php }
            $_SESSION['ProjectDeleteStatus'] = '' ?>
            <!-- toast after successful Project Member addition -->
            <?php if (isset($_SESSION['addProjectMemberStatus']) && $_SESSION['addProjectMemberStatus'] == 'success') { ?>
                <div class="toast show m-auto hide">
                    <div class="toast-header bg-success text-white ">
                        <strong class="me-auto">Member added successfully!</strong>
                        <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            <?php }
            $_SESSION['addProjectMemberStatus'] = '' ?>
            <!-- toast after unsuccessful Project Member addition -->
            <?php if (isset($_SESSION['addProjectMemberStatus']) && $_SESSION['addProjectMemberStatus'] == 'failure') { ?>
                <div class="toast show m-auto hide">
                    <div class="toast-header bg-danger text-white ">
                        <strong class="me-auto">Oops somthing went wrong!</strong>
                        <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            <?php }
            $_SESSION['addProjectMemberStatus'] = '' ?>
            <!-- toast after successful Project addition -->
            <?php if (isset($_SESSION['AddProjectStatus']) && $_SESSION['AddProjectStatus'] == 'success') { ?>
                <div class="toast show m-auto hide">
                    <div class="toast-header bg-success text-white ">
                        <strong class="me-auto">Project added successfully!</strong>
                        <button type="button" class="btn-close btn btn-light" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            <?php }
            $_SESSION['AddProjectStatus'] = '' ?>
            <table id="projectsTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Started on - at</th>
                        <th>Action</th>
                        <th>Action</th>
                        <th>Action</th>
                        <th>Action</th>

                    </tr>
                </thead>
                <?php
                $result = $projectObject->showAllProjects();
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $dateTime = $row["created_at"];
                        $time = strtotime($dateTime);
                ?>
                        <tr>
                            <td><?php echo $row["id"] ?></td>
                            <td class='w-25 fw-bold'><?php echo $row["title"] ?></td>
                            <td class='w-25'><?php echo $row["description"] == '' ? 'N/A' : ( strlen($row["description"]) > 100 ? substr($row["description"], 0, 100)."..." : $row["description"]) ?></td>
                            <td class='w-25'><?php echo date('d M Y - H:ia', $time); ?></td>
                            <td>
                                <a href="updateProjectDetails.php?id=<?php echo $row["id"] ?>" class="btn btn-info text-white btn-sm">Update</a>
                            </td>
                            <td>
                                <a href="viewProjectMembers.php?id=<?php echo $row["id"] ?>" class="btn btn-success text-white btn-sm">Show Members</a>
                            </td>
                            <td>
                                <a href="addProjectMember.php?id=<?php echo $row["id"] ?>" class="btn btn-warning text-muted btn-sm">Add Members</a>
                            </td>
                            <td>
                                <!-- <a href="deleteProject.php?id=<?php echo $row["id"] ?>" class="btn btn-danger btn-sm">Remove</a> -->
                         
                                <a onclick="return confirm('Are you sure you want to delete this project?')" href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Remove</a>
                    
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
        new DataTable('#projectsTable', {
            stateSave: true
        });
    </script>
</body>

</html>