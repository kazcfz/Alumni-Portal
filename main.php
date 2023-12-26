<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Main</title>

    <link rel="stylesheet" href="css/styles.css">

    <!-- Bootstrap CSS --><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons --><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        .mainBanner{
            padding-right: calc(var(--bs-gutter-x) * 0);
        }
        .mainImg{
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .alert-main{
            bottom: 50px;
        }
    </style>
</head>
<body>
    <?php
        include 'init_db.php';

        session_start();
        $tempFlash;
        if (isset($_SESSION['flash_mode']) && $_SESSION['flash_mode'] == "alert-success"){
            $tempFlash = $_SESSION['flash_mode'];
            unset($_SESSION['flash_mode']);
        }
    ?>

    <!-- Flash message -->
    <div class="container-fluid">
        <?php if (isset($_SESSION['flash_mode']) || isset($tempFlash)){ ?>
            <div class="row justify-content-center position-absolute offset-1 ps-4 mt-0">
                <div class="col">
                    <div class="alert <?php echo (isset($_SESSION['flash_mode'])) ? $_SESSION['flash_mode'] : '' . (isset($tempFlash) ? $tempFlash : ''); ?> mt-4 py-2 fade-in fade-out-alert row align-items-center alert-main" role="alert">
                        <i class="bi <?php echo (isset($tempFlash) && $tempFlash == "alert-success" ? "bi-check-circle" : ((isset($_SESSION['flash_mode']) && $_SESSION['flash_mode'] == "alert-primary" ? "bi-info-circle" : ""))) ?> login-bi col-auto px-0"></i><div class="col ms-1"><?php echo isset($_SESSION['flash']) ? $_SESSION['flash'] : '' ?></div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="container mt-5 bg-white mainView">
            <div class="row align-items-center">
                <div class="col ms-4 slide-left">
                    <!-- Heading and text -->
                    <h1 class="text-center mb-4">Alumni Portal</h1>
                    <p>Stay connected with your alma mater and friends. Build networks and propel your career to the next stage.</p>
                    
                    <div class="row justify-content-center mt-5">
                        <!-- Login button -->
                        <div class="col-auto">
                            <a role="button" href="login.php" class="btn btn-primary fw-medium px-4 py-2">Login</a>
                        </div>
                        <!-- Register button -->
                        <div class="col-auto">
                            <a role="button" href="registration.php" class="btn btn-outline-primary fw-medium px-4 py-2">Register</a>
                        </div>
                    </div>
                </div>

                <!-- Main banner image -->
                <div class="col-8 mainBanner">
                    <img class="img-fluid mainImg" src="images/main_photo.jpg" alt="Main Photo"/>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Boostrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>