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

if(!isset($_POST['name']) || !isset($_POST['id'])) {
    header('location:view.php?t='.htmlentities($_POST['stringUrl']).'&e=1');
    die();
}

$trigger_name = $_POST['name'];
$trigger_id = $_POST['id'];
$trigger_ownerId = $_SESSION['userid'];


// save to db
$result = $utils->updateTrigger($trigger_id, $trigger_name, $trigger_ownerId);

if($result!=1){
    // wrong
    header("location:view.php?t=".htmlentities($_POST['stringUrl'])."&e=2");
} else {
    // all great
    header("location:view.php?t=".htmlentities($_POST['stringUrl'])."&s=1");
}

?>