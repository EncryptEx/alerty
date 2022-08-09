<?php
session_start();
require './../private/utils.php';

use \Utils\Utilities;

$utils = new Utilities;
$utils->redirectIfNotLogged();

$errorMsg = "";
if (isset($_GET['e'])) {
    if ($_GET['e'] == 1) {
        $errorMsg = "Please complete the form. There's some data missing.";
    }
    if ($_GET['e'] == 2) {
        $errorMsg = "Something went wrong while creating a new trigger.";
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <title>Alerty | Create Trigger</title>
</head>
<?php require realpath(__DIR__ . '/../private/templates/navbar.php'); ?>

<body class="text-bg-dark text-white">
    <div class="container col-xxl-8 px-4 py-5">
        <div class="col-12 ">
            <div class="alert alert-warning d-flex align-items-center alert-dismissible fade show" role="alert" style="display: <?php if ($errorMsg != "") {
                                                                                                                                    echo "block";
                                                                                                                                } else {
                                                                                                                                    echo "none";
                                                                                                                                } ?> !important">
                <span class="material-symbols-outlined align-middle" style="margin-right:10px">
                    warning
                </span>
                <span class="align-middle">
                    <?php echo $errorMsg; ?>
                </span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="window.location.href = window.location.href.split('?')[0];"></button>
            </div>
            <h3>Create a new trigger</h3>
            <hr>
            <form action="saveTrigger.php" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Backups generation" required>
                </div>
                <div class="mb-3">
                    <label for="actionType" class="form-label">Action Type</label>
                    <select name="type" class="form-select" id="actionType" required>
                        <?php
                        $actionType = $utils->getActionTypes();
                        if ($actionType['success']) :
                            foreach ($actionType['actionTypes'] as $action) {
                                echo "<option value='" . $action['id'] . "'>" . $action['name'] . "</option>";
                            }
                        else : ?>

                            <option disabled>No types created yet, please contact an admin or create a new one at the DB</option>
                        <?php endif; ?>
                    </select>
                </div>
                <button class="btn btn-light" type="submit">Create</button>
            </form>
        </div>

    </div>

</body>

</html>