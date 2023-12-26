<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Events/News</title>

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
                        <a class="nav-link nav-main-active px-5" aria-current="page" href="view_events.php"><i class="bi bi-calendar-event-fill nav-bi"></i></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link px-5" href="view_advertisements.php"><i class="bi bi-megaphone nav-bi"></i></a>
                    </li>
                </ul>
            </div>
            <?php include 'nav_user.php' ?>
        </div>
    </nav>

    <!-- Success Modal -->
    <div class='modal animate__animated animate__fadeIn animate__faster' id='successModalCenter' tabindex='-1' aria-labelledby='successModalCenter' aria-hidden='true'>
        <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h3 class='modal-title text-success fw-bold'>Success!</h3>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        <p>You've successfully registered for <span class="fw-medium"><?php echo $_POST['eventTitle']; ?></span>!</p>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-primary' data-bs-dismiss='modal'>OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Error Modal -->
    <div class='modal animate__animated animate__headShake animate__fast' id='errorModalCenter' tabindex='-1' aria-labelledby='errorModalCenterTitle' aria-hidden='true'>
        <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h3 class='modal-title text-danger fw-bold'>Error!</h3>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        <p>You're already registered for <span class="fw-medium"><?php echo $_POST['eventTitle']; ?></span>!</p>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-primary' data-bs-dismiss='modal'>OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error2 Modal -->
    <div class='modal animate__animated animate__headShake animate__fast' id='error2ModalCenter' tabindex='-1' aria-labelledby='error2ModalCenterTitle' aria-hidden='true'>
        <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h3 class='modal-title text-danger fw-bold'>Error!</h3>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        <p>An error has occured while registering for <span class="fw-medium"><?php echo $_POST['eventTitle']; ?></span>!</p>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-primary' data-bs-dismiss='modal'>OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="container my-3">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="breadcrumb-link text-secondary link-underline link-underline-opacity-0" href="main_menu.php">Home</a></li>
                <li class="breadcrumb-item breadcrumb-active" aria-current="page">Events/News</li>
            </ol>
        </nav>
    </div>

    <?php
        // Modal is off by default
        $modalOn = false;
        
        // POST method
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // GET the POST (which is retrieved from GET previously)
            if (isset($_POST['filterType'])) 
                $_GET['filterType'] = $_POST['filterType'];
            if (isset($_POST['filterType'])) 
                $_GET['filterTime'] = $_POST['filterTime'];
            if (isset($_POST['filterType'])) 
                $_GET['search'] = $_POST['search'];
            
            // Event registration
            if (isset($_POST['eventID'])) {
                $eventID = $_POST['eventID'];
                $eventTitle = $_POST['eventTitle'];
                $accountEmail = $_SESSION['logged_account']['email'];
                $result = $conn->query("SELECT * FROM event_registration_table WHERE event_id = $eventID AND participant_email = '$accountEmail'");

                // If user is already registered to event
                if ($result->num_rows > 0) {
                    echo "<script>
                        window.onload = function() {
                            var errorModalCenter = new bootstrap.Modal(document.getElementById('errorModalCenter'));
                            errorModalCenter.show();
                        }
                    </script>";
                    $modalOn = true;
                } else {
                    // User is not registered, insert the registration
                    if ($conn->query("INSERT INTO event_registration_table (event_id, participant_email) VALUES ($eventID, '$accountEmail')") == TRUE) {
                        echo "<script>
                            window.onload = function() {
                                var successModalCenter = new bootstrap.Modal(document.getElementById('successModalCenter'));
                                successModalCenter.show();
                            }
                        </script>";
                        $modalOn = true;
                    // Registration somehow failed
                    } else {
                        echo "<script>
                            window.onload = function() {
                                var error2ModalCenterTitle = new bootstrap.Modal(document.getElementById('error2ModalCenterTitle'));
                                error2ModalCenterTitle.show();
                            }
                        </script>";
                        $modalOn = true;
                    }
                }
            }
        }
    ?>

    <div class="container mb-5">
        <h1 class="<?php echo (isset($_POST['eventID']) || isset($_GET['filterType']) || isset($_GET['filterTime']) || isset($_GET['search'])) ? NULL : 'slide-left' ?>">Events/News</h1>
        
        <!-- Filter -->
        <div class="container mt-3 py-3 px-4 card bg-white fw-medium <?php echo (isset($_POST['eventID']) || isset($_GET['filterType']) || isset($_GET['filterTime']) || isset($_GET['search'])) ? NULL : 'slide-left' ?>">
            <form id="eventsFilterForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
                <!-- Type (All, Events, News) -->
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="filterType" id="filterType1" value="All" <?php echo (isset($_GET['filterType']) && $_GET['filterType'] == 'All') ? 'checked' : ((!isset($_GET['filterTime'])) ? 'checked' : NULL ) ?>>
                    <label class="form-check-label" for="filterType1">All</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="filterType" id="filterType2" value="Event" <?php echo (isset($_GET['filterType']) && $_GET['filterType'] == 'Event') ? 'checked' : NULL ?>>
                    <label class="form-check-label badge text-bg-success" for="filterType2">Events</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="filterType" id="filterType3" value="News" <?php echo (isset($_GET['filterType']) && $_GET['filterType'] == 'News') ? 'checked' : NULL ?>>
                    <label class="form-check-label badge text-bg-warning" for="filterType3">News</label>
                </div>

                <!-- Time (All, Past, Upcoming) -->
                <div class="form-check-inline ms-4">
                    <div class="input-group">
                        <label class="input-group-text" for="filterTime"><i class="bi bi-clock-history" style="-webkit-text-stroke: 0.25px;"></i></label>
                        <select class="form-select fw-medium" id="filterTime" name="filterTime" aria-label="Time filter">
                            <option value="All" class="fw-medium" <?php echo (isset($_GET['filterTime']) && $_GET['filterTime'] == 'All') ? 'selected' : NULL ?>>All</option>
                            <option value="Past" class="fw-medium" <?php echo (isset($_GET['filterTime']) && $_GET['filterTime'] == 'Past') ? 'selected' : NULL ?>>Past</option>
                            <option value="Upcoming" class="fw-medium" <?php echo (isset($_GET['filterTime']) && $_GET['filterTime'] == 'Upcoming') ? 'selected' : NULL ?>>Upcoming</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary fw-medium mb-1">Display List</button>

                <!-- Search Box -->
                <div class="form-check-inline me-0 float-end">
                    <div class="input-group">
                        <input type="text" class="form-control py-2" placeholder="Search events" name="search" aria-label="Search" aria-describedby="button-addon2" value="<?php echo (isset($_GET['search'])) ? trim($_GET['search']) : NULL; ?>">
                        <button class="btn btn-primary px-3 py-2" type="submit" id="button-addon2"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Events/News -->
        <div class="row row-cols-1 mt-4 px-0 mx-0 <?php echo ($modalOn == false) ? 'slide-left' : NULL ?>">
            <?php 
                $filterType = "";
                $filterTime = "";
                $filterSearch = "";

                // Type filter for Events/News
                if (isset($_GET['filterType']) && $_GET['filterType'] != 'All')
                    $filterType = "type = '" . $_GET['filterType'] . "'";

                // Time filter for Events/News
                if (isset($_GET['filterTime']) && $_GET['filterTime'] != 'All') {
                    date_default_timezone_set('Asia/Kuching');
                    $todayDate = date('Y-m-d');
                    if ($_GET['filterTime'] == 'Upcoming')
                        $filterTime = "event_date >= '" . $todayDate . "'";
                    elseif ($_GET['filterTime'] == 'Past')
                        $filterTime = "event_date < '" . $todayDate . "'";
                }

                // Search filter for Events/News
                if (isset($_GET['search']) && $_GET['search'] != "") {
                    $trimSearch = strtolower(trim($_GET['search']));
                    $filterSearch = "(
                        LOWER(title) LIKE '%$trimSearch%' OR 
                        LOWER(location) LIKE '%$trimSearch%' OR 
                        LOWER(description) LIKE '%$trimSearch%' OR 
                        LOWER(event_date) LIKE '%$trimSearch%' OR 
                        DATE_FORMAT(event_date, '%a, %e %b %Y') LIKE '%$trimSearch%' OR 
                        LOWER(type) LIKE '%$trimSearch%'
                        )";
                }

                // Puts WHERE and AND to the query appropriately
                $conditions = array_filter([$filterType, $filterTime, $filterSearch]);
                if (!empty($conditions))
                    $whereClause = "WHERE " . implode(" AND ", $conditions);
                else
                    $whereClause = "";

                // Query the database with the WHERE from previous
                $allEventsNews = $conn->query("SELECT * FROM event_table $whereClause ORDER BY id DESC");

                // Load the events/news if there's at least 1
                if ($allEventsNews && $allEventsNews->num_rows > 0) {
                    while ($eventsNews = $allEventsNews->fetch_assoc()) {
                        // Format date
                        $date = new DateTime($eventsNews['event_date']);
                        $formattedDate = strtoupper($date->format('D, j M Y'));
            ?>
                <div class="col mb-4 px-0 mx-0">
                    <div class="card">
                        <div class="row">
                            <div class="col-auto">
                                <div class="image-container-events"><img src="images/<?php echo $eventsNews['photo']; ?>" class="img-fluid profilePictureThumbnail" alt="profile_picture"></div>
                            </div>
                            <div class="col d-flex flex-column">
                                <div class="card-body px-2 flex-grow-1 me-4">
                                    <?php 
                                        echo "<span class='fw-medium fs-6'>".$formattedDate."</span>". (($eventsNews['type'] == 'Event') ? "<span class='badge text-bg-success mt-1 float-end'>Events</span>" : "<span class='badge text-bg-warning mt-1 float-end'>News</span>")."
                                            <br/>
                                            <span class='card-title h3'>".$eventsNews['title']."</span>
                                            <br/>
                                            <span class='card-text fw-medium text-secondary'>".$eventsNews['location']."</span>
                                            <br/><br/>
                                            <span class='card-text'>".$eventsNews['description']."</span>";
                                    ?>
                                </div>
                                <?php if ($eventsNews['type'] == 'Event') { ?>
                                    <div class="mb-4">
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                                            <input type="hidden" name="eventID" value="<?php echo htmlspecialchars($eventsNews['id']); ?>">
                                            <input type="hidden" name="eventTitle" value="<?php echo htmlspecialchars($eventsNews['title']); ?>">
                                            <?php echo (isset($_GET['filterType'])) ? '<input type="hidden" name="filterType" value="'.htmlspecialchars($_GET['filterType']).'">' : NULL ?>
                                            <?php echo (isset($_GET['filterTime'])) ? '<input type="hidden" name="filterTime" value="'.htmlspecialchars($_GET['filterTime']).'">' : NULL ?>
                                            <?php echo (isset($_GET['search'])) ? '<input type="hidden" name="search" value="'.htmlspecialchars($_GET['search']).'">' : NULL ?>
                                            <button type='submit' class='btn btn-primary fw-medium px-4'>Sign Up</button>
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
                } elseif (isset($_GET['filterType']) || isset($_GET['filterTime']) || isset($_GET['search']) && $_GET['search'] != "") { ?>
                    <div class="text-center slide-left">
                        <div class="row align-items-center ps-2 py-2">
                            <div class="col-12"><h5 class="fw-bold text-secondary">No events/news available from your filter</h5></div>
                        </div>
                    </div>
                <!-- Simply no result -->
                <?php } else { ?>
                    <div class="text-center slide-left">
                        <div class="row align-items-center ps-2 py-2">
                            <div class="col-12"><h5 class="fw-bold text-secondary">No events/news available</h5></div>
                        </div>
                    </div>
            <?php } 
                $conn->close();
            ?>
        </div>
    </div>
    
    <!-- Boostrap JS --><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
        // Ignore confirm form resubmission after POST request to register event
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>
</body>
</html>