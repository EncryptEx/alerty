<?php
session_start();
require './../private/utils.php';

use \Utils\Utilities;

$utils = new Utilities;
$utils->redirectIfNotLogged();

$errorMsg = "";
$successMsg = "";
if (isset($_GET['e'])) {
    if ($_GET['e'] == 1) {
        $errorMsg = "Incorrect trigger ID to inspect";
    }
    if ($_GET['e'] == 2) {
        $errorMsg = "An error ocurred while deleting the trigger/trigger's log";
    }
}
if (isset($_GET['s'])) {
    if ($_GET['s'] == 1) {
        $successMsg = "Trigger created successfully";
    }
    if ($_GET['s'] == 2) {
        $successMsg = "Trigger and its historic have been deleted successfully";
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <?php require realpath('./../private/templates/meta.html'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <title>Alerty | Dashboard</title>
    <style>
        .bg-gray {
            background-color: rgba(var(--bs-secondary-rgb), 0.2);
        }

        .trigger {
            padding: 10px;
        }

        /* fab icon */
        .fab-container {
            position: fixed;
            bottom: 39px;
            right: 39px;
            cursor: pointer;
        }


        .button {
            border-radius: 100%;
            width: 60px;
            height: 60px;
            background: var(--bs-warning);
        }

        .iconbutton span {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: var(--bs-black);
        }
    </style>
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
            <div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert" style="display: <?php if ($successMsg != "") {
                                                                                                                                    echo "block";
                                                                                                                                } else {
                                                                                                                                    echo "none";
                                                                                                                                } ?> !important">
                <span class="material-symbols-outlined align-middle" style="margin-right:10px">
                    check_circle
                </span>
                <span class="align-middle">
                    <?php echo $successMsg; ?>
                </span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="window.location.href = window.location.href.split('?')[0];"></button>
            </div>
            <h3>Dashboard</h3>
            <h6 class="text-muted">Select a trigger to inspect</h6>
            <hr>
            <div class="row">
                <?php
                $triggers = $utils->getAllTriggersFromUser($_SESSION['userid']);
                if ($triggers['success']) :
                    foreach ($triggers['allTriggers'] as $trigger) : ?>

                        <a class="text-decoration-none text-white d-block" href="./view.php?t=<?php echo htmlentities($trigger['stringUrl']) ?>">
                            <div class="pd-5 text-white bg-gray col-12 mb-3">
                                <div class="trigger align-middle">
                                    <h5 class="d-inline"><?php echo htmlentities($trigger['name']); ?></h5>
                                    <h6 class="d-inline text-muted text-break">#<?php echo htmlentities($trigger['stringUrl']); ?></h6>



                                </div>
                            </div>
                        </a>

                    <?php endforeach;
                else : // set that there are no triggers created 
                    ?>
                    <p class="text-white">No triggers created yet, let's create one <a href="createTrigger.php">here</a></p>
                <?php endif; ?>
            </div>
        </div>

    </div>
    <a class="fab-container d-block text-decoration-none" onclick="animateBtn();" href="#">
        <div class="button iconbutton">
            <span class="material-symbols-outlined">
                add
            </span>
        </div>
    </a>

    <script defer>
        // add button animation and redirect function
        const element = document.querySelector('.iconbutton');
        function animateBtn() {
            element.classList.add('animate__animated', 'animate__heartBeat');
            setTimeout(function (){
                window.location.href = "./createTrigger.php";
            }, 200);
        }
        element.addEventListener('animationend', () => {
            element.classList.remove('animate__animated', 'animate__heartBeat');
        });
    </script>
</body>

</html>