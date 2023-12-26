<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Apply Advertisement</title>

    <link rel="stylesheet" href="css/styles.css">

    <!-- Bootstrap CSS --><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons --><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .card {
            width: 100%;
            border: none;
            box-shadow: 0 2px 2px rgba(0,0,0,.08), 0 0 6px rgba(0,0,0,.05);
        }
    </style>
</head>
<body>
    <?php
        include 'db_controller.php';
        $conn->select_db("Alumni");

        session_start();
        include 'logged_user.php';

        
        $tempFlash;
        if (isset($_SESSION['flash_mode'])){
            $tempFlash = $_SESSION['flash_mode'];
            unset($_SESSION['flash_mode']);
        }

        // Assign session data or initialize new array
        $formData = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : array();
        $errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : array();
        $verified = isset($_SESSION['verified']) ? $_SESSION['verified'] : array();

        // Clear session data
        unset($_SESSION['form_data']);
        unset($_SESSION['errors']);
        unset($_SESSION['verified']);


        // POST method
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // GET the POST (which is retrieved from GET previously)
            $id = $_POST['id'];
            if (isset($_POST['id'])) 
                $_GET['id'] = $_POST['id'];

            $email = $_POST['email'];
            $result = $conn->query("SELECT COUNT(*) as row_count FROM advertisement_registration_table WHERE advertisement_id = $id AND email = '$email'");
            $row = $result->fetch_assoc();
            if ($row['row_count'] > 0) {
                // Set flash message and redirect back
                $_SESSION['flash_mode'] = "alert-secondary";
                $_SESSION['flash'] = "You've already applied for this Advertisement.";
                header('Location: view_advertisements.php');
            } else {
                foreach ($_POST as $key => $value) {
                    // Clean input
                    $_POST[$key] = trim($_POST[$key]); //remove extra spaces, tabs, newlines
                    $value = trim($value);

                    // Nullify empty inputs
                    if ($_POST[$key] == "")
                        $_POST[$key] = NULL;

        
                    // Check for required fields and validation
                    include "validation_field.php";
                }
                // If the email is the same being logged in with, ignore error
                if ($_POST['email'] == $_SESSION['logged_account']['email']) {
                    unset($errors['email']);
                    $verified['email'] = true;
                } else {
                    $errors['email'] = '*Email must belong to this account.';
                    $verified['email'] = false;
                }

                if (!empty($errors)) {
                    $formData = $_POST;
                } else {
                    try {
                        $SQLApplyAd = $conn->prepare("INSERT INTO advertisement_registration_table (advertisement_id, email, first_name, last_name, dob, gender, contact_number, hometown, current_location, job_position, qualification, year, university, company, resume) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $SQLApplyAd->bind_param("sssssssssssssss", $id, $_POST['email'], $_POST['firstName'], $_POST['lastName'], $_POST['dob'], $_POST['gender'], $_POST['contactNo'], $_POST['hometown'], $_POST['currentLocation'], $_POST['jobPosition'], $_POST['degreeProgram'], $_POST['yearGraduated'], $_POST['university'], $_POST['company'], $_POST['resume']);
                        if ($SQLApplyAd->execute()) {
                            // Set flash message and redirect back
                            $_SESSION['flash_mode'] = "alert-success";
                            $_SESSION['flash'] = "Successfully applied to this Advertisement.";
                            header('Location: view_advertisements.php');
                        }
                    } catch (Exception $e) {
                        $_SESSION['flash_mode'] = "alert-warning";
                        $_SESSION['flash'] = "An error has occured applying to this Advertisement.";
                        header('Location: view_advertisements.php');
                    }
                }
            }
        }

        // Get advertisement details
        $SQLGetAdInfo = $conn->prepare("SELECT * FROM advertisement_table WHERE id = ?");
        $SQLGetAdInfo->bind_param("s", $_GET['id']);
        $SQLGetAdInfo->execute();
        $result = $SQLGetAdInfo->get_result();
        $advertisement = $result->fetch_assoc();

        // Format date
        $date = new DateTime($advertisement['date_added']);
        $formattedDate = strtoupper($date->format('D, j M Y'));
    ?>

    <!-- Top nav bar -->
    <nav class="navbar sticky-top navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand mx-0 mb-0 h1" href="main_menu.php">Alumni Portal</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse me-5" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto fake-active">
                    <li class="nav-item mx-1">
                        <a class="nav-link px-5" href="main_menu.php"><i class="bi bi-house-door nav-bi "></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link px-5" href="view_alumni.php"><i class="bi bi-people nav-bi"></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link px-5" href="view_events.php"><i class="bi bi-calendar-event nav-bi"></i></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link px-5" aria-current="page" href="view_advertisements.php"><i class="bi bi-megaphone nav-bi"></i></a>
                    </li>
                </ul>
            </div>
            <?php include 'nav_user.php' ?>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="container my-3">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="breadcrumb-link text-secondary link-underline link-underline-opacity-0" href="main_menu.php">Home</a></li>
                <li class="breadcrumb-item"><a class="breadcrumb-link text-secondary link-underline link-underline-opacity-0" href="view_advertisements.php">Advertisements</a></li>
                <li class="breadcrumb-item breadcrumb-active" aria-current="page">Apply Advertisement</li>
            </ol>
        </nav>
    </div>
    
    <div class="container-fluid">
        <!-- Flash message -->
        <?php if (isset($tempFlash)){ ?>
            <div class="row justify-content-center position-absolute top-1 start-50 translate-middle">
                <div class="col-auto">
                    <div class="alert <?php echo (isset($_SESSION['flash_mode'])) ? $_SESSION['flash_mode'] : '' . (isset($tempFlash) ? $tempFlash : ''); ?> mt-4 py-2 fade-in fade-out-alert row align-items-center" role="alert">
                        <i class="bi <?php echo (isset($tempFlash) && $tempFlash == "alert-success" ? "bi-check-circle" : ((isset($tempFlash) && ($tempFlash == "alert-primary" || $tempFlash == "alert-secondary") ? "bi-info-circle" : ((isset($tempFlash) && $tempFlash == "alert-warning" ? "bi-exclamation-triangle" : ""))))) ?> login-bi col-auto px-0"></i><div class="col ms-1"><?php echo isset($_SESSION['flash']) ? $_SESSION['flash'] : '' ?></div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="container mb-5">
            <h1>Advertisement Application Form</h1>
            
            <div class="col mt-3 mb-4 px-0 mx-0">
                <div class="card">
                    <div class="row">
                        <!-- Image -->
                        <div class="col-auto">
                            <div class="image-container-events"><img src="images/<?php echo $advertisement['photo']; ?>" class="img-fluid profilePictureThumbnail" alt="profile_picture"></div>
                        </div>

                        <div class="col d-flex flex-column">
                            <!-- Body info -->
                            <div class="card-body px-2 flex-grow-1 me-4">
                                <?php 
                                    echo "<span class='fw-medium fs-6'>".$formattedDate."</span>"
                                    . (($advertisement['status'] == 'Active') ? "<span class='badge text-bg-success mt-1 float-end'>Active</span>" : "<span class='badge text-bg-secondary mt-1 float-end'>Inactive</span>")."
                                    <span class='float-end'>&nbsp;</span>
                                    <span class='badge text-bg-info mt-1 float-end'>".$advertisement['category']."</span>
                                    <br/>
                                    <span class='card-title h3'>".$advertisement['title']."</span>
                                    <br/><br/>
                                    <span class='card-text'>".$advertisement['description']."</span>";
                                ?>
                            </div>

                            <!-- Custom Button -->
                            <?php if ($advertisement['button_message'] != "" && $advertisement['button_link'] != "") { ?>
                                <div class="<?php echo ($advertisement['appliable'] == 1) ? 'mb-2' : 'mb-4' ?>">
                                    <a type='button' class='btn btn-outline-primary fw-medium px-4' href='<?php echo $advertisement['button_link']; ?>' target='_blank'><?php echo $advertisement['button_message']; ?><i class="bi bi-box-arrow-up-right ms-2" style="-webkit-text-stroke: 0.25px;"></i></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row slide-left mainView bg-white py-4 px-4">
            <!-- Form to register details -->
                <div class="row">
                    <!-- Upload resume -->
                    <h4>Resume</h4>
                    <!-- Additional function to view or delete resume -->
                    <?php 
                        echo (isset($loggedUser['resume']) && $loggedUser['resume'] != '') ? 
                            '<div class="row justify-content-start px-0 mb-5 ms-4">
                                <form action="view_resume.php" method="GET" class="col mt-2 px-0" target="_blank">
                                    <input type="hidden" name="resume" value="'.htmlspecialchars($loggedUser['resume']).'">
                                    <button type="submit" class="btn btn-outline-primary fw-medium px-5"><i class="bi bi-file-earmark-text me-2"></i>View</button>
                                </form>
                            </div>'
                            : 
                            '<div class="mb-5 ms-4">
                                <p class="text-secondary fw-medium">
                                    *No resume associated with your account. It is recommended to attach one before applying.<br/>
                                    <a class="fw-medium link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover" href="update_profile.php?email='.$_SESSION['logged_account']['email'].'">Update your profile</a> to upload your resume.
                                </p>
                            </div>';
                    ?>
                        
                    <form id="updateForm" class="form-floating needs-validation" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                        <h4>Personal Details</h4>
                        <div class="row row-cols-2 mb-4 mx-4">
                            <!-- First name -->
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control <?php echo (isset($errors['firstName'])) ? 'is-invalid' : ((isset($verified['firstName'])) ? 'is-valid' : ''); ?>" id="firstName" name="firstName" placeholder="John" maxlength="50" value="<?php echo isset($formData['firstName']) ? htmlspecialchars($formData['firstName']) : (isset($loggedUser['first_name']) ? htmlspecialchars($loggedUser['first_name']) : "");?>">
                                    <label for="firstName">First Name<strong class="text-danger">*</strong></label>
                                    <?php echo (isset($errors['firstName'])) ? '<div class="invalid-feedback">' . $errors['firstName'] . '</div>' : ''; ?>
                                </div>
                            </div>
                            <!-- Last name -->
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control <?php echo (isset($errors['lastName'])) ? 'is-invalid' : ((isset($verified['lastName'])) ? 'is-valid' : ''); ?>" id="lastName" name="lastName" placeholder="Doe" maxlength="50" value="<?php echo isset($formData['lastName']) ? htmlspecialchars($formData['lastName']) : (isset($loggedUser['last_name']) ? htmlspecialchars($loggedUser['last_name']) : ''); ?>">
                                    <label for="lastName">Last Name<strong class="text-danger">*</strong></label>
                                    <?php echo (isset($errors['lastName'])) ? '<div class="invalid-feedback">' . $errors['lastName'] . '</div>' : ''; ?>
                                </div>
                            </div>
                            <!-- Date of Birth -->
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="date" class="form-control <?php echo (isset($errors['dob'])) ? 'is-invalid' : ((isset($verified['dob'])) ? 'is-valid' : ''); ?>" id="dob" name="dob" value="<?php echo isset($formData['dob']) ? htmlspecialchars($formData['dob']) : (isset($loggedUser['dob']) ? htmlspecialchars($loggedUser['dob']) : ''); ?>">
                                    <label for="dob">Date of Birth<strong class="text-danger">*</strong></label>
                                    <?php echo (isset($errors['dob'])) ? '<div class="invalid-feedback">' . $errors['dob'] . '</div>' : ''; ?>
                                </div>
                            </div>
                            <!-- Gender -->
                            <div class="col">
                                <p class="mb-1">Gender<strong class="text-danger">*</strong></p>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input <?php echo (isset($errors['gender'])) ? 'is-invalid' : ((isset($verified['gender'])) ? 'is-valid' : ''); ?>" type="radio" name="gender" id="genderFemale" value="Female" checked <?php echo isset($formData['gender']) ? (($formData['gender'] == 'Female') ? 'checked' : '') : (isset($loggedUser['gender']) ? (($loggedUser['gender'] == 'Female') ? 'checked' : '') : ''); ?>>
                                    <label class="form-check-label" for="genderFemale">Female</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input <?php echo (isset($errors['gender'])) ? 'is-invalid' : ((isset($verified['gender'])) ? 'is-valid' : ''); ?>" type="radio" name="gender" id="genderMale" value="Male" <?php echo isset($formData['gender']) ? (($formData['gender'] == 'Male') ? 'checked' : '') : (isset($loggedUser['gender']) ? (($loggedUser['gender'] == 'Male') ? 'checked' : '') : ''); ?>>
                                    <label class="form-check-label" for="genderMale">Male</label>
                                </div>
                                <?php echo (isset($errors['gender'])) ? '<div class="invalid-feedback">' . $errors['gender'] . '</div>' : ''; ?>
                            </div>
                            <!-- Contact No. -->
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control <?php echo (isset($errors['contactNo'])) ? 'is-invalid' : ((isset($verified['contactNo'])) ? 'is-valid' : ''); ?>" id="contactNo" name="contactNo" placeholder="0123456789" maxlength="15" value="<?php echo isset($formData['contactNo']) ? htmlspecialchars($formData['contactNo']) : (isset($loggedUser['contact_number']) ? htmlspecialchars($loggedUser['contact_number']) : ''); ?>">
                                    <label for="contactNo">Contact No.</label>
                                    <?php echo (isset($errors['contactNo'])) ? '<div class="invalid-feedback">' . $errors['contactNo'] . '</div>' : ''; ?>
                                </div>
                            </div>
                            <!-- Email -->
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control <?php echo (isset($errors['email'])) ? 'is-invalid' : ((isset($verified['email'])) ? 'is-valid' : ''); ?>" id="email" name="email" placeholder="johndoe@email.com" maxlength="50" value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : (isset($loggedUser['email']) ? htmlspecialchars($loggedUser['email']) : ''); ?>">
                                    <label for="email">Email<strong class="text-danger">*</strong></label>
                                    <?php echo (isset($errors['email'])) ? '<div class="invalid-feedback">' . $errors['email'] . '</div>' : ''; ?>
                                </div>
                            </div>
                        </div>
                        
                        <h4>Address</h4>
                        <div class="row row-cols-2 mb-4 mx-4">
                            <!-- Hometown -->
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control <?php echo (isset($errors['hometown'])) ? 'is-invalid' : ((isset($verified['hometown'])) ? 'is-valid' : ''); ?>" id="hometown" name="hometown" placeholder="Kuching" value="<?php echo isset($formData['hometown']) ? htmlspecialchars($formData['hometown']) : (isset($loggedUser['hometown']) ? htmlspecialchars($loggedUser['hometown']) : ''); ?>">
                                    <label for="hometown">Hometown<strong class="text-danger">*</strong></label>
                                    <?php echo (isset($errors['hometown'])) ? '<div class="invalid-feedback">' . $errors['hometown'] . '</div>' : ''; ?>
                                </div>
                            </div>
                            <!-- Current Location -->
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control <?php echo (isset($errors['currentLocation'])) ? 'is-invalid' : ((isset($verified['currentLocation'])) ? 'is-valid' : ''); ?>" id="currentLocation" name="currentLocation" placeholder="Kuala Lumpur" maxlength="50" value="<?php echo isset($formData['currentLocation']) ? htmlspecialchars($formData['currentLocation']) : (isset($loggedUser['current_location']) ? htmlspecialchars($loggedUser['current_location']) : ''); ?>">
                                    <label for="currentLocation">Current Location</label>
                                    <?php echo (isset($errors['currentLocation'])) ? '<div class="invalid-feedback">' . $errors['currentLocation'] . '</div>' : ''; ?>
                                </div>
                            </div>
                        </div>

                        <h4>Education</h4>
                        <div class="row row-cols-2 mb-4 mx-4">
                            <!-- University -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control <?php echo (isset($errors['university'])) ? 'is-invalid' : ((isset($verified['university'])) ? 'is-valid' : ''); ?>" id="university" name="university" placeholder="Kuching" maxlength="50" value="<?php echo isset($formData['university']) ? htmlspecialchars($formData['university']) : (isset($loggedUser['university']) ? htmlspecialchars($loggedUser['university']) : ''); ?>">
                                    <label for="university">University / Campus</label>
                                    <?php echo (isset($errors['university'])) ? '<div class="invalid-feedback">' . $errors['university'] . '</div>' : ''; ?>
                                </div>
                            </div>
                            <!-- Degree -->
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control <?php echo (isset($errors['degreeProgram'])) ? 'is-invalid' : ((isset($verified['degreeProgram'])) ? 'is-valid' : ''); ?>" id="degreeProgram" name="degreeProgram" placeholder="Kuching" maxlength="70" value="<?php echo isset($formData['degreeProgram']) ? htmlspecialchars($formData['degreeProgram']) : (isset($loggedUser['qualification']) ? htmlspecialchars($loggedUser['qualification']) : ''); ?>">
                                    <label for="degreeProgram">Degree Program</label>
                                    <?php echo (isset($errors['degreeProgram'])) ? '<div class="invalid-feedback">' . $errors['degreeProgram'] . '</div>' : ''; ?>
                                </div>
                            </div>
                            <!-- Year Graduated -->
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control <?php echo (isset($errors['yearGraduated'])) ? 'is-invalid' : ((isset($verified['yearGraduated'])) ? 'is-valid' : ''); ?>" id="yearGraduated" name="yearGraduated" placeholder="0" max="9999" value="<?php echo isset($formData['yearGraduated']) ? htmlspecialchars($formData['yearGraduated']) : (isset($loggedUser['year']) ? htmlspecialchars($loggedUser['year']) : ''); ?>">
                                    <label for="yearGraduated">Year Graduated</label>
                                    <?php echo (isset($errors['yearGraduated'])) ? '<div class="invalid-feedback">' . $errors['yearGraduated'] . '</div>' : ''; ?>
                                </div>
                            </div>
                        </div>

                        <h4>Current Job</h4>
                        <div class="row row-cols-2 mb-4 mx-4">
                            <!-- Job position -->
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control <?php echo (isset($errors['jobPosition'])) ? 'is-invalid' : ((isset($verified['jobPosition'])) ? 'is-valid' : ''); ?>" id="jobPosition" name="jobPosition" placeholder="Kuching" maxlength="50" value="<?php echo isset($formData['jobPosition']) ? htmlspecialchars($formData['jobPosition']) : (isset($loggedUser['job_position']) ? htmlspecialchars($loggedUser['job_position']) : ''); ?>">
                                    <label for="jobPosition">Job Position</label>
                                    <?php echo (isset($errors['jobPosition'])) ? '<div class="invalid-feedback">' . $errors['jobPosition'] . '</div>' : ''; ?>
                                </div>
                            </div>
                            <!-- Comapany -->
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control <?php echo (isset($errors['company'])) ? 'is-invalid' : ((isset($verified['company'])) ? 'is-valid' : ''); ?>" id="company" name="company" placeholder="Kuching" maxlength="50" value="<?php echo isset($formData['company']) ? htmlspecialchars($formData['company']) : (isset($loggedUser['company']) ? htmlspecialchars($loggedUser['company']) : ''); ?>">
                                    <label for="company">Company</label>
                                    <?php echo (isset($errors['company'])) ? '<div class="invalid-feedback">' . $errors['company'] . '</div>' : ''; ?>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="resume" value="<?php echo htmlspecialchars($loggedUser['resume']); ?>">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">

                        <!-- Apply / Cancel button -->
                        <div class="row justify-content-end align-items-center">
                            <div class="col"><span class="text-secondary fst-italic"><strong class="text-danger">*</strong>Indicates required field</span></div>
                            <div class="col-auto">
                                <a role="button" href="view_advertisements.php" class="btn btn-outline-secondary px-5">Cancel</a>
                                <button type="submit" class="btn btn-primary ms-3 me-4 px-5 fw-medium">Apply</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Boostrap JS --><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- Axios --><script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Disables/Enables Resume upload button
        const resumeInput = document.getElementById('profileResumeInput');
        const submitResumeButton = document.getElementById('submitResumeButton');

        resumeInput.addEventListener('change', function () {
            if (resumeInput.files.length > 0)
                submitResumeButton.removeAttribute('disabled');
            else
                submitResumeButton.setAttribute('disabled', 'disabled');
        });
    </script>
</body>
</html>