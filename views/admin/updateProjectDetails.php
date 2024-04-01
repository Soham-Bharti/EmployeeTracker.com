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
$titleErr = $descErr = $title = $description = '';

if (isset($desiredProjectId)) {
    $result = $projectObject->showProjectDetails($desiredProjectId);
    if (mysqli_num_rows($result) == 1) {
        while ($row = mysqli_fetch_assoc($result)) {
            $title = $row['title'];
            $description = $row['description'];
            $dateTime = $row["created_at"];
            $time = strtotime($dateTime);
        }
    }
}


function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$flag = true;
if (isset($_POST['update'])) {
    // print_r($_POST);
    $title = test_input($_POST['title']);
    $description = test_input($_POST['description']);
    $desiredProjectId = $_POST['desiredProjectId'];

    if (empty($title)) {
        $titleErr = "Required";
        $flag = false;
    }

    if (empty($description)) {
        $descErr = "Required";
        $flag = false;
    }

    if ($flag) {
        $result = $projectObject->updateProjectDetails($desiredProjectId, $title, $description);
        if ($result) {
            // echo "<br>Record updated successfully<br>";
            $_SESSION['updateProjectDetailsStatus'] = 'success';
            header("Location: viewAllProjects.php");
            // echo "Update success";
        } else {
            echo "<br>Error occurred while updated into table : " . mysqli_error($conn); // Print any errors returned by MySQL
            // $_SESSION['updateEmployeeInfoStatus'] = 'fail';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Project</title>
    <?php include('../common/favicon.php'); ?>
    <link rel="stylesheet" href="../../Styles/update-ProjectDetails.css">
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
    <h2 class="text-center mt-2"><span class='gradient-custom-1'>Update</span> Projects' Details</h2>
    <div class="container mt-3">
        <div class="col-md-7">
            <div class="my-3 d-flex align-items-center justify-content-around gap-4">
                <div class="d-inline-block profile-img w-25">
                    <img src="<?php echo "../../Images/project.gif" ?>" alt="No project icon to show" class="img-thumbnail object-fit-contain mb-2">
                </div>
                <div class='text-center'>
                    <p>Project ID: <span class="fw-bold text-secondary"><?php echo $desiredProjectId ?></span></p>
                    <p>Created On: <span class="fw-bold text-secondary"><?php echo date('l, d M Y - H:ia', $time); ?></span></p>
                </div>
            </div>
            <hr>
            <form action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="post">
                <div class="mb-3">
                    <label class="col-form-label">Title <span>* <?php echo $titleErr ?></span></label>
                    <div class="mb-3">
                        <input type="text" name="title" class="form-control" placeholder="FF Bargain SST" value="<?php echo $title ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description <span><?php echo $descErr ?></span></label>
                    <textarea class="form-control" name="description" placeholder="All about the PROJECT..." rows="7"><?php echo $description ?></textarea>
                </div>

                <input type="hidden" class="form-control" name="desiredProjectId" value="<?php echo $desiredProjectId ?>">

                <div class="buttons">
                    <input type="submit" name="update" class="btn btn-dark btn-lg" value="Save">
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