<?php
$errorMsg = "";
$errorClass = "";
if (isset($_GET['e'])) {
    if ($_GET['e'] == 1) {
        $errorClass = "was-validated";
    }
    elseif ($_GET['e'] == 2){
        $errorMsg="Something went wrong while sending your email verification";
    
    } elseif ($_GET['e'] == 3) {
        // user already exists
        $errorMsg = "That email adress is already in use.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    <title>Alerty | Register</title>
    <style>
    </style>

</head>
<?php require realpath(__DIR__ . '/../private/templates/navbar.php'); ?>

<body class="text-bg-dark text-white">

    <!-- HERO -->
    <div class="container col-xxl-8 px-4 py-5">
        <form action="newauth.php" method="POST" class="needs-validation row justify-content-center <?php echo $errorClass; ?>" novalidate>
            <div class="col-12 col-md-8 col-lg-6">

                <h3>Sign-Up</h3>
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
                <label for="staticName" class="col-sm-2 col-form-label">Name</label>
                <div class="input-group has-validation">
                    <input name="name" type="name" class="form-control" id="inputName" required placeholder="John">
                    <div class="invalid-feedback">
                        Please introduce a name.
                    </div>
                </div>
                <label for="staticEmail" class="col-sm-2 col-form-label">Email</label>
                <div class="input-group has-validation">
                    <input name="email" type="email" class="form-control" id="inputEmail" required placeholder="john.snow@example.com">
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
                <button class="btn btn-warning" type="submit">Register</button>
            </div>
            <script>
                // JS BS5 validation
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