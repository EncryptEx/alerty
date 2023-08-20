<?php
session_start();
if (isset($_SERVER['HTTPS'])) {
    $extraS = "s";
} else {
    $extraS = "";
}
$actualPath = "http" . $extraS . "://" . $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_FILENAME']), "", $_SERVER['PHP_SELF']); // if project is in subfolder, useful when coding in local with an ending /  // if project is in subfolder, useful when coding in local with an ending /
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require realpath('./../private/templates/meta.html'); ?>
    <title>Alerty</title>
    <style>
        code {
            padding: 18px;
            background-color: #000;
            border-radius: 5px;
            margin-top: 5px;
            margin-bottom: 5px;
            display: block;
        }

        .shadow-white{
            box-shadow: 0 0 5rem rgba(255,255,255,.175)!important;
        }
        .highlighted {
            color: wheat;
        }
    </style>

</head>
<?php require realpath(__DIR__ . '/../private/templates/navbar.php'); ?>

<body class="text-bg-dark text-white">

    <!-- HERO -->
    <div class="container col-xxl-8 px-4 py-5">
        <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="col-10 col-sm-8 col-lg-6">
                <!-- <div class=" p-3 mb-5 bg-secondary rounded"> -->
                    <img src="assets/img/alerty-screenshot.png" class="shadow-white d-block mx-lg-auto img-fluid" alt="" width="700" height="500" loading="lazy">
                <!-- </div> -->
            </div>
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold lh-1 mb-3">Fast and reliable reply notifications after tasks</h1>
                <p class="lead">Tired of not knowing if your cron-job did finish or threw any errors? Try Alerty and give it a go, you will be amazed of the rapidity of its set up.</p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <a href="register.php" class="btn btn-warning btn-lg px-4 me-md-2">Get started</a>
                    <a href="login.php" class="btn btn-outline-secondary btn-lg px-4">Login</a>
                </div>
            </div>
        </div>
    </div>

    <hr id="features">

    <!-- FEATURES -->
    <div class="container px-4 py-5">
        <h2 class="pb-2 border-bottom">Features</h2>
        <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
            <div class="col d-flex align-items-start">
                <div class="icon-square text-bg-dark d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
                    <span class="material-symbols-outlined">
                        timer
                    </span>
                </div>
                <div>
                    <h2>Easy to use and set up</h2>
                    <p>Alerty is a minimalistic but easy-to-use service. The set up can be done in less than five minutes with a few clicks.</p>
                </div>
            </div>
            <div class="col d-flex align-items-start">
                <div class="icon-square text-bg-dark d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
                    <span class="material-symbols-outlined">
                        report_off
                    </span>
                </div>
                <div>
                    <h2>Escalable and error-less</h2>
                    <p>The infrastructure and code has been designed in a way to be scalable to multiple alerts. It can also handle a ridicolous amount of requests without throwing an unknown or unexpected error.</p>
                </div>
            </div>
            <div class="col d-flex align-items-start">
                <div class="icon-square text-bg-dark d-inline-flex align-items-center justify-content-center fs-4 flex-shrink-0 me-3">
                    <span class="material-symbols-outlined">
                        fast_forward
                    </span>
                </div>
                <div>
                    <h2>Fast Alerts</h2>
                    <p>Alerts are sent via email (by default). This alerts are triggered just right after the call of its Url trigger. They are sent with encryption and following a strict minimalistic style to avoid disctractions, we just want to alert, not entertain.</p>
                </div>
            </div>
        </div>
    </div>

    <hr id="installation">
    <br>
    <!-- installation -->
    <div class="container px-4 py-5">
        <h2 class="pb-2 border-bottom">Installation</h2>
        <div class="row g-4 py-5 row-cols-12">

            <div>
                <p>The simplest way you can check in with the trigger is to use curl and request the url:</p>
                <code>curl <?php echo $actualPath; ?>trigger.php?t=1v4npa5ps</code>
            </div><br>
            <div>
                <p>You can also pass an optional message which via POST</p>
                <code>curl -X POST -d "m=<span class="highlighted">message goes here</span>" <?php echo $actualPath; ?>trigger.php?t=1v4npa5ps</code>
                <p>or the GET method (preferred to encode the URL):</p>
                <code>curl <?php echo $actualPath; ?>trigger.php?t=1v4npa5ps&m=<span class="highlighted">message%20goes%20here</span></code>
            </div>
            <div>
                <p>This can be set up in any type of task, including a coding file which has an HTTP Libaray to send the request, Alerty will work.
                    It also can be placed at the finish of a cronjob.
                </p>
                <h5><b>Real case example:</b></h5>
                <code>
                    0 10 * * 1 sudo python3 /path/to/worker.py > /var/log/worker.log && curl -X POST <span class="highlighted">-d "s=$?"</span> <?php echo $actualPath; ?>trigger.php?t=1v4npa5ps
                </code>
                <p>This cronjob, apart from updating and upgrading the system every Monday at 10am, it sends an alert by requesting the trigger Url and passes the <b>exit code</b> using the <span class="highlighted">s</span> parameter</p>
                <br>
                <p>Another example but in this case, uploading a log file or artifact, would be as following: </p>
                <code>
                    0 10 * * 1 sudo python3 /path/to/worker.py > /var/log/worker.log && curl -X POST <span class="highlighted">-F file=@/var/log/worker.log </span><?php echo $actualPath; ?>trigger.php?t=1v4npa5ps
                </code>
            </div>
        </div>



        <?php echo file_get_contents(realpath(__DIR__ . '/../private/templates/footer.html')); ?>
    </div><br>

</body>

</html>
