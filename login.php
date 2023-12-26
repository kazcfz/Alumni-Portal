<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Login</title>

    <link rel="stylesheet" href="css/styles.css">

    <!-- Bootstrap CSS --><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons --><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Animate.css --><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
</head>
<body>
    <?php
        include 'db_controller.php';
        $conn->select_db("Alumni");

        session_start();

        // Navigate to main_menu if user is logged in
        if(isset($_SESSION['logged_account']) && $_SESSION['logged_account']['type'] == 'user')
            header('Location: main_menu.php');
        elseif(isset($_SESSION['logged_account']) && $_SESSION['logged_account']['type'] == 'admin')
            header('Location: main_menu_admin.php');

        // Assign session data or initialize new array
        $formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : array();
        $loginErrors = isset($_SESSION['login_errors']) ? $_SESSION['login_errors'] : array();
        $verified = isset($_SESSION['verified']) ? $_SESSION['verified'] : array();
        $tempFlash;
        if (isset($_SESSION['flash_mode'])){
            $tempFlash = $_SESSION['flash_mode'];
            unset($_SESSION['flash_mode']);
        }

        // Clear session data
        // unset($_SESSION['form_data']);
        unset($_SESSION['login_errors']);
        unset($_SESSION['verified']);
    ?>

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
            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    unset($loginErrors);
                    foreach ($_POST as $key => $value) {
                        //trim every key except password
                        if ($key != "password") {
                            $_POST[$key] = trim($_POST[$key]);
                            $value = trim($value);
                        }

                        // check for empty email field
                        if ($key == 'email' && $value == '')
                            $loginErrors[$key] = '*Email is required.';

                        // check for empty password field
                        if ($key == 'password' && $value == '')
                            $loginErrors[$key] = '*Password is required.';
                    }

                    if (!isset($loginErrors['email'])) {
                        // Find password from entered email
                        $SQLLoginAccount = $conn->prepare("SELECT password FROM account_table WHERE email = ?");
                        $SQLLoginAccount->bind_param("s",$_POST['email']);
                        $SQLLoginAccount->execute();
                        $result = $SQLLoginAccount->get_result();
                        if ($result->num_rows > 0) {
                            $hashedPassword = $result->fetch_assoc()['password'];

                            // Verify password against hashed password saved in DB
                            if (password_verify($_POST['password'], $hashedPassword)) {
                                unset($_SESSION['flash_mode']);
                                unset($_SESSION['flash']);
                                unset($loginErrors);

                                // Get and save account info from account_table into session
                                $SQLGetAccountInfo = $conn->prepare("SELECT email, type, status FROM account_table WHERE email = ?");
                                $SQLGetAccountInfo->bind_param("s", $_POST['email']);
                                $SQLGetAccountInfo->execute();
                                $accountInfo = $SQLGetAccountInfo->get_result()->fetch_assoc();
                                $_SESSION['logged_account'] = $accountInfo;

                                // Get and save user info from user_table into session
                                $SQLGetUserInfo = $conn->prepare("SELECT * FROM user_table WHERE email = ?");
                                $SQLGetUserInfo->bind_param("s", $_POST['email']);
                                $SQLGetUserInfo->execute();
                                $userInfo = $SQLGetUserInfo->get_result()->fetch_assoc();
                                $_SESSION['logged_user'] = $userInfo;

                                // Redirects user to the page they tried to visit, if none, main_menu.php
                                header('Location: '.(isset($_SESSION['redirect_url']) ? basename($_SESSION['redirect_url']) : ($_SESSION['logged_account']['type'] == 'user' ? 'main_menu.php' : 'main_menu_admin.php') ));
                                unset($_SESSION['redirect_url']);
                            } elseif (!isset($loginErrors['password'])) {
                                $loginErrors['email'] = '';
                                $loginErrors['password'] = 'Invalid email or password';
                            }
                        // No result means email is not registered
                        } else {
                            $loginErrors['email'] = '*Email is not registered. <strong><a class="link-underline link-underline-opacity-0" href="registration.php">Register here</a></strong>.';
                            unset($loginErrors['password']);
                            $verified['email'] = true;
                            $_SESSION['form_data'] = $_POST;
                        }

                        // If there are invalid data, retrieve form data, login_errors, verified fields
                        if (!empty($loginErrors)) {
                            $_SESSION['form_data'] = $_POST;
                            $_SESSION['login_errors'] = $loginErrors;
                            $_SESSION['verified'] = $verified;
                        }
                    }
                }
            ?>
            
            <div class="row align-items-center my-5 mx-5">
                <div class="col ms-3 mt-5 mb-5">
                    <img class="img-fluid" src="images/signin-image.jpg" alt="Signin image" width="450"/>
                </div>
                <div class="col <?php echo (isset($loginErrors) && empty($loginErrors)) ? 'slide-left' : NULL ?>">
                    <h1>Log in</h1>
                    <form id="registrationForm" class="form-floating needs-validation <?php echo (isset($loginErrors) && !empty($loginErrors)) ? 'animate__animated animate__headShake animate__fast' : NULL ?>" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <!-- Email -->
                        <div class="row">
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control <?php echo (isset($loginErrors['email'])) ? 'is-invalid' : ''; ?>" id="email" name="email" placeholder="johndoe@email.com" maxlength="50" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
                                    <label for="email">Email</label>
                                    <?php echo (isset($loginErrors['email'])) ? '<div class="invalid-feedback">' . $loginErrors['email'] . '</div>' : ''; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="row">
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control <?php echo (isset($loginErrors['password'])) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="123456">
                                    <label for="password">Password</label>
                                    <?php echo (isset($loginErrors['password'])) ? '<div class="invalid-feedback">' . $loginErrors['password'] . '</div>' : ''; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Login button -->
                        <div class="row mb-5">
                            <div class="d-grid gap-2 col-12 mx-auto">
                                <button type="submit" class="btn btn-primary py-2 fw-medium">Log in</button>
                            </div>
                        </div>
                    </form>

                    <hr/>

                    <div class="row mt-5 justify-content-start align-items-center">
                        <div class="col-auto pe-0"><span class="fw-medium">Don't have an account?</span></div>
                        <div class="col ps-2"><a class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover" href="registration.php">Register</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Boostrap JS --><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        // Reset all validations (mostly on Bootstrap)
        function resetValidation(){
            // Remove error classes and messages
            while (document.getElementsByClassName("is-invalid").length !== 0)
                document.getElementsByClassName("is-invalid")[0].classList.remove("is-invalid");

            // Remove valid classes and messages
            while (document.getElementsByClassName("is-valid").length !== 0)
                document.getElementsByClassName("is-valid")[0].classList.remove("is-valid");

            // Clear error messages
            document.querySelectorAll('.invalid-feedback').forEach(errorMessage => { errorMessage.innerHTML = ''; })
        }

        // Ignore confirm form resubmission after POST request to login
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>
</body>
</html>