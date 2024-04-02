<?php
session_start();
require_once '../../config/dbConnection.php';
require '../../Classes/Project.php';
$projectObject = new Project();
if ($_SESSION['role'] !== 'emp') {
    header('Location: ../start/login.php');
}

if (isset($_GET['id'])) {
    $desiredUserId = $_GET['id'];
}

$description = "";
$Err = "";

$flag = true;

if (isset($_POST['submit'])) {
    if (empty($_POST['projectId'])) {
        $Err = "Oops! No project found.";
        $flag = false;
    } else {
        $projectId_Array = $_POST['projectId'];
        $description_Array = $_POST['description'];
        $desiredUserId = $_POST['desiredUserId'];
        foreach ($description_Array as $value) {
            if (!isset($value) || $value == '') {
                $flag = false;
                $Err = "* An empty description was found *";
                break;
            }
        }
    }

    if ($flag) {
        $index = 0;
        foreach ($projectId_Array as $pId) {
            $description = $description_Array[$index++];
            $result = $projectObject->addProjectDailyTask($desiredUserId, $pId, $description);
            if ($result) {
                // echo "<br>New record inserted successfully<br>";
                $_SESSION['AddDailyTaskStatus'] = 'success';
                header("Location: pms.php");
            } else echo "<br>Error occured while inserting into table";
        }
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Daily Task</title>
    <?php include('../common/favicon.php'); ?>
    <link rel="stylesheet" href="../../Styles/add-ProjectHours.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
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
                <a class="nav-link" href="pms.php">Back</a>
            </li>
        </ul>
    </nav>
    <!-- nav ends -->
    <h2 class="text-center mt-5">Add <span class='gradient-custom-2'>Daily</span> <span class='gradient-custom-2'>Task</span></h2>
    <div class="container mt-3">
        <div class="col-md-7">
            <div class='text-danger text-center fw-bold h4'><?php echo $Err ?></div>
            <form action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="post">
                <div class="form-group fieldGroup d-flex justify-content-between align-items-center gap-5">
                    <div class="w-75">
                        <div class="mb-3">
                            <label class="col-form-label">Assigned Projects <span class='text-danger'>*</label>
                            <!-- selectpicker -->
                            <select class="form-control" name="projectId[]" id='projectId' data-live-search="true">
                                <?php
                                $result = $projectObject->showProjectsByUserId($desiredUserId);
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $projectID = $row['id'];
                                        $projectTITLE = $row['title'];

                                ?> <option value="<?php echo $projectID ?>"><?php echo $projectTITLE ?></option>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <option value="" disabled selected>No Projects found!</option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description <span class='text-danger'>*</label>
                            <textarea class="form-control" name="description[]" placeholder="Fixed ***** bug..." rows="3"></textarea>
                        </div>
                    </div>
                    <span>
                        <a href="javascript:void(0);" class="btn btn-success addMore w-100"><i class="custicon plus"></i> Add More</a>
                    </span>
                </div>

                <input type="hidden" class="form-control" name="desiredUserId" value="<?php echo $desiredUserId; ?>">
                <div class="buttons">
                    <input type="submit" name="submit" class="btn btn-dark btn-lg" value="Save">
                    <input type="reset" name="reset" class="btn btn-dark btn-lg">
                </div>

            </form>

            <!-- Replica of input field group HTML -->
            <div class="form-group fieldGroupCopy d-none">
                <div class="w-75">
                    <div class="mb-3">
                        <label class="col-form-label">Assigned Projects <span class='text-danger'>*</label>
                        <!-- selectpicker -->
                        <select class="form-control" name="projectId[]" id='projectId' data-live-search="true">
                            <?php
                            $result = $projectObject->showProjectsByUserId($desiredUserId);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $projectID = $row['id'];
                                    $projectTITLE = $row['title'];

                            ?> <option value="<?php echo $projectID ?>"><?php echo $projectTITLE ?></option>
                                <?php
                                }
                            } else {
                                ?>
                                <option value="" disabled selected>No Projects found!</option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description <span class='text-danger'>*</label>
                        <textarea class="form-control" name="description[]" placeholder="Fixed ***** bug..." rows="3"></textarea>
                    </div>
                </div>
                <span>
                    <a href="javascript:void(0)" class="btn btn-danger remove  w-100"><i class="custicon cross"></i> Remove</a>
                </span>
            </div>
        </div>
    </div>

    <!-- footer here -->
    <?php include('../common/footer.php'); ?>
    <!-- footer ends -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Maximum number of groups can be added
            var maxGroup = 10;

            // Add more group of input fields
            $(".addMore").click(function() {
                if ($('body').find('.fieldGroup').length < maxGroup) {
                    var fieldHTML = '<div class="form-group fieldGroup d-flex justify-content-between align-items-center gap-5">' + $(".fieldGroupCopy").html() + '</div>';
                    $('body').find('.fieldGroup:last').after(fieldHTML);
                } else {
                    alert('Maximum ' + maxGroup + ' groups are allowed.');
                }
            });

            // Remove fields group
            $("body").on("click", ".remove", function() {
                $(this).parents(".fieldGroup").remove();
            });
        });
    </script>
</body>

</html>