<?php 
session_start();
require './../private/utils.php';

use \Utils\Utilities;

$utils = new Utilities;
$utils->redirectIfNotLogged();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405); // method not allowed
    die();
}

if(!isset($_POST['name']) || !isset($_POST['type'])) {
    header('location:createTrigger.php?e=1');
    die();
}

$trigger_name = $_POST['name'];

$trigger_type = $_POST['type'];

$triggerUrlDoesExist = True;
while($triggerUrlDoesExist) {
    $newString = $utils->generateString(11);
    $triggerUrlDoesExist = $utils->doesExist($newString)['success'];
}

// save to db
$result = $utils->saveTrigger($trigger_name, $newString, $trigger_type, $_SESSION['userid']);

if($result!=1){
    header("location:createTrigger.php?e=2");
} else {
    header("location:dashboard.php?s=1");
}

?>