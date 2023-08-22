<?php

require_once('./../private/utils.php');

use Utils\Utilities;

$utils = new Utilities();

if (!isset($_GET['t'])) { # if is not set key, die
    http_response_code(400); # invalid request
    die();
}

$stringUrl = $_GET['t'];
# Set header to json
header('Content-type: application/json; charset=utf-8');

$doesExist = $utils->doesExist($stringUrl); # Returned vals: doesExist (bool)? and (if yes), id
if ($doesExist['success']) {
    $triggerId = $doesExist['id']; #since does exist,
    $actionToDo = $doesExist['action'];
    $ownerId = $doesExist['ownerId'];
    $triggerName = $doesExist['name'];

    $data = "";
    $status = null;
    // check if data has been sent
    if (isset($_GET['m'])) {
        $data = $_GET['m'];
    }
    if (isset($_POST['m'])) {
        $data = $_POST['m'];
    }
    if (isset($_GET['s'])) {
        $status = $_GET['s'];
    }
    if (isset($_POST['s'])) {
        $status = $_POST['s'];
    }

    $filePath = null;
    $newName = null;
    if (isset($_FILES) && count($_FILES) == 1) {

        $uploaddir = "/../private/uploads/";
        $newName = "log" . time() . "-" . $triggerId . ".txt";
        $uploadfile = __DIR__ . $uploaddir . $newName;


        $allowed = array('txt', 'log');
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        if (!in_array($ext, $allowed)) {
            $utils->showError('Only .txt and .log extensions are supported');
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            $filePath = $uploadfile;
        } else {
            $utils->showError('There was an error while uploading the file');
        }
    }


    if ($utils->getActionId($actionToDo) == 1) { // is email (alert)
        # send email to ownerId

        $emailResult = $utils->sendEmailTo($ownerId, $triggerName, $stringUrl, $data, $status, $filePath);

        if (!$emailResult['success']) {
            $utils->showError($emailResult['message']);
        }
    } else {
        # id does not match, throw error
        $utils->showError('There was an error while trying to fetch the action type');
    }

    # save log
    $result = $utils->triggerLog($triggerId, $data, $newName);

    if (!$result) { # if couldn't be saved, throw error
        $utils->showError('There was an error while trying to save the log');
    }

    http_response_code(200);
    print(
        json_encode(
            [
                'success' => true,
                'message' => 'Action performed successfully!',
                'timestamp' => time()
            ]
        )
    );
} else {
    http_response_code(404);
    print(
        json_encode(
            [
                'success' => false,
                'message' => 'The requested TriggerID does not exist',
                'timestamp' => time()
            ]
        )
    );
}