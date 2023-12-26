<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Edit Advertisement</title>

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
            if (!isset($_GET['id'])) {
                $_SESSION['flash_mode'] = "alert-secondary";
                $_SESSION['flash'] = "Please select a specific advertisement to edit.";
                header('Location: manage_advertisements.php');
            }

            // Retrieve selected event/news
            $SQLEditAd = $conn->prepare("SELECT * FROM advertisement_table WHERE id = ?");
            $SQLEditAd->bind_param("s", $_GET['id']);
            $SQLEditAd->execute();
            $adDetails = $SQLEditAd->get_result()->fetch_assoc();
        }

        // POST request
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $verified = array();
            unset($errors);
            foreach ($_POST as $key => $value) {
                $_POST[$key] = trim($_POST[$key]);
                $value = trim($value);

                // input validation
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

                if ($key == 'description' && $value == '')
                    $errors[$key] = '*Description is required.';
                elseif ($key == 'description' && strlen($value) >= 700)
                    $errors[$key] = '*Description must be less than 700 characters.';
                elseif ($key == 'description')
                    $verified[$key] = true;

                if ($key == 'button_message' && strlen($value) >= 50)
                    $errors[$key] = '*Button text must be less than 50 characters.';
                elseif ($_POST["button_message"] == "" && $_POST["button_link"] != "")
                    $errors["button_message"] = '*Button message must not be empty while Button link is filled';
                elseif ($key == 'button_message')
                    $verified[$key] = true;

                if ($key == 'button_link' && strlen($value) >= 700)
                    $errors[$key] = '*Button URL must be less than 700 characters.';
                elseif ($key == 'button_link' && !filter_var($value, FILTER_VALIDATE_URL) && $value != "")
                    $errors[$key] = '*Button URL must be a valid URL. Must include protocol http:// or https://';
                elseif ($_POST["button_link"] == "" && $_POST["button_message"] != "")
                    $errors["button_link"] = '*Button URL must not be empty while Button message is filled';
                elseif ($key == 'button_link' && $value == "") {}
                elseif ($key == 'button_link')
                    $verified[$key] = true;
            }
            // Image extension validation
            if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
                $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                if (!in_array($fileExtension, ["jpg", "jpeg", "png"]))
                    $errors['image'] = '*Photo must be of type jpg, jpeg or png.';
            }
            $adDetails = $_POST;
            isset($_POST['image']) ? $adDetails["photo"] = $_POST['image'] : NULL;

            if (empty($errors)) {
                // Use same photo if unchanged
                if ($_FILES["image"]["name"] == "")
                    $_FILES["image"]["name"] = $adDetails["photo"];

                // Use boolean for check box
                if ($_POST['appliable'] == 'on')
                    $_POST['appliable'] = TRUE;
                else
                    $_POST['appliable'] = FALSE;

                // Nullify date for SQL (0000-00-00)
                if ($_POST["date_to_hide"] == NULL)
                    $_POST["date_to_hide"] = NULL;

                try {
                    // Add advertisement into DB
                    $SQLEditAdvertisements = $conn->prepare("UPDATE advertisement_table SET 
                        title = ?, 
                        description = ?, 
                        button_message = ?, 
                        button_link = ?, 
                        photo = ?, 
                        category = ?, 
                        status = ?, 
                        appliable = ?,
                        date_to_hide = ? 
                        WHERE id = ?
                    ");
                    $SQLEditAdvertisements->bind_param("ssssssssss",$_POST['title'],$_POST['description'],$_POST['button_message'],$_POST['button_link'],$_FILES["image"]["name"],$_POST['category'],$_POST['status'],$_POST['appliable'],$_POST['date_to_hide'],$_POST['id']);
                    if ($SQLEditAdvertisements->execute() == true) {
                        $insertedID = $_POST['id'];
                        // Add image into storage
                        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
                            $uploadDir = "images/";
                            $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                            $fileName = "Ad".$insertedID.".".$fileExtension;

                            // Remove previous photo if exists
                            foreach (['jpg', 'jpeg', 'png'] as $extension) {
                                $oldPhoto = $uploadDir."Ad".$insertedID.".".$extension;
                                if (file_exists($oldPhoto))
                                    unlink($oldPhoto);
                            }

                            // Add new photo to storage
                            if (in_array($fileExtension, ["jpg", "jpeg", "png"])) // Ensure file type is jpg, jpeg, png
                                move_uploaded_file($_FILES["image"]["tmp_name"], $uploadDir . $fileName);
                            
                            // Update db with new image path
                            $conn->query("UPDATE advertisement_table SET photo = '$fileName' WHERE id = '$insertedID'");
                        }

                        // Set flash message and redirect back
                        if ($SQLEditAdvertisements->affected_rows > 0) {
                            $_SESSION['flash_mode'] = "alert-success";
                            $_SESSION['flash'] = "<span class='fw-medium'>Advertisement ".$_POST['id']."</span> updated successfully.";
                        } elseif ($SQLEditAdvertisements->affected_rows == 0) {
                            $_SESSION['flash_mode'] = "alert-secondary";
                            $_SESSION['flash'] = "No changes made to <span class='fw-medium'>Advertisement ".$_POST['id']."</span>.";
                        }
                        
                        header('Location: manage_advertisements.php');
                    } else {
                        $_SESSION['flash_mode'] = "alert-warning";
                        $_SESSION['flash'] = "An error has occured updating <span class='fw-medium'>Advertisement ".$_POST['id']."</span>.";
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

    <?php
        
    ?>

    <div class="container my-3">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="breadcrumb-link text-secondary link-underline link-underline-opacity-0" href="main_menu_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a class="breadcrumb-link text-secondary link-underline link-underline-opacity-0" href="manage_advertisements.php">Manage Advertisements</a></li>
                <li class="breadcrumb-item breadcrumb-active" aria-current="page">Edit Advertisement</li>
            </ol>
        </nav>
    </div>

    <div class="container <?php echo ($_SERVER["REQUEST_METHOD"] == "POST") ? NULL : 'slide-left' ?>">
        <div class="row">
            <div class="col"><h1>Edit Advertisement</h1></div>
        </div>

        <form class="needs-validation" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="multipart/form-data">
            <div class="col mb-4 px-0 mx-0">
                <div class="card">
                    <!-- Event/News adding area -->
                    <div class="row mb-5">
                        <!-- Image -->
                        <div class="col-auto pe-0 me-0">
                            <div class="image-container-events"><img src="images/<?php echo isset($adDetails["photo"]) ? $adDetails["photo"] : "";?>" class="img-fluid profilePictureThumbnail" id="previewImage" alt="profile_picture"></div>
                            <div class="col-10 ms-2 <?php echo (isset($errors['image']) && !empty($errors['image'])) ? 'animate__animated animate__headShake animate__fast' : NULL ?>">
                                <input type="file" class="form-control my-2 pe-0 <?php echo (isset($errors['image'])) ? 'is-invalid' : ((isset($verified['image'])) ? 'is-valid' : ''); ?>" id="imageInput" name='image' accept=".jpg, .jpeg, .png"/>
                                <?php echo (isset($errors['image'])) ? '<div class="invalid-feedback">'.$errors['image'].'</div>' : NULL; ?>
                            </div>
                        </div>

                        <div class="col d-flex flex-column ps-0 ms-0 <?php echo (isset($errors) && !empty($errors)) ? 'animate__animated animate__headShake animate__fast' : NULL ?>">
                            <div class="card-body px-0 flex-grow-1 me-4">
                                <div class="row justify-content-between mb-1">

                                    <!-- Date Added -->
                                    <div class="col-auto">
                                        <div class="row">
                                            <div class="col-auto pe-0">
                                                <?php
                                                    $date = new DateTime($adDetails['date_added']);
                                                    $formattedDate = strtoupper($date->format('D, j M Y'));
                                                    echo '<span class="my-2 pe-0 fw-medium">'.$formattedDate.'</span>';
                                                ?>
                                                <input type="hidden" name="date_added" value="<?php echo htmlspecialchars($adDetails['date_added']); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <!-- Category -->
                                        <select class="badge text-bg-info <?php echo (isset($errors['category'])) ? 'is-invalid' : ((isset($verified['category'])) ? 'is-valid' : ''); ?>" id="category" name="category" style="border: none;">
                                            <option value="Engineering" class="fw-medium text-black bg-white" <?php echo isset($formData['category']) ? (($formData['category'] == 'Engineering') ? 'selected' : '') : (isset($adDetails["category"]) && $adDetails["category"] == 'Engineering' ? 'selected' : ""); ?>>Engineering</option>
                                            <option value="IT" class="fw-medium text-black bg-white" <?php echo isset($formData['category']) ? (($formData['category'] == 'IT') ? 'selected' : '') : (isset($adDetails["category"]) && $adDetails["category"] == 'IT' ? 'selected' : ""); ?>>IT</option>
                                            <option value="Business" class="fw-medium text-black bg-white" <?php echo isset($formData['category']) ? (($formData['category'] == 'Business') ? 'selected' : '') : (isset($adDetails["category"]) && $adDetails["category"] == 'Business' ? 'selected' : ""); ?>>Business</option>
                                            <option value="Design" class="fw-medium text-black bg-white" <?php echo isset($formData['category']) ? (($formData['category'] == 'Design') ? 'selected' : '') : (isset($adDetails["category"]) && $adDetails["category"] == 'Design' ? 'selected' : ""); ?>>Design</option>
                                        </select>
                                        <?php echo (isset($errors['category'])) ? '<div class="invalid-feedback">'.$errors['category'].'</div>' : NULL; ?>

                                        <!-- Status -->
                                        <select class="badge text-bg-success <?php echo (isset($errors['status'])) ? 'is-invalid' : ((isset($verified['status'])) ? 'is-valid' : ''); ?>" id="status" name="status" style="border: none;" onchange="changeType(this)">
                                            <option value="Active" class="fw-medium text-black bg-white" <?php echo isset($formData['status']) ? (($formData['status'] == 'Active') ? 'selected' : '') : (isset($adDetails["status"]) && $adDetails["status"] == 'Active' ? 'selected' : ""); ?>>Active</option>
                                            <option value="Inactive" class="fw-medium text-black bg-white" <?php echo isset($formData['status']) ? (($formData['status'] == 'Inactive') ? 'selected' : '') : (isset($adDetails["status"]) && $adDetails["status"] == 'Inactive' ? 'selected' : ""); ?>>Inactive</option>
                                        </select>
                                        <?php echo (isset($errors['status'])) ? '<div class="invalid-feedback">'.$errors['status'].'</div>' : NULL; ?>
                                    </div>
                                </div>

                                <!-- Title -->
                                <div class="mb-4 col-auto">
                                    <input type="text" class="form-control title fw-medium py-0 <?php echo (isset($errors['title'])) ? 'is-invalid' : ((isset($verified['title'])) ? 'is-valid' : ''); ?>" id="title" name="title" placeholder="Title*" value="<?php echo isset($formData["title"]) ? htmlspecialchars($formData["title"]) : (isset($adDetails["title"]) ? htmlspecialchars($adDetails["title"]) : "");?>">
                                    <?php echo (isset($errors['title'])) ? '<div class="invalid-feedback">'.$errors['title'].'</div>' : NULL; ?>
                                </div>

                                <!-- Description -->
                                <div class="mb-1 col-auto">
                                    <textarea rows="4" type="text" class="form-control py-0 <?php echo (isset($errors['description'])) ? 'is-invalid' : ((isset($verified['description'])) ? 'is-valid' : ''); ?>" id="description" name="description" placeholder="Description*"><?php echo isset($formData["description"]) ? htmlspecialchars($formData["description"]) : (isset($adDetails["description"]) ? htmlspecialchars($adDetails["description"]) : "");?></textarea>
                                    <?php echo (isset($errors['description'])) ? '<div class="invalid-feedback">'.$errors['description'].'</div>' : NULL; ?>
                                </div>
                            </div>

                            <div class="row pe-4">
                                <!-- Custom button -->
                                <div class="col-auto">
                                    <input type="text" class="btn btn-outline-primary text-primary fw-medium py-2 auto-resize-input edit-button-text <?php echo (isset($errors['button_message'])) ? 'is-invalid' : ((isset($verified['button_message'])) ? 'is-valid' : ''); ?>" id="button_message" name="button_message" placeholder="Custom button message" maxlength="50" value="<?php echo isset($formData["button_message"]) ? htmlspecialchars($formData["button_message"]) : (isset($adDetails["button_message"]) ? htmlspecialchars($adDetails["button_message"]) : "");?>">
                                    <?php echo (isset($errors['button_message'])) ? '<div class="invalid-feedback">'.$errors['button_message'].'</div>' : NULL; ?>
                                </div>

                                <!-- Button link -->
                                <div class="col">
                                    <input type="text" class="form-control text-primary fst-italic py-2 <?php echo (isset($errors['button_link'])) ? 'is-invalid' : ((isset($verified['button_link'])) ? 'is-valid' : ''); ?>" id="button_link" name="button_link" placeholder="Custom button URL (e.g. https://www.google.com)" value="<?php echo isset($formData["button_link"]) ? htmlspecialchars($formData["button_link"]) : (isset($adDetails["button_link"]) ? htmlspecialchars($adDetails["button_link"]) : "");?>">
                                    <?php echo (isset($errors['button_link'])) ? '<div class="invalid-feedback">'.$errors['button_link'].'</div>' : NULL; ?>
                                </div>
                            </div>

                            <!-- Apply Button -->
                            <div class="row">
                                <div class="col mb-1 mt-5">
                                    <input type="checkbox" class="form-check-input" id="appliable" name="appliable" <?php echo (isset($formData["appliable"])) ? "checked" : (isset($adDetails["appliable"]) && $adDetails["appliable"] == TRUE ? "checked" : NULL) ?>>
                                    <label class="form-check-label" for="appliable">Enable user application to this Ad</label><br/>
                                    <button type="button" id="applyButton" class="btn btn-primary fw-medium py-2 px-5" <?php echo (isset($formData["appliable"])) ? NULL : (isset($adDetails["appliable"]) && $adDetails["appliable"] == TRUE ? NULL : "disabled") ?> data-bs-toggle="tooltip" data-bs-title="This is just a preview button">Apply</button>
                                </div>

                                <!-- Date to hide -->
                                <div class="col-auto mb-1 mt-5 me-4">
                                    <div class=" pe-0 me-2"> <label for="date_to_hide" class="">Date/Time to hide:</label> </div>
                                    <div class=" ps-0"> <input type="datetime-local" class="form-control fw-normal py-0" id="date_to_hide" name="date_to_hide" value="<?php echo isset($formData["date_to_hide"]) ? htmlspecialchars($formData["date_to_hide"]) : ($adDetails["date_to_hide"] ? htmlspecialchars($adDetails["date_to_hide"]) : NULL)?>"> </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ID, Photo -->
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($adDetails['id']); ?>"> 
                    <input type="hidden" name="image" value="<?php echo htmlspecialchars($adDetails['photo']); ?>">

                    <!-- Footer? -->
                    <div class="row justify-content-end align-items-center mb-4">
                        <div class="col ms-2"><span class="text-secondary fst-italic"><strong class="text-black">*</strong>Indicates required field</span></div>
                        <div class="col-auto">
                            <a role="button" href="manage_advertisements.php" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary ms-3 me-4 px-4 fw-medium"><i class="bi bi-arrow-counterclockwise me-2" style="-webkit-text-stroke: 0.25px;"></i>Update Advertisement</button>
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
        // Bootstrap tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

        // Dynamically adjusts button width to its content
        const inputElement = document.getElementById("button_message");
        function adjustInputSize() {
            inputElement.style.width = "auto"; // Allow the input to resize
            inputElement.style.width = (inputElement.scrollWidth + 5) + "px"; // Add some padding
        }
        inputElement.addEventListener("input", adjustInputSize);
        adjustInputSize();

        var selectOption = document.getElementById("status");
        var image = document.getElementById("previewImage");
        var filename = image.src.substring(image.src.lastIndexOf('/') + 1);
        var imageInput1 = document.getElementById("imageInput");

        // Change Events/News type badges
        function changeType(selectElement) {
            var selectedOption = selectElement.options[selectElement.selectedIndex].value;

            // Remove previous class
            selectElement.classList.remove("text-bg-success");
            selectElement.classList.remove("text-bg-secondary");

            // Add appropriate class + transition button
            if (selectedOption == "Active")
                selectElement.classList.add("text-bg-success");
            else if (selectedOption == "Inactive")
                selectElement.classList.add("text-bg-secondary");
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
            selectOption.classList.remove("text-bg-secondary");

            // Add Bootstrap styling class based on selected option
            if (selectOption.value == "Active")
                selectOption.classList.add("text-bg-success");
            else if (selectOption.value == "Inactive")
                selectOption.classList.add("text-bg-secondary");
        }

        // Enable or disable the Apply button based on the checkbox state
        document.getElementById('appliable').addEventListener('change', () => {
            document.getElementById('applyButton').disabled = !document.getElementById('appliable').checked;
        });
    </script>
</body>
</html>