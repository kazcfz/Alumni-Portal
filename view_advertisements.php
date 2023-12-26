<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Advertisements</title>

    <link rel="stylesheet" href="css/styles.css">

    <!-- Bootstrap CSS --><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons --><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Animate.css --><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
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
    ?>

    <!-- Top nav bar -->
    <nav class="navbar sticky-top navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand mx-0 mb-0 h1" href="main_menu.php">Alumni Portal</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse me-5" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto">
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
                        <a class="nav-link nav-main-active px-5" aria-current="page" href="view_advertisements.php"><i class="bi bi-megaphone-fill nav-bi"></i></a>
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
                <li class="breadcrumb-item breadcrumb-active" aria-current="page">Advertisements</li>
            </ol>
        </nav>
    </div>

    <?php
        // GET the POST (which was retrieved from GET previously)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['filterStatus'])) 
                $_GET['filterStatus'] = $_POST['filterStatus'];
            if (isset($_POST['filterStatus'])) 
                $_GET['filterCategory'] = $_POST['filterCategory'];
            if (isset($_POST['filterStatus'])) 
                $_GET['search'] = $_POST['search'];
        }

        if (isset($_SESSION['ad_apply_error']) && $_SESSION['ad_apply_error'] == true) {
            echo "<script>
                window.onload = function() {
                    var errorModalCenter = new bootstrap.Modal(document.getElementById('errorModalCenter'));
                    errorModalCenter.show();
                }
            </script>";
            $modalOn = true;
            unset($_SESSION['ad_apply_error']);
        }

        if (isset($_SESSION['ad_apply_success']) && $_SESSION['ad_apply_success'] == true) {
            echo "<script>
                window.onload = function() {
                    var successModalCenter = new bootstrap.Modal(document.getElementById('successModalCenter'));
                    successModalCenter.show();
                }
            </script>";
            $modalOn = true;
            unset($_SESSION['ad_apply_success']);
        }

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

        <div class="container mb-5">
            <h1 class="<?php echo (isset($_POST['eventID']) || isset($_GET['filterStatus']) || isset($_GET['filterCategory']) || isset($_GET['search'])) ? NULL : 'slide-left' ?>">Advertisements</h1>
            
            <!-- Filter -->
            <div class="container mt-3 py-3 px-4 card bg-white fw-medium <?php echo (isset($_POST['eventID']) || isset($_GET['filterStatus']) || isset($_GET['filterCategory']) || isset($_GET['search'])) ? NULL : 'slide-left' ?>">
                <form id="eventsFilterForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
                    <!-- Status (All, Active, Inactive) -->
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filterStatus" id="filterStatus1" value="All" <?php echo (isset($_GET['filterStatus']) && $_GET['filterStatus'] == 'All') ? 'checked' : NULL ?>>
                        <label class="form-check-label" for="filterStatus1">All</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filterStatus" id="filterStatus2" value="Active" <?php echo (isset($_GET['filterStatus']) && $_GET['filterStatus'] == 'Active') ? 'checked' : ((!isset($_GET['filterCategory'])) ? 'checked' : NULL ) ?>>
                        <label class="form-check-label badge text-bg-success" for="filterStatus2">Active</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="filterStatus" id="filterStatus3" value="Inactive" <?php echo (isset($_GET['filterStatus']) && $_GET['filterStatus'] == 'Inactive') ? 'checked' : NULL ?>>
                        <label class="form-check-label badge text-bg-secondary" for="filterStatus3">Inactive</label>
                    </div>

                    <!-- Departments (All, Engineering, IT, Business, Design) -->
                    <div class="form-check-inline ms-4">
                        <div class="input-group">
                            <label class="input-group-text" for="filterCategory"><i class="bi bi-buildings"></i></label>
                            <select class="form-select fw-medium" id="filterCategory" name="filterCategory" aria-label="Time filter">
                                <option value="All" class="fw-medium" <?php echo (isset($_GET['filterCategory']) && $_GET['filterCategory'] == 'All') ? 'selected' : NULL ?>>All</option>
                                <option value="Engineering" class="fw-medium" <?php echo (isset($_GET['filterCategory']) && $_GET['filterCategory'] == 'Engineering') ? 'selected' : NULL ?>>Engineering</option>
                                <option value="IT" class="fw-medium" <?php echo (isset($_GET['filterCategory']) && $_GET['filterCategory'] == 'IT') ? 'selected' : NULL ?>>IT</option>
                                <option value="Business" class="fw-medium" <?php echo (isset($_GET['filterCategory']) && $_GET['filterCategory'] == 'Business') ? 'selected' : NULL ?>>Business</option>
                                <option value="Design" class="fw-medium" <?php echo (isset($_GET['filterCategory']) && $_GET['filterCategory'] == 'Design') ? 'selected' : NULL ?>>Design</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary fw-medium mb-1">Display List</button>

                    <!-- Search box -->
                    <div class="form-check-inline me-0 float-end">
                        <div class="input-group">
                            <input type="text" class="form-control py-2" placeholder="Search advertisements" name="search" aria-label="Search" aria-describedby="button-addon2" value="<?php echo (isset($_GET['search'])) ? trim($_GET['search']) : NULL; ?>">
                            <button class="btn btn-primary px-3 py-2" type="submit" id="button-addon2"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Advertisements -->
            <div class="row row-cols-1 mt-4 px-0 mx-0 <?php echo ($_SERVER["REQUEST_METHOD"] == "POST") ? NULL : 'slide-left' ?>">
                <?php 
                    $filterStatus = "";
                    $filterCategory = "";
                    $filterSearch = "";

                    // Type filter for Advertisements
                    if (isset($_GET['filterStatus']) && $_GET['filterStatus'] != 'All')
                        $filterStatus = "status = '" . $_GET['filterStatus'] . "'";
                    elseif (!isset($_GET['filterStatus']))
                        $filterStatus = "status = 'Active'";

                    // Time filter for Advertisements
                    if (isset($_GET['filterCategory']) && $_GET['filterCategory'] != 'All') {
                        $filterCategory = "category = '" . $_GET['filterCategory'] . "'";
                    }

                    // Search filter for Advertisements
                    if (isset($_GET['search']) && $_GET['search'] != "") {
                        $trimSearch = strtolower(trim($_GET['search']));
                        $filterSearch = "(
                            LOWER(title) LIKE '%$trimSearch%' OR 
                            LOWER(description) LIKE '%$trimSearch%' OR 
                            LOWER(date_added) LIKE '%$trimSearch%' OR 
                            DATE_FORMAT(date_added, '%a, %e %b %Y') LIKE '%$trimSearch%' OR 
                            LOWER(button_message) LIKE '%$trimSearch%' OR 
                            LOWER(button_link) LIKE '%$trimSearch%' OR 
                            LOWER(category) LIKE '%$trimSearch%' OR 
                            LOWER(status) LIKE '%$trimSearch%'
                        )";
                    }

                    // Puts WHERE and AND to the query appropriately
                    $conditions = array_filter([$filterStatus, $filterCategory, $filterSearch]);
                    if (!empty($conditions))
                        $whereClause = "WHERE " . implode(" AND ", $conditions);
                    else
                        $whereClause = "";

                    date_default_timezone_set('Asia/Kuching');
                    $today = date('Y-m-d H:i:s');
                    if (isset($whereClause) && (!empty($whereClause)))
                        $nonHiddenAds = "AND (date_to_hide IS NULL OR date_to_hide >= '".$today."')";
                    else
                        $nonHiddenAds = "WHERE (date_to_hide IS NULL OR date_to_hide >= '".$today."')";

                    // Retrieve from db
                    $allAdvertisements = $conn->query("SELECT * FROM advertisement_table $whereClause $nonHiddenAds ORDER BY id DESC");

                    // Load the advertisements if there's at least 1
                    if ($allAdvertisements && $allAdvertisements->num_rows > 0) {
                        while ($advertisement = $allAdvertisements->fetch_assoc()) {
                            // Format date
                            $date = new DateTime($advertisement['date_added']);
                            $formattedDate = strtoupper($date->format('D, j M Y'));
                ?>
                    <!-- Each card made for each advertisement -->
                    <div class="col mb-4 px-0 mx-0">
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

                                    <!-- Apply Button -->
                                    <?php if ($advertisement['appliable'] == 1) { ?>
                                        <div class="mb-4">
                                            <form action="<?php echo htmlspecialchars('advertisement_apply.php');?>" method="GET">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($advertisement['id']); ?>">
                                                <button type='submit' class='btn btn-primary fw-medium py-2 px-5'>Apply</button>
                                            </form>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                        }
                    // No result from filter
                    } elseif (isset($_GET['filterStatus']) || isset($_GET['filterCategory']) || isset($_GET['search']) && $_GET['search'] != "") { ?>
                        <div class="text-center slide-left">
                            <div class="row align-items-center ps-2 py-2">
                                <div class="col-12"><h5 class="fw-bold text-secondary">No advertisements available from your filter</h5></div>
                            </div>
                        </div>
                    <!-- Simply no result -->
                    <?php } else { ?>
                        <div class="text-center slide-left">
                            <div class="row align-items-center ps-2 py-2">
                                <div class="col-12"><h5 class="fw-bold text-secondary">No advertisements available</h5></div>
                            </div>
                        </div>
                <?php } 
                    $conn->close();
                ?>
            </div>
        </div>
    </div>
    
    <!-- Boostrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        // Ignore confirm form resubmission after POST request to register event
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>
</body>
</html>