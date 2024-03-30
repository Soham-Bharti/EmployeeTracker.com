<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/Project.php';
$projectObject = new Project();

// print_r($_SESSION);
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../start/login.php');
}

$description = $title = "";
$titleErr = $descErr = "";

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
$flag = true;
if (isset($_POST['submit'])) {
    $title = test_input($_POST['title']);
    $description = test_input($_POST['description']);

    if (empty($title)) {
        $titleErr = 'Required';
        $flag = false;
    } else {
        // if (!preg_match("/^[a-zA-Z0-9-' ]*$/", $title)) {
        //     $titleErr = "Only letters, white space and numbers allowed";
        //     $flag = false;
        // }
    }

    if (empty($description)) {
    } else {
        // if (!preg_match("/^[a-zA-Z0-9-' ]*$/", $description)) {
        //     $descErr = "* Only letters, white space and numbers allowed";
        //     $flag = false;
        // }
    }

    if ($flag) {
        $result = $projectObject->add($title, $description);
        if ($result) {
            // echo "<br>New record inserted successfully<br>";
            $_SESSION['AddProjectStatus'] = 'success';
            header("Location: viewAllProjects.php");
        } else echo "<br>Error occured while inserting into table : " . mysqli_error($conn);
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Project Registration</title>
    <?php include('../common/favicon.php');?>
    <link rel="stylesheet" href="../../Styles/add-Project.css">
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
                    <a class="nav-link" href="viewAllProjects.php">Back</a>
                </li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>

        </div>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-2">New <span class='gradient-custom-2'>Project</span> Registration</h2>
    <div class="container mt-3">
        <div class="col-md-7">
            <form action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="post">
                <div class="mb-3">
                    <label class="col-form-label">Title <span>* <?php echo $titleErr ?></span></label>
                    <div class="mb-3">
                        <input type="text" name="title" class="form-control" placeholder="FF Bargain SST">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description <span><?php echo $descErr ?></span></label>
                    <textarea class="form-control" name="description" placeholder="All about FFB..." rows="7"></textarea>
                </div>

                <div class="buttons">
                    <input type="submit" name="submit" class="btn btn-dark btn-lg" value="Add Project">
                    <input type="reset" name="reset" class="btn btn-dark btn-lg">
                </div>

            </form>
        </div>
    </div>

    <!-- footer here -->
    <?php include('../common/footer.php'); ?>
    <!-- footer ends -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>