<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Profile Detail</title>

    <link rel="stylesheet" href="css/styles.css">

    <!-- Bootstrap CSS --><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons --><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .card {
            width: 25rem;
            border: none;
            box-shadow: 0 2px 2px rgba(0,0,0,.08), 0 0 6px rgba(0,0,0,.05);
            transition: 0.2s ease;
        }

        .card:hover{
            background-color: #E4E6E9;
            transition: 0.075s ease;
        }

        .profilePictureThumbnail{
            width: 100px;
            border-radius: 50%;
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
                <ul class="navbar-nav mx-auto fake-active">
                    <li class="nav-item mx-1">
                        <a class="nav-link px-5" href="main_menu.php"><i class="bi bi-house-door nav-bi "></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link px-5" href="view_alumni.php"><i class="bi bi-people nav-bi"></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link px-5" href="view_events.php"><i class="bi bi-calendar-event nav-bi"></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link px-5" href="view_advertisements.php"><i class="bi bi-megaphone nav-bi"></i></a>
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
                <?php echo (isset($alumnusToViewEmail) && $alumnusToViewEmail != $_SESSION['logged_account']['email']) ? '<li class="breadcrumb-item"><a class="breadcrumb-link text-secondary link-underline link-underline-opacity-0" href="view_alumni.php">Alumni Friends</a></li>' : ''; ?>
                <li class="breadcrumb-item breadcrumb-active" aria-current="page"><?php echo (isset($alumnusToView)) ? $alumnusToViewName : '?'; ?></li>
            </ol>
        </nav>
    </div>

    <?php if (isset($alumnusToView)){ ?>
        <div class="container py-4 px-4 mainView bg-white <?php echo (!isset($_GET["search"])) ? 'slide-left' : ''; ?>">
            <div class="row">
                <!-- Profile image -->
                <div class="col-auto mx-3">
                    <?php
                        if (isset($alumnusToViewProfilePicture) && $alumnusToViewProfilePicture != "")
                            echo '<div class="image-container"><img src="profile_images/'.$alumnusToViewProfilePicture.'" class="img-fluid profilePicture ms-1" width="250" height="250" alt="profile_picture"></div>';
                        elseif ($alumnusToViewGender == "Male")
                            echo '<div class="image-container"><img src="profile_images/default-male-user-profile-icon.jpg" class="img-fluid profilePicture ms-1" width="250" height="250" alt="profile_picture"></div>';
                        elseif ($alumnusToViewGender == "Female")
                            echo '<div class="image-container"><img src="profile_images/default-female-user-profile-icon.jpg" class="img-fluid profilePicture ms-1" width="250" height="250" alt="profile_picture"></div>';
                    ?>
                </div>

                <div class="col">
                    <!-- Resume -->
                    <div class="row">
                        <div class="col"><?php echo (isset($alumnusToViewName)) ? '<h2 class="mb-0">'.$alumnusToViewName.'</h2>' : ''; ?></div>
                            <?php 
                                if (isset($alumnusToViewResume) && $alumnusToViewResume != '') {
                            ?>
                                <form action="view_resume.php" method="GET" class="col-auto" target="_blank">
                                    <input type="hidden" name="resume" value="<?php echo htmlspecialchars($alumnusToViewResume); ?>">
                                    <button type="submit" class="btn btn-primary px-4 fw-medium"><i class="bi bi-file-earmark-text me-2"></i>View resume</button>
                                </form>
                            <?php } else { ?>
                                <form class="col-auto">
                                    <button class="btn btn-primary disabled px-4 fw-medium" aria-disabled="true"><i class="bi bi-file-earmark-text me-2"></i>View resume</button>
                                </form>
                            <?php } ?>
                        <?php echo ($alumnusToViewEmail == $_SESSION['logged_account']['email']) ? '<div class="col-auto"><a role="button" href="update_profile.php?email='.htmlspecialchars($_SESSION['logged_account']['email']).'" class="btn btn-gray fw-medium px-3"><i class="bi bi-pencil-fill nav-user-bi me-2"></i>Update profile</a></div>' : ''; ?>
                    </div>

                    <!-- Job, Company, Current Location -->
                    <div class="row">
                        <div class="col">
                            <?php
                                echo (isset($alumnusToViewJobPosition) && $alumnusToViewJobPosition != '') ? '<span class="mb-2">'.$alumnusToViewJobPosition.'</span>' : '';
                                echo (isset($alumnusToViewJobPosition) && $alumnusToViewJobPosition != '' && isset($alumnusToViewCompany) && $alumnusToViewCompany != '') ? '<span> at </span>' : '';
                                echo (isset($alumnusToViewCompany) && $alumnusToViewCompany != '') ? '<span>'.$alumnusToViewCompany.'</span>' : '';
                                echo (((isset($alumnusToViewCompany) && $alumnusToViewCompany != '') || isset($alumnusToViewJobPosition) && $alumnusToViewJobPosition != '') && isset($alumnusToViewCurrentLocation) && $alumnusToViewCurrentLocation != '') ? '<span class="middle-dot mx-1"></span>' : '';
                                echo (isset($alumnusToViewCurrentLocation) && $alumnusToViewCurrentLocation != '') ? '<span>'.$alumnusToViewCurrentLocation.'</span>' : '';
                            ?>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="row mb-3">
                        <div class="col">
                            <?php echo (isset($alumnusToViewEmail)) ? '<a class="link-underline link-underline-opacity-0" href="mailto:'.$alumnusToViewEmail.'">'.$alumnusToViewEmail.'</a>' : '';?>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Hometown -->
                        <div class="col">
                            <?php echo (isset($alumnusToViewHometown)) ? '<h4 class="mt-3 mb-2 pt-0">Hometown</h4><p class="my-0 ms-3 fw-medium">'.$alumnusToViewHometown.'</p>' : ''; ?>
                        </div>
                        <!-- Degree, Campus, Year Graduated -->
                        <div class="col">
                            <?php
                                echo ((isset($alumnusToViewDegree) && $alumnusToViewDegree != '') || (isset($alumnusToViewDegreeYearGraduated) && $alumnusToViewDegreeYearGraduated != '') || (isset($alumnusToViewCampus) && $alumnusToViewCampus != '')) ? '<h4 class="mt-3 mb-2 pt-0">Education</h4>' : '';
                                echo (isset($alumnusToViewCampus)) ? '<p class="my-0 ms-3 fw-medium">'.$alumnusToViewCampus.'</p>' : '';
                                echo (isset($alumnusToViewDegree)) ? '<p class="my-0 ms-3">'.$alumnusToViewDegree.'</p>' : '';
                                echo (isset($alumnusToViewDegreeYearGraduated) && $alumnusToViewDegreeYearGraduated != '') ? '<p class="my-0 ms-3 fw-light text-secondary">Graduated in '.$alumnusToViewDegreeYearGraduated.'</p>' : '';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View alumni friends -->
        <div class="container my-5">
            <div class="row">
                <!-- Title -->
                <h3><?php echo ($alumnusToViewEmail == $_SESSION['logged_account']['email']) ? 'Your Alumni Friends' : 'Other Alumni Friends'; ?></h3>

                <!-- Search box -->
                <form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method = "GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control py-3 my-3" placeholder="Search" name="search" aria-label="Search" aria-describedby="button-addon2" value="<?php echo (isset($_GET['search'])) ? $_GET['search'] : ""; ?>">
                        <input type="hidden" class="form-control" name="email" value="<?php echo htmlspecialchars($alumnusToViewEmail); ?>">
                        <button class="btn btn-primary px-4 py-3 my-3" type="submit" id="button-addon2"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <?php
                    $loggedAccountEmail = $_SESSION['logged_account']['email'];

                    // Perform search on query, otherwise simply fetch all
                    $searchQuery = "";
                    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
                        $searchQuery = trim(strtolower($_GET['search']));
    
                        if ($searchQuery != ""){
                            $allUsers = $conn->query("SELECT user_table.*, account_table.type FROM user_table JOIN account_table ON user_table.email = account_table.email WHERE user_table.email != '$loggedAccountEmail' AND user_table.email != '$alumnusToViewEmail' AND account_table.type != 'Admin' AND 
                                (LOWER(user_table.email) LIKE '%$searchQuery%' OR 
                                LOWER(user_table.first_name) LIKE '%$searchQuery%' OR 
                                LOWER(user_table.last_name) LIKE '%$searchQuery%' OR 
                                LOWER(user_table.dob) LIKE '%$searchQuery%' OR 
                                LOWER(user_table.gender) LIKE '%$searchQuery%' OR 
                                LOWER(user_table.contact_number) LIKE '%$searchQuery%' OR 
                                LOWER(user_table.hometown) LIKE '%$searchQuery%' OR 
                                LOWER(user_table.current_location) LIKE '%$searchQuery%' OR 
                                LOWER(user_table.job_position) LIKE '%$searchQuery%' OR 
                                LOWER(user_table.qualification) LIKE '%$searchQuery%' OR 
                                LOWER(user_table.year) LIKE '%$searchQuery%' OR 
                                LOWER(user_table.university) LIKE '%$searchQuery%' OR 
                                LOWER(user_table.company) LIKE '%$searchQuery%' OR 
                                LOWER(user_table.resume) LIKE '%$searchQuery%')
                            ");
                        } else 
                            $allUsers = $conn->query("SELECT user_table.*, account_table.type FROM user_table JOIN account_table ON user_table.email = account_table.email WHERE user_table.email != '$loggedAccountEmail' AND user_table.email != '$alumnusToViewEmail' AND account_table.type != 'Admin'");
                    } else
                        $allUsers = $conn->query("SELECT user_table.*, account_table.type FROM user_table JOIN account_table ON user_table.email = account_table.email WHERE user_table.email != '$loggedAccountEmail' AND user_table.email != '$alumnusToViewEmail' AND account_table.type != 'Admin'");
    
                    // Display each retrieved alumnus in its card
                    if ($allUsers && $allUsers->num_rows > 0) {
                        while ($user = $allUsers->fetch_assoc()) {
                            // Name and hometown
                            $listDetails = "<h5 class='card-title'>".$user['first_name']." ".$user['last_name']."</h5>
                                <p class='card-text'>".$user['hometown']."</p>";
    
                            // Default gender profile photo
                            if ($user['gender'] == "Male")
                                $listProfilePicture = "profile_images/default-male-user-profile-icon.jpg";
                            elseif ($user['gender'] == "Female")
                                $listProfilePicture = "profile_images/default-female-user-profile-icon.jpg";
    
                            // Or use custom uploaded photo instead
                            if ($user['profile_image'] != null)
                                $listProfilePicture = "profile_images/".$user['profile_image'];
    
                            // Create the card
                            echo '
                                <div class="col-auto mb-3 slide-left">
                                    <form action="'.htmlspecialchars('profile_detail.php').'" method="GET">
                                        <input type="hidden" name="email" value="'.$user['email'].'">
                                        <div class="card">
                                            <div class="row align-items-center ps-2 py-2">
                                                <div class="col-auto">
                                                    <div class="image-container-alumni"><img src="'.$listProfilePicture.'" class="img-fluid profilePictureThumbnail" alt="profile_picture"></div>
                                                </div>
                                                <div class="col">
                                                    <div class="card-body px-2">
                                                        '.$listDetails.'
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="stretched-link btn-hidden"></button>
                                        </div>
                                    </form>
                                </div>
                            ';
                        }
                    // If not results found with filter
                    } elseif($allUsers && $allUsers->num_rows == 0 && $searchQuery != "") {
                        echo '
                            <div class="text-center slide-left">
                                <div class="row align-items-center ps-2 py-2">
                                    <div class="col-12"><h5 class="fw-bold text-secondary">No results for: '.$_GET['search'].'</h5></div>
                                </div>
                            </div>
                        ';
                    // If no alumnus to retrieve
                    } else {
                        echo '
                            <div class="text-center slide-left">
                                <div class="row align-items-center ps-2 py-2">
                                    <div class="col-12"><h5 class="fw-bold text-secondary">No other registered alumni friends</h5></div>
                                </div>
                            </div>
                        ';
                    }
                ?>
            </div>
        </div>
    <?php
        } else {
            echo '<h4 class="container">User not found.</h4>';
        }
    ?>
    
    <!-- Boostrap JS --><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>