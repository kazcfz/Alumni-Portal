<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Register</title>

    <link rel="stylesheet" href="css/styles.css">

    <!-- Bootstrap CSS --><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons --><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Animate.css --><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
</head>
<body>
    <?php
        session_start();

        unset($_SESSION['login_errors']);

        // Navigate to main_menu if user is logged in
        if(isset($_SESSION['logged_account']) && $_SESSION['logged_account']['type'] == 'user')
            header('Location: main_menu.php');
        elseif(isset($_SESSION['logged_account']) && $_SESSION['logged_account']['type'] == 'admin')
            header('Location: main_menu_admin.php');

        // Assign session data or initialize new array
        $formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : array();
        $errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : array();
        $verified = isset($_SESSION['verified']) ? $_SESSION['verified'] : array();

        // Clear session data
        unset($_SESSION['form_data']);
        unset($_SESSION['errors']);
        unset($_SESSION['verified']);
    ?>

    <div class="container mt-5 bg-white mainView">
        <div class="row align-items-center">
            <!-- Registration banner -->
            <div class="col ms-3 mt-5 ms-5">
                <img class="img-fluid" src="images/signup-image.jpg" alt="Signup image" width="450"/>
            </div>

            <div class="col my-5 me-5 <?php echo (isset($errors) && empty($errors)) ? 'slide-left' : NULL ?>">
                <h1>Register</h1>
                
                <form id="registrationForm" class="form-floating needs-validation <?php echo (isset($errors) && !empty($errors)) ? 'animate__animated animate__headShake animate__fast' : NULL ?>" action="<?php echo htmlspecialchars('process_register.php');?>" method="POST">
                    <!-- First name, Last name -->
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control <?php echo (isset($errors['firstName'])) ? 'is-invalid' : ((isset($verified['firstName'])) ? 'is-valid' : ''); ?>" id="firstName" name="firstName" placeholder="John" value="<?php echo isset($formData["firstName"]) ? htmlspecialchars($formData["firstName"]) : "";?>">
                                <label for="firstName">First Name<strong class="text-danger">*</strong></label>
                                <?php echo (isset($errors['firstName'])) ? '<div class="invalid-feedback">' . $errors['firstName'] . '</div>' : ''; ?>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control <?php echo (isset($errors['lastName'])) ? 'is-invalid' : ((isset($verified['lastName'])) ? 'is-valid' : ''); ?>" id="lastName" name="lastName" placeholder="Doe" value="<?php echo isset($formData['lastName']) ? htmlspecialchars($formData['lastName']) : ''; ?>">
                                <label for="lastName">Last Name<strong class="text-danger">*</strong></label>
                                <?php echo (isset($errors['lastName'])) ? '<div class="invalid-feedback">' . $errors['lastName'] . '</div>' : ''; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Date of Birth, Gender -->
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="date" class="form-control <?php echo (isset($errors['dob'])) ? 'is-invalid' : ((isset($verified['dob'])) ? 'is-valid' : ''); ?>" id="dob" name="dob" value="<?php echo isset($formData['dob']) ? htmlspecialchars($formData['dob']) : ''; ?>">
                                <label for="dob">Date of Birth<strong class="text-danger">*</strong></label>
                                <?php echo (isset($errors['dob'])) ? '<div class="invalid-feedback">' . $errors['dob'] . '</div>' : ''; ?>
                            </div>
                        </div>
                        <div class="col">
                            <p class="mb-1">Gender<strong class="text-danger">*</strong></p>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input <?php echo (isset($errors['gender'])) ? 'is-invalid' : ((isset($verified['gender'])) ? 'is-valid' : ''); ?>" type="radio" name="gender" id="genderFemale" value="Female" checked <?php echo isset($formData['gender']) ? (($formData['gender'] == 'Female') ? 'checked' : '') : ''; ?>>
                                <label class="form-check-label" for="genderFemale">Female</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input <?php echo (isset($errors['gender'])) ? 'is-invalid' : ((isset($verified['gender'])) ? 'is-valid' : ''); ?>" type="radio" name="gender" id="genderMale" value="Male" <?php echo isset($formData['gender']) ? (($formData['gender'] == 'Male') ? 'checked' : '') : ''; ?>>
                                <label class="form-check-label" for="genderMale">Male</label>
                            </div>
                            <?php echo (isset($errors['gender'])) ? '<div class="invalid-feedback">' . $errors['gender'] . '</div>' : ''; ?>
                        </div>
                    </div>

                    <!-- Email, Hometown -->
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control <?php echo (isset($errors['email'])) ? 'is-invalid' : ((isset($verified['email'])) ? 'is-valid' : ''); ?>" id="email" name="email" placeholder="johndoe@email.com" value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : ''; ?>">
                                <label for="email">Email<strong class="text-danger">*</strong></label>
                                <?php echo (isset($errors['email'])) ? '<div class="invalid-feedback">' . $errors['email'] . '</div>' : ''; ?>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control <?php echo (isset($errors['hometown'])) ? 'is-invalid' : ((isset($verified['hometown'])) ? 'is-valid' : ''); ?>" id="hometown" name="hometown" placeholder="Kuching" value="<?php echo isset($formData['hometown']) ? htmlspecialchars($formData['hometown']) : ''; ?>">
                                <label for="hometown">Hometown<strong class="text-danger">*</strong></label>
                                <?php echo (isset($errors['hometown'])) ? '<div class="invalid-feedback">' . $errors['hometown'] . '</div>' : ''; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Password, Confirm Password -->
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control <?php echo (isset($errors['password'])) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="123456">
                                <label for="password">Password<strong class="text-danger">*</strong></label>
                                <?php echo (isset($errors['password'])) ? '<div class="invalid-feedback">' . $errors['password'] . '</div>' : ''; ?>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control <?php echo (isset($errors['confirmPassword'])) ? 'is-invalid' : ''; ?>" id="confirmPassword" name="confirmPassword" placeholder="123456">
                                <label for="confirmPassword">Confirm Password<strong class="text-danger">*</strong></label>
                                <?php echo (isset($errors['confirmPassword'])) ? '<div class="invalid-feedback">' . $errors['confirmPassword'] . '</div>' : ''; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Register button -->
                    <div class="row">
                        <div class="d-grid gap-2 col-12 mx-auto">
                            <button type="submit" class="btn btn-primary py-2 fw-medium">Register</button>
                        </div>
                    </div>

                    <!-- *Required field message, Reset Form -->
                    <div class="row mt-3 mb-5 justify-content-between align-items-center">
                        <div class="col"><span class="text-secondary fst-italic"><strong class="text-danger">*</strong>Indicates required field</span></div>
                        <div class="col-auto"><button type="reset" class="btn btn-outline-danger me-2" onclick="resetValidation(event)">Reset Form</button></div>
                    </div>
                </form>
                
                <hr/>

                <!-- Login button -->
                <div class="row mt-5 justify-content-start align-items-center">
                    <div class="col-auto pe-0"><span class="fw-medium">Already have an account?</span></div>
                    <div class="col ps-2"><a class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover" href="login.php">Login</a></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Boostrap JS --><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        // Reset form
        // Javascript required since PHP repopulates the input values making that the default to reset to
        function resetValidation(event){
            event.preventDefault();
            // Remove error classes and msgs
            while (document.getElementsByClassName("is-invalid").length !== 0)
                document.getElementsByClassName("is-invalid")[0].classList.remove("is-invalid");

            // Remove valid classes and msgs
            while (document.getElementsByClassName("is-valid").length !== 0)
                document.getElementsByClassName("is-valid")[0].classList.remove("is-valid");

            // Remove input values
            document.querySelectorAll("input").forEach(element => {
                if (!(element.type === "radio" && element.name === "gender"))
                    element.value = "";
            });

            // Clear error msgs
            document.querySelectorAll('.invalid-feedback').forEach(errorMessage => { errorMessage.innerHTML = ''; })
        }
    </script>
</body>
</html>