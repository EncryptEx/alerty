<?php 

require_once('./../private/utils.php');
use \Utils\Utilities;
$utils = new Utilities();

if(!isset($_GET['t'])) { # if is not set key, die
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

    $data="";
    // check if data has been sent
    if(isset($_GET['m'])) {
        $data = $_GET['m'];
    } 
    if(isset($_POST['m'])) {
        $data = $_POST['m'];
    } 
    

    if($utils->getActionName($actionToDo) == "email"){
        # send email to ownerId

        $emailResult = $utils->sendEmailTo($ownerId, $triggerName, $stringUrl, $data);

        if(!$emailResult['success']){
            http_response_code(500);
            print(json_encode(
                [
                    'success'=>FALSE, 
                    'message'=>$emailResult['message'],
                    'timestamp'=>time()
                ]));
            die();
        }

    } else {
        # id does not match, throw error
        http_response_code(500);
        print(json_encode(
            [
                'success'=>FALSE, 
                'message'=>'There was an error while trying to fetch the action type',
                'timestamp'=>time()
            ]));
        die();
    }

    # save log
    $result = $utils->triggerLog($triggerId, $data);

    if (!$result)  # if couldn't be saved, throw error
    { 
        http_response_code(500);
        print(json_encode(
            [
                'success'=>FALSE, 
                'message'=>'There was an error while trying to save the log',
                'timestamp'=>time()
            ]));
        die();
    }

    http_response_code(200);
    print(json_encode(
        [
            'success'=>TRUE,
            'message'=>'Action performed successfully!',
            'timestamp'=>time()
        ]
    ));
} else {
    http_response_code(404);
    print(json_encode(
        [
            'success'=>FALSE, 
            'message'=>'The requested TriggerID does not exist',
            'timestamp'=>time()
        ]));
}
