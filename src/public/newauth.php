<?php 
require_once(__DIR__ . '/../private/utils.php');
use \Utils\Utilities;
$utils = new Utilities();

if(!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['password'])){
    header('location:register.php?e=1');
    die();
}
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405); // method not allowed
    die();
}

$entered_name = $_POST['name'];
$entered_email = $_POST['email'];
$encrypted_password = hash("SHA256", $_POST['password']);

$sanitized = filter_var($entered_email, FILTER_SANITIZE_EMAIL);
if($entered_email !== $sanitized && filter_var($entered_email, FILTER_VALIDATE_EMAIL)) {
    // email wrong
    header('Location: register.php?e=1');
    die();
}

// call DB and register User
$registerResult = $utils->Register($entered_name, $entered_email, $encrypted_password);
if(boolval($registerResult)) {
    // send the user the email verification
    $result = $utils->sendEmailVerification($entered_email, $entered_name);

    if(!$result['success']){
        header("location:register.php?e=2");
        exit;
    }
    header("location:login.php?s=1");
    exit;
} else {
    // return errror
    header('location:register.php?e=3');
    exit;
}

?>
Redirecting...
if(!filter_var($email, FILTER_VALIDATE_EMAIL))