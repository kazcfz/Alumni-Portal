<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Update Profile</title>

    <link rel="stylesheet" href="css/styles.css">

    <!-- Bootstrap CSS --><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons --><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .card {
            width: 23rem;
            border: none;
            box-shadow: 0 4px 4px rgba(0,0,0,.2);
        }

        .card-btn {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
</head>
<body>
    <?php
        include 'db_controller.php';
        $conn->select_db("Alumni");

        session_start();

        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        include 'logged_user.php';
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

    <?php include 'get_alumnus_by_email.php'; ?>

    <!-- Breadcrumb -->
    <div class="container my-3">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="breadcrumb-link text-secondary link-underline link-underline-opacity-0" href="main_menu.php">Home</a></li>
                <li class="breadcrumb-item breadcrumb-active" aria-current="page">Update Profile</li>
            </ol>
        </nav>
    </div>

    <?php
        if (isset($alumnusToView) && $alumnusToView == $loggedUser){ //makes sure the alumnnus to update is the one logged in 
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
    ?>
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

            <div class="container py-4 px-4 mb-5 mainView bg-white ">
                <div class="row slide-left">
                    <div class="col-auto mx-3">
                        <!-- Display Profile photo -->
                        <?php
                            if (isset($alumnusToViewProfilePicture) && $alumnusToViewProfilePicture != "")
                                echo '<div class="image-container"><img src="profile_images/'.$alumnusToViewProfilePicture.'" class="img-fluid profilePicture ms-1" width="250" height="250" alt="profile_picture"></div>';
                            elseif ($alumnusToViewGender == "Male")
                                echo '<div class="image-container"><img src="profile_images/default-male-user-profile-icon.jpg" class="img-fluid profilePicture ms-1" width="250" height="250" alt="profile_picture"></div>';
                            elseif ($alumnusToViewGender == "Female")
                                echo '<div class="image-container"><img src="profile_images/default-female-user-profile-icon.jpg" class="img-fluid profilePicture ms-1" width="250" height="250" alt="profile_picture"></div>';
                        ?>

                        <!-- Upload photo -->
                        <form class="col-9 mt-3" action="<?php echo htmlspecialchars('process_update.php');?>" method="post" enctype="multipart/form-data">
                            <legend class="h4">Profile Photo</legend>
                            <input type="file" class="form-control mb-2 ms-4" id="profileImageInput" name="profileImage" accept=".jpg, .jpeg, .png" />
                            <button type="submit" class="w-100 btn btn-primary fw-medium ms-4" name="profileImage" id="submitImageButton" disabled><i class="bi bi-plus-lg me-2"></i>Upload Photo</button>
                        </form>
                        <!-- Additional function to view or delete photo -->
                        <?php 
                            echo (isset($alumnusToViewProfilePicture) && $alumnusToViewProfilePicture != '') ? 
                                '<div class="row justify-content-start px-0 mb-5">
                                    <div class="col-auto mt-2 pe-2">
                                        <button onclick="deleteProfilePicture()" class="btn btn-outline-danger ms-4"><i class="bi bi-trash me-2"></i>Delete</button>
                                    </div>
                                    <div class="col mt-2 px-0">
                                        <a href="profile_images/'.$alumnusToViewProfilePicture.'" target="_blank" class="btn btn-outline-primary fw-medium px-5"><i class="bi bi-image me-2"></i>View</a>
                                    </div>
                                </div>'
                                : '';
                        ?>

                        <br/>

                        <!-- Upload resume -->
                        <form class="col-9 mt-5" action="<?php echo htmlspecialchars('process_update.php');?>" method="post" enctype="multipart/form-data">
                            <legend class="h4">Resume</legend>
                            <input type="file" class="form-control mb-2 ms-4" id="profileResumeInput" name="resume" accept=".pdf" />
                            <button type="submit" class="w-100 btn btn-primary fw-medium ms-4" name="resume" id="submitResumeButton" disabled><i class="bi bi-plus-lg me-2"></i>Upload Resume</button>
                        </form>
                        <!-- Additional function to view or delete resume -->
                        <?php 
                            echo (isset($alumnusToViewResume) && $alumnusToViewResume != '') ? 
                                '<div class="row justify-content-start px-0 mb-5">
                                    <div class="col-auto mt-2 pe-2">
                                        <button onclick="deleteResume()" class="btn btn-outline-danger ms-4"><i class="bi bi-trash me-2"></i>Delete</button>
                                    </div>
                                    <form action="view_resume.php" method="GET" class="col mt-2 px-0" target="_blank">
                                        <input type="hidden" name="resume" value="'.htmlspecialchars($alumnusToViewResume).'">
                                        <button type="submit" class="btn btn-outline-primary fw-medium px-5"><i class="bi bi-file-earmark-text me-2"></i>View</button>
                                    </form>
                                </div>'
                                : '';
                        ?>
                    </div>

                    <div class="col">
                        <form id="updateForm" class="form-floating needs-validation" action="<?php echo htmlspecialchars('process_update.php');?>" method="POST">
                            <h4>Personal Details</h4>
                            <div class="row row-cols-2 mb-4 mx-4">
                                <!-- First name -->
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control <?php echo (isset($errors['firstName'])) ? 'is-invalid' : ((isset($verified['firstName'])) ? 'is-valid' : ''); ?>" id="firstName" name="firstName" placeholder="John" maxlength="50" value="<?php echo isset($formData['firstName']) ? htmlspecialchars($formData['firstName']) : (isset($alumnusToViewFirstName) ? htmlspecialchars($alumnusToViewFirstName) : "");?>">
                                        <label for="firstName">First Name<strong class="text-danger">*</strong></label>
                                        <?php echo (isset($errors['firstName'])) ? '<div class="invalid-feedback">' . $errors['firstName'] . '</div>' : ''; ?>
                                    </div>
                                </div>
                                <!-- Last name -->
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control <?php echo (isset($errors['lastName'])) ? 'is-invalid' : ((isset($verified['lastName'])) ? 'is-valid' : ''); ?>" id="lastName" name="lastName" placeholder="Doe" maxlength="50" value="<?php echo isset($formData['lastName']) ? htmlspecialchars($formData['lastName']) : (isset($alumnusToViewLastName) ? htmlspecialchars($alumnusToViewLastName) : ''); ?>">
                                        <label for="lastName">Last Name<strong class="text-danger">*</strong></label>
                                        <?php echo (isset($errors['lastName'])) ? '<div class="invalid-feedback">' . $errors['lastName'] . '</div>' : ''; ?>
                                    </div>
                                </div>
                                <!-- Date of Birth -->
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control <?php echo (isset($errors['dob'])) ? 'is-invalid' : ((isset($verified['dob'])) ? 'is-valid' : ''); ?>" id="dob" name="dob" value="<?php echo isset($formData['dob']) ? htmlspecialchars($formData['dob']) : (isset($alumnusToViewDOB) ? htmlspecialchars($alumnusToViewDOB) : ''); ?>">
                                        <label for="dob">Date of Birth<strong class="text-danger">*</strong></label>
                                        <?php echo (isset($errors['dob'])) ? '<div class="invalid-feedback">' . $errors['dob'] . '</div>' : ''; ?>
                                    </div>
                                </div>
                                <!-- Gender -->
                                <div class="col">
                                    <p class="mb-1">Gender<strong class="text-danger">*</strong></p>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input <?php echo (isset($errors['gender'])) ? 'is-invalid' : ((isset($verified['gender'])) ? 'is-valid' : ''); ?>" type="radio" name="gender" id="genderFemale" value="Female" checked <?php echo isset($formData['gender']) ? (($formData['gender'] == 'Female') ? 'checked' : '') : (isset($alumnusToViewGender) ? (($alumnusToViewGender == 'Female') ? 'checked' : '') : ''); ?>>
                                        <label class="form-check-label" for="genderFemale">Female</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input <?php echo (isset($errors['gender'])) ? 'is-invalid' : ((isset($verified['gender'])) ? 'is-valid' : ''); ?>" type="radio" name="gender" id="genderMale" value="Male" <?php echo isset($formData['gender']) ? (($formData['gender'] == 'Male') ? 'checked' : '') : (isset($alumnusToViewGender) ? (($alumnusToViewGender == 'Male') ? 'checked' : '') : ''); ?>>
                                        <label class="form-check-label" for="genderMale">Male</label>
                                    </div>
                                    <?php echo (isset($errors['gender'])) ? '<div class="invalid-feedback">' . $errors['gender'] . '</div>' : ''; ?>
                                </div>
                                <!-- Contact No. -->
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control <?php echo (isset($errors['contactNo'])) ? 'is-invalid' : ((isset($verified['contactNo'])) ? 'is-valid' : ''); ?>" id="contactNo" name="contactNo" placeholder="0123456789" maxlength="15" value="<?php echo isset($formData['contactNo']) ? htmlspecialchars($formData['contactNo']) : (isset($alumnusToViewContactNo) ? htmlspecialchars($alumnusToViewContactNo) : ''); ?>">
                                        <label for="contactNo">Contact No.</label>
                                        <?php echo (isset($errors['contactNo'])) ? '<div class="invalid-feedback">' . $errors['contactNo'] . '</div>' : ''; ?>
                                    </div>
                                </div>
                                <!-- Email -->
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control <?php echo (isset($errors['email'])) ? 'is-invalid' : ((isset($verified['email'])) ? 'is-valid' : ''); ?>" id="email" name="email" placeholder="johndoe@email.com" maxlength="50" value="<?php echo isset($formData['email']) ? htmlspecialchars($formData['email']) : (isset($alumnusToViewEmail) ? htmlspecialchars($alumnusToViewEmail) : ''); ?>">
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
                                        <input type="text" class="form-control <?php echo (isset($errors['hometown'])) ? 'is-invalid' : ((isset($verified['hometown'])) ? 'is-valid' : ''); ?>" id="hometown" name="hometown" placeholder="Kuching" value="<?php echo isset($formData['hometown']) ? htmlspecialchars($formData['hometown']) : (isset($alumnusToViewHometown) ? htmlspecialchars($alumnusToViewHometown) : ''); ?>">
                                        <label for="hometown">Hometown<strong class="text-danger">*</strong></label>
                                        <?php echo (isset($errors['hometown'])) ? '<div class="invalid-feedback">' . $errors['hometown'] . '</div>' : ''; ?>
                                    </div>
                                </div>
                                <!-- Current Location -->
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control <?php echo (isset($errors['currentLocation'])) ? 'is-invalid' : ((isset($verified['currentLocation'])) ? 'is-valid' : ''); ?>" id="currentLocation" name="currentLocation" placeholder="Kuala Lumpur" maxlength="50" value="<?php echo isset($formData['currentLocation']) ? htmlspecialchars($formData['currentLocation']) : (isset($alumnusToViewCurrentLocation) ? htmlspecialchars($alumnusToViewCurrentLocation) : ''); ?>">
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
                                        <input type="text" class="form-control <?php echo (isset($errors['university'])) ? 'is-invalid' : ((isset($verified['university'])) ? 'is-valid' : ''); ?>" id="university" name="university" placeholder="Kuching" maxlength="50" value="<?php echo isset($formData['university']) ? htmlspecialchars($formData['university']) : (isset($alumnusToViewCampus) ? htmlspecialchars($alumnusToViewCampus) : ''); ?>">
                                        <label for="university">University / Campus</label>
                                        <?php echo (isset($errors['university'])) ? '<div class="invalid-feedback">' . $errors['university'] . '</div>' : ''; ?>
                                    </div>
                                </div>
                                <!-- Degree -->
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control <?php echo (isset($errors['degreeProgram'])) ? 'is-invalid' : ((isset($verified['degreeProgram'])) ? 'is-valid' : ''); ?>" id="degreeProgram" name="degreeProgram" placeholder="Kuching" maxlength="70" value="<?php echo isset($formData['degreeProgram']) ? htmlspecialchars($formData['degreeProgram']) : (isset($alumnusToViewDegree) ? htmlspecialchars($alumnusToViewDegree) : ''); ?>">
                                        <label for="degreeProgram">Degree Program</label>
                                        <?php echo (isset($errors['degreeProgram'])) ? '<div class="invalid-feedback">' . $errors['degreeProgram'] . '</div>' : ''; ?>
                                    </div>
                                </div>
                                <!-- Year Graduated -->
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control <?php echo (isset($errors['yearGraduated'])) ? 'is-invalid' : ((isset($verified['yearGraduated'])) ? 'is-valid' : ''); ?>" id="yearGraduated" name="yearGraduated" placeholder="0" max="9999" value="<?php echo isset($formData['yearGraduated']) ? htmlspecialchars($formData['yearGraduated']) : (isset($alumnusToViewDegreeYearGraduated) ? htmlspecialchars($alumnusToViewDegreeYearGraduated) : ''); ?>">
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
                                        <input type="text" class="form-control <?php echo (isset($errors['jobPosition'])) ? 'is-invalid' : ((isset($verified['jobPosition'])) ? 'is-valid' : ''); ?>" id="jobPosition" name="jobPosition" placeholder="Kuching" maxlength="50" value="<?php echo isset($formData['jobPosition']) ? htmlspecialchars($formData['jobPosition']) : (isset($alumnusToViewJobPosition) ? htmlspecialchars($alumnusToViewJobPosition) : ''); ?>">
                                        <label for="jobPosition">Job Position</label>
                                        <?php echo (isset($errors['jobPosition'])) ? '<div class="invalid-feedback">' . $errors['jobPosition'] . '</div>' : ''; ?>
                                    </div>
                                </div>
                                <!-- Comapany -->
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control <?php echo (isset($errors['company'])) ? 'is-invalid' : ((isset($verified['company'])) ? 'is-valid' : ''); ?>" id="company" name="company" placeholder="Kuching" maxlength="50" value="<?php echo isset($formData['company']) ? htmlspecialchars($formData['company']) : (isset($alumnusToViewCompany) ? htmlspecialchars($alumnusToViewCompany) : ''); ?>">
                                        <label for="company">Company</label>
                                        <?php echo (isset($errors['company'])) ? '<div class="invalid-feedback">' . $errors['company'] . '</div>' : ''; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Update / Cancel button -->
                            <div class="row justify-content-end align-items-center">
                                <div class="col"><span class="text-secondary fst-italic"><strong class="text-danger">*</strong>Indicates required field</span></div>
                                <div class="col-auto">
                                    <a role="button" href="main_menu.php" class="btn btn-outline-secondary px-5">Cancel</a>
                                    <button type="submit" class="btn btn-primary ms-3 me-4 px-5 fw-medium">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php } else {echo "<div class='container slide-left'><h3>You can only update your own profile.</h3></div>";} ?>
    
    <!-- Boostrap JS --><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- Axios --><script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Disables/Enables Image upload button
        const imageInput = document.getElementById('profileImageInput');
        const submitImageButton = document.getElementById('submitImageButton');

        imageInput.addEventListener('change', function () {
            if (imageInput.files.length > 0)
                submitImageButton.removeAttribute('disabled');
            else
                submitImageButton.setAttribute('disabled', 'disabled');
        });

        // Disables/Enables Resume upload button
        const resumeInput = document.getElementById('profileResumeInput');
        const submitResumeButton = document.getElementById('submitResumeButton');

        resumeInput.addEventListener('change', function () {
            if (resumeInput.files.length > 0)
                submitResumeButton.removeAttribute('disabled');
            else
                submitResumeButton.setAttribute('disabled', 'disabled');
        });

        // Axios call to remove alumnus' resume from storage and DB
        function deleteResume() {
            const alumnusToViewResume = "<?php echo $alumnusToViewResume ?>";
            axios.delete(`process_update.php`, {
                data: {
                    alumnusToViewResume: alumnusToViewResume,
                }
            })
            .then(response => {
                window.location.href = 'update_profile.php?email=<?php echo $_SESSION['logged_account']['email']; ?>';
            });
        }

        // Axios call to remove alumnus' profile picture from storage and DB
        function deleteProfilePicture() {
            const alumnusToViewProfilePicture = "<?php echo $alumnusToViewProfilePicture ?>";
            axios.delete(`process_update.php`, {
                data: {
                    alumnusToViewProfilePicture: alumnusToViewProfilePicture,
                }
            })
            .then(response => {
                window.location.href = 'update_profile.php?email=<?php echo $_SESSION['logged_account']['email']; ?>';
            });
        }
    </script>
</body>
</html>