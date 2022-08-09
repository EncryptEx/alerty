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
if(!isset($_POST['stringUrl'])){
    header('location:createTrigger.php?e=1');
    die();
}

if(!isset($_POST['id'])) {
    header('location:view.php?t='.htmlentities($_POST['stringUrl']).'&e=1');
    die();
}

$trigger_id = $_POST['id'];
$trigger_ownerId = $_SESSION['userid'];


// save to db
$result = $utils->deleteTrigger($trigger_id, $trigger_ownerId);

if($result!=1){
    // wrong
    header("location:dashboard.php?e=2");
} else {
    // all great
    header("location:dashboard.php?s=2");
}

?>