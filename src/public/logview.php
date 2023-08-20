<?php
session_start();
require './../private/utils.php';

use Utils\Utilities;

$utils = new Utilities();
$utils->redirectIfNotLogged();

if (!isset($_GET['t'])) {
    header('location:dashboard.php?e=1');
    die();
}

$trigger = $utils->getTriggerData($_SESSION['userid'], $_GET['t']);
if (!$trigger['success']) {
    //something failed while fethcing that triggerid,
    // simply doesn't exist, or is not owned by that user
    header('location:dashboard.php?e=1');
    die();
}




if (!$utils->isLogFromtrigger($trigger['id'], $_GET['f'])) {
    header('location:view.php?t=' . $trigger['stringUrl'] . '&e=2');
    die();
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <?php require realpath('./../private/templates/meta.html'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
    <style>
        .logViewer {
            border: 1px solid #525558;
            padding: 10px;
            border-radius: 3px;
        }
    </style>
    <title>Alerty | Trigger View</title>
</head>
<?php require realpath(__DIR__ . '/../private/templates/navbar.php'); ?>

<body class="text-bg-dark text-white">

    <div class="container col-xxl-8 px-4 py-5">

        <div class="col-12 d-block">
            <h3 id="staticTitle">Reading <?php echo htmlentities($trigger['name']); ?>'s log</h3>
            <p>Recieved log at: <?php echo htmlentities(date("h:i:s d/m/y", $_GET['d'])) ?></p>
        </div>

        <div class="col-12 d-block">
            <pre class="logViewer"><?php echo htmlentities($utils->readLogFile($_GET['f'])); ?></pre>
        </div>

</body>

</html>