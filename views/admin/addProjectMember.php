<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/Project.php';
$projectObject = new Project();

// print_r($_SESSION);
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../start/login.php');
}

$Err = $selectMemberErr = "";
$desiredProjectId = "";

if (isset($_GET['id'])) $desiredProjectId = $_GET['id'];
// echo $desiredUserId;

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$flag = true;
if (isset($_POST['add'])) {
    // print_r($_POST);
    $desiredProjectId = test_input($_POST['projectId']);

    if (!isset($_POST['memberId'])) {
        $selectMemberErr = 'Required';
        $flag = false;
    } else {
        $memberIdArr = $_POST['memberId'];
    }

    if ($flag) {
        // sending data to data base
        foreach ($memberIdArr as $value) {
            $result =  $projectObject->addProjectMembers($desiredProjectId, $value);
            if ($result) {
                // echo "<br>Record inserted successfully<br>";
                // if everthing if well then redirecting the admin
            } else {
                $flag = false;
                echo "<br>Error occurred while inserting into table"; // Print any errors returned by MySQL
            }
        }
        if ($flag) {
            $_SESSION['addProjectMemberStatus'] = 'success';
            header("Location: viewAllProjects.php");
        } else {
            // echo "There was an error while insertion!";
            $_SESSION['addProjectMemberStatus'] = 'failure';
            header("Location: viewAllProjects.php");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Memeber/s</title>
    <?php include('../common/favicon.php');?>
    <link rel="stylesheet" href="../../Styles/add-ProjctMember.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
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
    <?php
    $result = $projectObject -> showProjectDetails($desiredProjectId);
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
    <h2 class="text-center mt-2"><span class='gradient-custom-2'>Add</span> Member/s in <span class='gradient-custom-1'><?php echo $title ?></span> Project</h2>
    <div class="container mt-3">
        <div class="col-md-7">
            <div class="my-3 d-flex align-items-center justify-content-around gap-4">
                <div class='text-center'>
                    <p>Project ID: <span class="fw-bold text-secondary"><?php echo $desiredProjectId ?></span></p>
                    <p>Description: <span class="fw-bold text-secondary"><?php echo $description ?></span></p>
                    <p><span><?php echo $Err ?></p>
                </div>
            </div>
            <hr>
            <form action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="post">
                <div class="mb-3">
                    <label class="col-form-label" for='memberId'>Select Member/s <span class="text-danger">* <?php echo $selectMemberErr ?></span></label>
                    <select class="form-control selectpicker" name="memberId[]" id="memberId" multiple data-live-search="true" placeholder='choose'>
                        <?php
                       $result = $projectObject -> showUnAddedProjectMembers($desiredProjectId);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                            <?php
                            }
                        } else {
                            ?>
                            <option value="" disabled selected>No users found!</option>
                        <?php
                        };
                        ?>
                    </select>
                </div>
                <input type="hidden" name="projectId" value="<?php echo $desiredProjectId ?>">
                <div class="buttons">
                    <input type="submit" name="add" class="btn btn-dark btn-lg" value="Add">
                </div>


            </form>
        </div>
    </div>

    <!-- footer here -->
    <?php include('../common/footer.php'); ?>
    <!-- footer ends -->

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
</body>

</html>