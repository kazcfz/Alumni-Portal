<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Edit Event</title>

    <link rel="stylesheet" href="css/styles.css">

    <!-- Bootstrap CSS --><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons --><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Animate.css --><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- DataTables Bootstrap 5 --><link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
    <style>
        .card {
            width: 100%;
            border: none;
            box-shadow: 0 2px 2px rgba(0,0,0,.08), 0 0 6px rgba(0,0,0,.05);
        }

        .title {
            font-size: 1.75rem;
            /* font-weight: bold; */
        }

        .disabled {
            pointer-events: all !important;
            cursor: default;
        }
    </style>
</head>
<body class="admin-bg">
    <?php
        include 'db_controller.php';
        $conn->select_db("Alumni");

        session_start();

        include 'logged_admin.php';

        // GET request (default)
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $SQLEditEvent = $conn->prepare("SELECT * FROM event_table WHERE id = ?");
            $SQLEditEvent->bind_param("s", $_GET['id']);
            $SQLEditEvent->execute();
            $eventDetails = $SQLEditEvent->get_result()->fetch_assoc();
        }

        // POST request
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            unset($errors);
            foreach ($_POST as $key => $value) {
                $_POST[$key] = trim($_POST[$key]);
                $value = trim($value);

                // check for empty fields
                if ($key == 'date' && $value == '')
                    $errors[$key] = '*Date is required.';
                elseif ($key == 'date')
                    $verified[$key] = true;

                if ($key == 'type' && $value == '')
                    $errors[$key] = '*Type is required.';
                elseif ($key == 'type')
                    $verified[$key] = true;

                if ($key == 'title' && $value == '')
                    $errors[$key] = '*Title is required.';
                elseif ($key == 'title' && strlen($value) >= 100)
                    $errors[$key] = '*Title must be less than 100 characters.';
                elseif ($key == 'title')
                    $verified[$key] = true;

                if ($key == 'location' && $value == '')
                    $errors[$key] = '*Location is required.';
                elseif ($key == 'location' && strlen($value) >= 50)
                    $errors[$key] = '*Location must be less than 50 characters.';
                elseif ($key == 'location')
                    $verified[$key] = true;

                if ($key == 'description' && $value == '')
                    $errors[$key] = '*Description is required.';
                elseif ($key == 'description' && strlen($value) >= 700)
                    $errors[$key] = '*Description must be less than 700 characters.';
                elseif ($key == 'description')
                    $verified[$key] = true;
            }
            if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
                $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                if (!in_array($fileExtension, ["jpg", "png"]))
                    $errors['image'] = '*Photo must be of type jpg or png.';
            }
            isset($_POST['image']) ? $eventDetails["photo"] = $_POST['image'] : NULL;
            $eventDetails['id'] = $_POST['id'];
            $eventDetails['title'] = $_POST['title'];
            $eventDetails['event_date'] = $_POST['date'];
            $eventDetails['location'] = $_POST['location'];
            $eventDetails['description'] = $_POST['description'];
            $eventDetails['type'] = $_POST['type'];

            // If all validations are checked and cleared
            if (empty($errors)) {
                $SQLGetOri = $conn->prepare("SELECT * FROM event_table WHERE id = ?");
                $SQLGetOri->bind_param("s", $_POST['id']);
                $SQLGetOri->execute();
                $original = $SQLGetOri->get_result()->fetch_assoc();

                if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0){
                } else {
                    if ($_POST['type'] == "Event" && ($original['photo'] == "default_news.png" || $original['photo'] == "default_events.jpg"))
                        $_FILES["image"]["name"] = "default_events.jpg";
                    elseif ($_POST['type'] == "News" && ($original['photo'] == "default_news.png" || $original['photo'] == "default_events.jpg"))
                        $_FILES["image"]["name"] = "default_news.png";
                    else
                        $_FILES["image"]["name"] = $original['photo'];
                }

                try {
                    // Edit event/news on DB
                    $SQLUpdateEvent = $conn->prepare("UPDATE event_table SET 
                        title = ?,
                        location = ?,
                        description = ?,
                        event_date = ?,
                        photo = ?,
                        type = ?
                        WHERE id = ?
                    ");
                    $SQLUpdateEvent->bind_param("sssssss", $_POST['title'],$_POST['location'],$_POST['description'],$_POST['date'],$_FILES["image"]["name"],$_POST['type'],$_POST['id']);
                    if($SQLUpdateEvent->execute()) {
                        $insertedID = $_POST['id'];

                        // If type has changed, rename photo to match type
                        if ($original['type'] != $_POST['type']) {
                            $uploadDir = "images/";
                            foreach (['jpg', 'png'] as $extension) {
                                $originalPhotoPath = $uploadDir.$original['type'].$original['id'].".".$extension;
                                $newPhotoPath = $uploadDir.$_POST['type'].$original['id'].".".$extension;
                                $newPhoto = $_POST['type'].$original['id'].".".$extension;

                                if (file_exists($originalPhotoPath)) {
                                    rename($originalPhotoPath, $newPhotoPath);
                                    $conn->query("UPDATE event_table SET photo = '$newPhoto' WHERE id = '$insertedID'");
                                    break;
                                }
                            }
                        }

                        // If it's just the image, process it only
                        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
                            $uploadDir = "images/";
                            $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                            $fileName = $_POST['type'].$insertedID.".".$fileExtension;

                            // Remove other extension photo if exists
                            foreach (['jpg', 'png'] as $extension) {
                                $oldPhoto = $uploadDir.$_POST['type'].$original['id'].".".$extension;
                                if (file_exists($oldPhoto))
                                    unlink($oldPhoto);
                            }
                            
                            // Add new photo to storage
                            if (in_array($fileExtension, ["jpg", "png"])) // Ensure file type is jpg, png
                                move_uploaded_file($_FILES["image"]["tmp_name"], $uploadDir . $fileName);
                            
                            // Update db with new image path
                            $conn->query("UPDATE event_table SET photo = '$fileName' WHERE id = '$insertedID'");
                        }

                        // Set flash message and redirect back
                        if ($SQLUpdateEvent->affected_rows > 0) {
                            $_SESSION['flash_mode'] = "alert-success";
                            $_SESSION['flash'] = "<span class='fw-medium'>".$_POST['type']." ".$_POST['id']."</span> updated successfully.";
                        } elseif ($SQLUpdateEvent->affected_rows == 0) {
                            $_SESSION['flash_mode'] = "alert-secondary";
                            $_SESSION['flash'] = "No changes made to <span class='fw-medium'>".$_POST['type']." ".$_POST['id']."</span>.";
                        }

                        header('Location: manage_events.php');
                    } else {
                        $_SESSION['flash_mode'] = "alert-warning";
                        $_SESSION['flash'] = "An error has occured updating <span class='fw-medium'>".$_POST['type']." ".$_POST['id']."</span>";
                    }
                } catch (Exception $e) {
                    $_SESSION['flash_mode'] = "alert-warning";
                    $_SESSION['flash'] = "An error has occured updating <span class='fw-medium'>Advertisement ".$_POST['id']."</span>.";
                    header('Location: manage_advertisements.php');
                }
            } else {
                $_SESSION['form_data'] = $_POST;
                $_SESSION['errors'] = $errors;
                $_SESSION['verified'] = $verified;
            }
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

    <nav class="navbar sticky-top navbar-expand-lg" style="background-color: #002c59;">
        <div class="container">
            <a class="navbar-brand mx-0 mb-0 h1 text-light" href="main_menu_admin.php">Alumni Portal</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse me-5" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto fake-active">
                    <li class="nav-item mx-1">
                        <a class="nav-link nav-admin-link px-5" href="main_menu_admin.php"><i class="bi bi-house-door nav-bi-admin"></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link nav-admin-link px-5" href="manage_accounts.php"><i class="bi bi-people nav-bi-admin"></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link nav-admin-link px-5" href="manage_events.php"><i class="bi bi-calendar-event nav-bi-admin"></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link nav-admin-link px-5" href="manage_advertisements.php"><i class="bi bi-megaphone nav-bi-admin"></i></a>
                    </li>
                </ul>
            </div>
            <?php include 'nav_user.php' ?>
        </div>
    </nav>

    <div class="container my-3">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="breadcrumb-link text-secondary link-underline link-underline-opacity-0" href="main_menu_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a class="breadcrumb-link text-secondary link-underline link-underline-opacity-0" href="manage_events.php">Manage Events/News</a></li>
                <li class="breadcrumb-item breadcrumb-active" aria-current="page">Edit Events/News</li>
            </ol>
        </nav>
    </div>

    <div class="container <?php echo ($_SERVER["REQUEST_METHOD"] == "POST") ? NULL : 'slide-left' ?>">
        <div class="row">
            <div class="col"><h1>Edit <?php echo (isset($eventDetails['type'])) ? $eventDetails['type'] : NULL ?></h1></div>
        </div>

        <form class="needs-validation" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="multipart/form-data">
            <div class="col mb-4 px-0 mx-0">
                <div class="card">
                    <!-- Event/News adding area -->
                    <div class="row mb-5">
                        <!-- Photo -->
                        <div class="col-auto pe-0 me-0">
                            <div class="image-container-events"><img src="images/<?php echo isset($eventDetails["photo"]) ? $eventDetails["photo"] : "";?>" class="img-fluid profilePictureThumbnail" id="previewImage" alt="profile_picture"></div>
                            <div class="col-10 ms-2 <?php echo (isset($errors['image']) && !empty($errors['image'])) ? 'animate__animated animate__headShake animate__fast' : NULL ?>">
                                <input type="file" class="form-control my-2 pe-0 <?php echo (isset($errors['image'])) ? 'is-invalid' : ((isset($verified['image'])) ? 'is-valid' : ''); ?>" id="imageInput" name="image" accept=".jpg, .png"/>
                                <?php echo (isset($errors['image'])) ? '<div class="invalid-feedback">'.$errors['image'].'</div>' : NULL; ?>
                            </div>
                        </div>

                        <div class="col d-flex flex-column ps-0 ms-0 <?php echo (isset($errors) && !empty($errors)) ? 'animate__animated animate__headShake animate__fast' : NULL ?>">
                            <div class="card-body px-0 flex-grow-1 me-4">
                                <div class="row justify-content-between mb-1">
                                    <!-- Date -->
                                    <div class="col-auto">
                                        <div class="row">
                                            <div class="col-auto pe-0">
                                                <input type="date" class="form-control fw-medium py-0 <?php echo (isset($errors['date'])) ? 'is-invalid' : ((isset($verified['date'])) ? 'is-valid' : ''); ?>" id="date" name="date" value="<?php echo isset($formData["date"]) ? htmlspecialchars($formData["date"]) : (isset($eventDetails["event_date"]) ? $eventDetails["event_date"] : "");?>">
                                                <?php echo (isset($errors['date'])) ? '<div class="invalid-feedback">'.$errors['date'].'</div>' : NULL; ?>
                                            </div>
                                            <div class="col-auto ps-1">*</div>
                                        </div>
                                    </div>
                                    <!-- Type -->
                                    <div class="col-auto">
                                        <select class="badge text-bg-success <?php echo (isset($errors['type'])) ? 'is-invalid' : ((isset($verified['type'])) ? 'is-valid' : ''); ?>" id="type" name="type" style="border: none;" onchange="changeType(this)">
                                            <option value="Event" class="fw-medium text-black bg-white" <?php echo (isset($formData['type']) && $formData['type'] == 'Event') ? 'selected' : (isset($eventDetails["type"]) && $eventDetails["type"] == 'Event' ? 'selected' : ""); ?>>Events</option>
                                            <option value="News" class="fw-medium text-black bg-white" <?php echo (isset($formData['type']) && $formData['type'] == 'News') ? 'selected' : (isset($eventDetails["type"]) && $eventDetails["type"] == 'News' ? 'selected' : ""); ?>>News</option>
                                        </select>
                                        <?php echo (isset($errors['type'])) ? '<div class="invalid-feedback">'.$errors['type'].'</div>' : NULL; ?>
                                    </div>
                                </div>
                                <!-- Title -->
                                <div class="mb-1 col-auto">
                                    <input type="text" class="form-control title fw-medium py-0 <?php echo (isset($errors['title'])) ? 'is-invalid' : ((isset($verified['title'])) ? 'is-valid' : ''); ?>" id="title" name="title" placeholder="Title*" value="<?php echo isset($formData["title"]) ? htmlspecialchars($formData["title"]) : (isset($eventDetails["title"]) ? $eventDetails["title"] : "");?>">
                                    <?php echo (isset($errors['title'])) ? '<div class="invalid-feedback">'.$errors['title'].'</div>' : NULL; ?>
                                </div>
                                <!-- Location -->
                                <div class="mb-4 col-auto">
                                    <input type="text" class="form-control fw-medium text-secondary py-0 <?php echo (isset($errors['location'])) ? 'is-invalid' : ((isset($verified['location'])) ? 'is-valid' : ''); ?>" id="location" name="location" placeholder="Location*" value="<?php echo isset($formData["location"]) ? htmlspecialchars($formData["location"]) : (isset($eventDetails["location"]) ? $eventDetails["location"] : "");?>">
                                    <?php echo (isset($errors['location'])) ? '<div class="invalid-feedback">'.$errors['location'].'</div>' : NULL; ?>
                                </div>
                                <!-- Description -->
                                <div class="mb-1 col-auto">
                                    <textarea rows="4" type="text" class="form-control py-0 <?php echo (isset($errors['description'])) ? 'is-invalid' : ((isset($verified['description'])) ? 'is-valid' : ''); ?>" id="description" name="description" placeholder="Description*"><?php echo isset($formData["description"]) ? htmlspecialchars($formData["description"]) : (isset($eventDetails["description"]) ? $eventDetails["description"] : "");?></textarea>
                                    <?php echo (isset($errors['description'])) ? '<div class="invalid-feedback">'.$errors['description'].'</div>' : NULL; ?>
                                </div>
                            </div>
                            <!-- Preview sign up button for Events -->
                            <div class="mb-4">
                                <button class='btn btn-primary fw-medium px-4 disabled' id="signupButton" disabled data-bs-toggle="tooltip" data-bs-title="This is just a preview button">Sign Up</button>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($eventDetails['id']); ?>">
                    <input type="hidden" name="image" value="<?php echo htmlspecialchars($eventDetails['photo']); ?>">

                    <!-- Footer? -->
                    <div class="row justify-content-end align-items-center mb-4">
                        <div class="col ms-2"><span class="text-secondary fst-italic"><strong class="text-black">*</strong>Indicates required field</span></div>
                        <div class="col-auto">
                            <a role="button" href="manage_events.php" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary ms-3 me-4 px-4 fw-medium"><i class="bi bi-arrow-counterclockwise me-2" style="-webkit-text-stroke: 0.25px;"></i>Update <?php echo (isset($eventDetails['type'])) ? $eventDetails['type'] : NULL ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
    
    <!-- Boostrap JS --><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- jQuery --><script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- DataTables --><script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
    <!-- DataTables Bootstrap 5 --><script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Moment.js --><script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

        var selectOption = document.getElementById("type");
        var signupButton = document.getElementById("signupButton");
        var image = document.getElementById("previewImage");
        var filename = image.src.substring(image.src.lastIndexOf('/') + 1);
        var imageInput1 = document.getElementById("imageInput");

        // Change Events/News type badges
        function changeType(selectElement) {
            var selectedOption = selectElement.options[selectElement.selectedIndex].value;

            // Remove previous class
            selectElement.classList.remove("text-bg-success");
            selectElement.classList.remove("text-bg-warning");

            // Add appropriate class + transition button
            if (selectedOption == "Event") {
                if (imageInput1.files.length <= 0)
                    if (filename == "default_events.jpg" || filename == "default_news.png")
                        image.src = "images/default_events.jpg";

                selectElement.classList.add("text-bg-success");
                signupButton.style.display = "block";
                signupButton.classList.add("fade-in");
                setTimeout(() => {
                    signupButton.classList.remove("fade-in");
                }, 300);
            } else if (selectedOption == "News") {
                if (imageInput1.files.length <= 0)
                    if (filename == "default_events.jpg" || filename == "default_news.png")
                        image.src = "images/default_news.png";

                selectElement.classList.add("text-bg-warning");
                signupButton.classList.add("fade-out");
                setTimeout(() => {
                    signupButton.style.display = "none";
                    signupButton.classList.remove("fade-out");
                }, 300);
            }
        }

        // Image preview when selected
        const imageInput = document.getElementById("imageInput");
        const previewImage = document.getElementById("previewImage");

        imageInput.addEventListener("change", function () {
        if (imageInput.files && imageInput.files[0]) {
            const reader = new FileReader();
            // update img
            reader.onload = function (e) {
                previewImage.src = e.target.result;
            };
            reader.readAsDataURL(imageInput.files[0]); // read image as data URL
        }
        });

        window.onload = (event) => {
            // Remove previous class
            selectOption.classList.remove("text-bg-success");
            selectOption.classList.remove("text-bg-warning");

            // Add Bootstrap styling class based on selected option
            if (selectOption.value == "Event") {
                selectOption.classList.add("text-bg-success");
                signupButton.style.display = "block";
            } else if (selectOption.value == "News") {
                selectOption.classList.add("text-bg-warning");
                signupButton.style.display = "none";
            }
        }
    </script>
</body>
</html>