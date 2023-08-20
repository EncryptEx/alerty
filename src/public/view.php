<?php
session_start();
require './../private/utils.php';

use \Utils\Utilities;

$utils = new Utilities;
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


// error and success alerts 
$errorMsg = "";
$successMsg = "";
if (isset($_GET['e'])) {
    if ($_GET['e'] == 1) {
        $errorMsg = "Missing data, please fill the inputs.";
    }
    if ($_GET['e'] == 2) {
        $errorMsg = "The requested log file is not attached to this trigger.";
    }
}
if (isset($_GET['s'])) {
    if ($_GET['s'] == 1) {
        $successMsg = "Trigger edited successfully";
    }
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

    <title>Alerty | Trigger View</title>
</head>
<?php require realpath(__DIR__ . '/../private/templates/navbar.php'); ?>

<body class="text-bg-dark text-white">

    <div class="container col-xxl-8 px-4 py-5">
        <div class="col-12">
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
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="window.location.href = window.location.href.split('&')[0];"></button>
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
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="window.location.href = window.location.href.split('&')[0];"></button>
            </div>
            <div class="col-12 d-block">
                <div class="float-start">
                    <h3 id="staticTitle"><?php echo htmlentities($trigger['name']); ?></h3>
                    <form action="updateTrigger.php" method="post" id="editForm">
                        <input type="text" name="name" class="form-control-lg d-none" id="triggerTitle" value="<?php echo htmlentities($trigger['name']); ?>">
                        <input type="hidden" name="id" value="<?php echo htmlentities($trigger['id']); ?>">
                        <input type="hidden" name="stringUrl" value="<?php echo htmlentities($trigger['stringUrl']); ?>">
                    </form>
                </div>
                <div class=" float-end">
                    <!-- edit and submit edit btn-->
                    <a href="#" class="btn btn-dark text-white align-middle" style="padding-bottom:10px" onclick="edit();" id="editBtn">
                        <span class="material-symbols-outlined align-middle">
                            edit
                        </span>
                    </a>
                    <!-- cancel edit -->
                    <a href="#" class="btn btn-dark text-white align-middle d-none" style="padding-bottom:10px" onclick="cancelEdit();" id="editBtncancel">
                        <span class="material-symbols-outlined align-middle">
                            cancel
                        </span>
                    </a>


                    <!-- remove button -->
                    <button type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" class="btn btn-dark text-white align-middle" style="padding-bottom:10px" id="deleteButton">
                        <span class="material-symbols-outlined align-middle">
                            delete
                        </span>
                    </button>

                    <!-- remove modal asking for confirmation -->

                    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content bg-dark">
                                <form action="deleteTrigger.php" method="post">
                                    <input type="hidden" name="id" value="<?php echo htmlentities($trigger['id']); ?>">
                                    <input type="hidden" name="stringUrl" value="<?php echo htmlentities($trigger['stringUrl']); ?>">
                                    <div class="modal-header bg-dark">
                                        <h5 class="modal-title bg-dark">Delete trigger</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body bg-dark">
                                        <p>Are you sure you want to delete <b>permanently</b> the trigger and its with the name of <code><?php echo htmlentities($trigger['name']); ?></code>?</p>
                                        <span class="text-muted">Its historical data will also be removed.</span>
                                    </div>
                                    <div class="modal-footer bg-dark">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" href="delete">Close</button>
                                        <button type="submot" class="btn btn-danger">Delete</button>
                                    </div>
                                </form </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <hr>
                <!-- Last triggered section -->
                <p class="white-text">⌛ Last triggered:
                    <?php
                    $timestamp = $utils->getLastTrigger($trigger['id']);
                    if ($timestamp == 0) :
                        echo 'never';
                    else :
                        echo $utils->time_since(time() - $timestamp) . " ago";
                    ?>
                        <span class="text-muted d-block d-sm-inline">(<?php echo date("d M Y H:i:s", $timestamp); ?>)</span>
                    <?php endif; ?>
                </p>
                <p>📣 Triggered <?php echo $utils->getTriggerTotal($trigger['id']); ?> time/s in total</p>
                <p class="d-inline text-break">🚀 Activation code: #<?php echo $trigger['stringUrl']; ?>
                <div class="d-inline-block text-decoration-none text-muted text-break">
                    <span class="align-middle d-inline" id="triggerUrl">
                        <?php
                        if (isset($_SERVER['HTTPS'])) {
                            $extraS = "s";
                        } else {
                            $extraS = "";
                        }
                        $actualPath = "http" . $extraS . "://" . $_SERVER['HTTP_HOST'] . str_replace(basename($_SERVER['SCRIPT_FILENAME']), "", $_SERVER['PHP_SELF']); // if project is in subfolder, useful when coding in local with an ending / 
                        echo $actualPath . "trigger.php?t=" . $trigger['stringUrl']; ?>
                    </span>
                    <a class="btn btn-dark d-inline text-muted" onclick="copy();" id="buttonId" style="padding-bottom:12px;">
                        <span class="material-symbols-outlined align-middle">
                            content_copy
                        </span>
                    </a>


                </div>
                </p>

            </div>
            <div class="col-12 mt-5">
                <h6>Alert History </h6>
                <p class="text-muted">Represented in number of occurrences per day</p>
                <canvas id="myChart"></canvas>

            </div>
            <div class="col-12 mt-5">
            <h6>Data History</h6>
            <p class="text-muted">Showing only the last 100 data entries</p>
            <?php
            
            // TODO pagination to avoid printing 100k items, for now, just last 100 items
            $allData = $utils->getAllData($trigger['id'], 100);
            if (!boolval($allData['success'])) : ?>
                <p class='text-muted'>No data nor files recieved yet. Remember that it needs to be passed through the <code>m</code> parameter (-d "m=message" in curl) and files need to be <code>uploaded</code> (-F @/path/file.txt in curl)</p>
            <?php else : ?>
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th scope="col">Timestamp</th>
                            <th scope="col">Data</th>
                            <th scope="col">Logfile</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allData['allData'] as $data) : ?>
                        <tr>
                            <td><?php echo  htmlentities(date("d/m/Y H:i:s", $data['timestamp']));?></td>
                            <td><?php echo htmlentities($data['extraData']);?></td>
                            <td><a href="logview.php?t=<?php echo htmlentities($trigger['stringUrl']);?>&f=<?php echo htmlentities($data['logFilename'])."&d=".$data['timestamp'];?>">Log available</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            </div>

            <!-- Javscript code starts here -->
            <script defer>
                function copy() {
                    // clipboard
                    var tocopy = document.getElementById('triggerUrl').innerText;
                    var textArea = document.createElement("textarea");
                    textArea.value = tocopy;
                    document.body.appendChild(textArea);
                    textArea.select();
                    navigator.clipboard.writeText(textArea.value);
                    textArea.remove();
                    // animation
                    var button = document.getElementById('buttonId');
                    var before = button.innerHTML;
                    button.innerHTML = "Copied!";
                    setTimeout(function() {
                        button.innerHTML = before;
                    }, 1000);
                }
            </script>
            <script defer>
                // historic chart 
                function ConvertDate(UNIXtimestamp) {
                    timestamp = UNIXtimestamp * 1000;
                    date = new Date(timestamp);
                    year = date.getFullYear();
                    month = date.getMonth();
                    day = date.getDate();
                    hours = date.getHours();
                    min = date.getMinutes();
                    secs = date.getSeconds();

                    const zeroPad = (num, places) => String(num).padStart(places, '0')
                    // 2020-02-15 18:37:39
                    return year + "-" + zeroPad(month, 2) + "-" + zeroPad(day, 2) + " " + zeroPad(hours, 2) + ":" + zeroPad(min, 2) + ":" + zeroPad(secs, 2);
                }

                const ctx = document.getElementById('myChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [],
                        legend: {
                            display: true
                        },
                        datasets: [{
                            fill: false,
                            data: dataGen(),
                            backgroundColor: "#ffc107",
                            borderColor: "#ffc107",
                            type: 'line',
                            pointRadius: 2,
                            lineTension: 0.1,
                            borderWidth: 2,
                            pointHoverRadius: 9,
                        }]
                    },
                    options: {
                        animation: false,
                        responsive: true,
                        scales: {
                            xAxes: [{
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Time'
                                },
                                type: 'time',
                                time: {
                                    unit: 'day',
                                    displayFormats: {
                                        day: 'DD-MM-YYYY'
                                    },
                                    tooltipFormat: 'DD-MM-YYYY'
                                },
                                ticks: {
                                    maxRotation: 90,
                                    minRotation: 90
                                }
                            }],
                            yAxes: [{
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Triggers per day'
                                },
                            }]
                        },

                        legend: {
                            display: false
                        }

                    }
                });

                function dataGen() {
                    allTimestamps = [<?php echo $utils->getHistoricTimestamps($trigger['id']); ?>]
                    return allTimestamps.map(subArray => {
                        //split values, ex: ['2022-08-04,4]
                        var date = subArray[0]
                        var ocurrences = subArray[1]
                        return {
                            x: new Date(date),
                            y: ocurrences
                        };
                    });
                }
            </script>
            <script defer>
                // EDIT js
                var title = document.getElementById('staticTitle');
                var input = document.getElementById('triggerTitle');
                var button = document.getElementById('editBtn');
                var cancel = document.getElementById('editBtncancel');
                var deleteBtn = document.getElementById('deleteButton');
                submit = false

                function edit() {
                    if (!submit) {
                        cancel.classList.add('d-inline-block');
                        cancel.classList.remove('d-none');
                        input.classList.add('d-block');
                        input.classList.remove('d-none');
                        title.classList.add('d-none');
                        title.classList.remove('d-block');
                        deleteBtn.classList.add('d-none');
                        deleteBtn.classList.remove('d-inline-block');
                        button.innerHTML = "<span class=\"material-symbols-outlined align-middle\">done</span>";
                        submit = true;
                    } else {
                        document.getElementById('editForm').submit()
                    }
                }

                function cancelEdit() {
                    cancel.classList.remove('d-inline-block');
                    cancel.classList.add('d-none');
                    input.classList.remove('d-block');
                    input.classList.add('d-none');
                    title.classList.remove('d-none');
                    title.classList.add('d-block');
                    deleteBtn.classList.remove('d-none');
                    deleteBtn.classList.add('d-inline-block');
                    button.innerHTML = "<span class=\"material-symbols-outlined align-middle\">edit</span>";
                    submit = false;
                }
            </script>
</body>

</html>