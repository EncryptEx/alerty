<?php
$errorMsg = "";
$errorClass = "";
$successMsg = "";
if (isset($_GET['e'])) {
    if ($_GET['e'] == 1) {
        $errorClass = "was-validated";
    }
    elseif ($_GET['e'] == 2){
        // login error
        $errorMsg = "Incorrect email or password";
    } elseif ($_GET['e'] == 3) {
        // email verif. pending
        $errorMsg = "Your account needs an email verification, please check your email";
    } elseif ($_GET['e'] == 5) {
        // banned
        $errorMsg = "Your account has been suspended.";
    } elseif ($_GET['e'] == 6) {
        // banned
        $errorMsg = "This account cannot be verified. An error ocurred.";
    }
}
if (isset($_GET['s'])) {
    if ($_GET['s'] == 1) {
        $successMsg = "Great! Now check your inbox in order to activate your account.";
    }
    if ($_GET['s'] == 2) {
        $successMsg = "Account verified! Now login, please.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require realpath('./../private/templates/meta.html'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    <title>Alerty | Login</title>
    <style>
    </style>

</head>
<?php require realpath(__DIR__ . '/../private/templates/navbar.php'); ?>

<body class="text-bg-dark text-white">

    <!-- HERO -->
    <div class="container col-xxl-8 px-4 py-5">
        <form action="auth.php" method="POST" class="needs-validation row justify-content-center <?php echo $errorClass; ?>" novalidate>
            <div class="col-12 col-md-8 col-lg-6">

                <h3>Sign In</h3>
                <hr>
                <div class="alert alert-warning d-flex align-items-center alert-dismissible fade show" role="alert" style="display: <?php if($errorMsg != "") {echo "block";} else {echo "none";}?> !important">
                    <span class="material-symbols-outlined align-middle" style="margin-right:10px">
                        warning
                    </span>
                    <span class="align-middle">
                        <?php echo $errorMsg; ?>
                    </span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="window.location.href = window.location.href.split('?')[0];"></button>
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
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="window.location.href = window.location.href.split('?')[0];"></button>
            </div>
                <label for="staticEmail" class="col-sm-2 col-form-label">Email</label>
                <div class="input-group has-validation">
                    <input name="email" type="email" class="form-control" id="inputEmail" required placeholder="john.snow@example.con">
                    <div class="invalid-feedback">
                        Please introduce an email.
                    </div>
                </div>
                <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                <div class="input-group has-validation">
                    <input name="password" type="password" class="form-control" id="inputPassword" required placeholder="*******">
                    <div class="invalid-feedback">
                        Please introduce a password.
                    </div>
                </div>
                <br>
                <button class="btn btn-warning" type="submit">Login</button>
            </div>
            <script>
                // Example starter JavaScript for disabling form submissions if there are invalid fields
                (function() {
                    'use strict'

                    // Fetch all the forms we want to apply custom Bootstrap validation styles to
                    var forms = document.querySelectorAll('.needs-validation')

                    // Loop over them and prevent submission
                    Array.prototype.slice.call(forms)
                        .forEach(function(form) {
                            form.addEventListener('submit', function(event) {
                                if (!form.checkValidity()) {
                                    event.preventDefault()
                                    event.stopPropagation()
                                }
                                form.classList.add('was-validated')
                            }, false)
                        })
                })()
            </script>
        </form>
    </div>
</body>

</html>