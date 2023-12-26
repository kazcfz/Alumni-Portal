<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Maintenance</title>

    <link rel="stylesheet" href="css/styles.css">

    <!-- Bootstrap CSS --><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons --><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Animate.css --><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        .traffic-cone {
            height: 300px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Flash message -->
        <?php if (isset($tempFlash)){ ?>
            <div class="row justify-content-center position-absolute top-0 start-50 translate-middle mt-5">
                <div class="col-auto">
                    <div class="alert <?php echo (isset($_SESSION['flash_mode'])) ? $_SESSION['flash_mode'] : '' . (isset($tempFlash) ? $tempFlash : ''); ?> mt-4 py-2 fade-out-alert row align-items-center" role="alert">
                        <i class="bi <?php echo (isset($tempFlash) && $tempFlash == "alert-success" ? "bi-check-circle" : ((isset($tempFlash) && $tempFlash == "alert-primary" ? "bi-info-circle" : ((isset($tempFlash) && ($tempFlash == "alert-danger" || $tempFlash == "alert-warning") ? "bi-exclamation-triangle" : ""))))) ?> login-bi col-auto px-0"></i><div class="col ms-1"><?php echo isset($_SESSION['flash']) ? $_SESSION['flash'] : '' ?></div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="container mt-5 bg-white mainView">
            <div class="row my-5 mx-5 text-center">
                <!-- Main content -->
                <div class="col align-self-center">
                    <h1 class="mt-4">Sorry!</h1>
                    <img class="img-fluid mx-auto d-block traffic-cone my-4 slide-left" src="images/maintenance.jpg" alt="Maintenance image"/>
                    <h5>We're having trouble connecting you to our database. Please check back later!</h5>
                    <p class="fw-medium fst-italic">(But this is just a local webserver on your computer, so do check your database connection)</p>
                    <a role="button" href="main.php" class="btn btn-primary fw-medium mt-4 mb-5">Back to Main</a>
                </div>

            </div>
        </div>
    </div>
    <!-- Boostrap JS --><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>