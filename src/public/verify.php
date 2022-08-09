<?php 
require_once('./../private/utils.php');
use \Utils\Utilities;
$utils = new Utilities();

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405); // method not allowed
    die();
}

if(!isset($_GET['email']) || !isset($_GET['eh'])){
    http_response_code(400); //invalid request
    die();
}

$vanillaEmail = $_GET['email'];
$hashedEmail = $_GET['eh'];



if(hash("SHA256", $_ENV['HASH_SALT'].$vanillaEmail) != $hashedEmail) {
    //not legit link, throw error
    http_response_code(403); // not authorized
    die();
}


$verifResult = $utils->Verify($vanillaEmail);
if(boolval($verifResult)) {
    header("location:login.php?s=2");
} else {
    // return errror
    header('location:login.php?e=6');
}

?>
Redirecting...