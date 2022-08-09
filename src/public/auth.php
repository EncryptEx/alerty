<?php
ini_set('session.gc_maxlifetime', 10800); // extend session time

session_start();

require_once('./../private/utils.php');
use \Utils\Utilities;
$utils = new Utilities();

if(!isset($_POST['email']) || !isset($_POST['password'])){
    header('location:login.php?e=1');
    die();
}

$entered_email = $_POST['email'];
$encrypted_password = hash("SHA256", $_POST['password']);

// call DB and logn
$loginResult = $utils->Login($entered_email, $encrypted_password);
if(boolval($loginResult['success'])) {
    // do login stuff
    if($loginResult['status'] == 0) {
        // email verification pending.
        header('location:login.php?e=3');
        die();
    }
    if($loginResult['status'] == 1 || $loginResult['status'] == 2) {
        // normal user
        $_SESSION['userid'] = $loginResult['id'];
        $_SESSION['realName'] = $loginResult['name'];
    }
    if($loginResult['status'] == 2) {
        // is admin
        $_SESSION['is_admin'] = True;
    }
    if($loginResult['status'] == 3) {
        // user banned
        header('location:login.php?e=5');
        die();
    }

    // no redirection so far.. 
    // redirect to dashboard.php
    header("location:dashboard.php");
} else {
    // return errror
    header('location:login.php?e=2');
}

?>
Redirecting...