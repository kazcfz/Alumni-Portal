<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Main Menu Admin</title>

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
<body class="admin-bg">
    <?php
        session_start();

        include 'logged_admin.php';
    ?>

    <!-- Top nav bar -->
    <nav class="navbar sticky-top navbar-expand-lg mb-5" style="background-color: #002c59;">
        <div class="container">
            <a class="navbar-brand mx-0 mb-0 h1 text-light" href="main_menu_admin.php">Alumni Portal</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse me-5" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item mx-1">
                        <a class="nav-link nav-admin-link nav-main-admin-active px-5" aria-current="page" href="main_menu_admin.php"><i class="bi bi-house-door-fill nav-bi"></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link nav-admin-link px-5" href="manage_accounts.php"><i class="bi bi-people nav-bi-admin position-relative">
                            <?php if (isset($pendingCount) && $pendingCount > 0) { ?> <span class="position-absolute top-0 start-100 badge rounded-pill bg-danger fst-normal fw-medium small-badge"><?php echo $pendingCount; ?></span><?php } ?>
                        </i></a>
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

    <div class="container slide-left">
        <div class="row justify-content-center">
            
            <!-- Manage Events/News -->
            <div class="col-auto mb-5">
                <div class="card text-center">
                    <img src="images/manage_event.PNG" class="card-img-top" alt="Social Event">
                    <div class="card-body">
                        <h5 class="card-title">Events/News</h5>
                        <p class="card-text">Post and manage events/news to publish the latest updates</p>
                    </div>
                    <div class="d-grid gap-2"> <a role="button" class="btn card-btn btn-primary fw-medium py-2" href="manage_events.php">Manage Events/News</a> </div>
                </div>
            </div>

            <!-- Manage User Accounts -->
            <div class="col-auto mb-5 mx-5">
                <div class="card text-center">
                    <img src="images/manage_account.PNG" class="card-img-top" alt="Social Image">
                    <div class="card-body">
                        <h5 class="card-title">User Accounts</h5>
                        <p class="card-text">Manage user account to bolster security measures</p>
                    </div>
                    <div class="d-grid gap-2"> <a role="button" class="btn card-btn btn-primary fw-medium py-2" href="manage_accounts.php">Manage Accounts</a> </div>
                </div>
            </div>

            <!-- Manage Advertisements -->
            <div class="col-auto mb-5">
                <div class="card text-center">
                    <img src="images/day_of_service.PNG" class="card-img-top" alt="Advertisement Photo">
                    <div class="card-body">
                        <h5 class="card-title">Advertisements</h5>
                        <p class="card-text">Manage exclusive job listings, workshops, seminars to nurture others' professional growth</p>
                    </div>
                    <div class="d-grid gap-2"> <a role="button" class="btn card-btn btn-primary fw-medium py-2" href="manage_advertisements.php">Manage Advertisements</a> </div>
                </div>
            </div>
        </div>

    </div>
    
    <!-- Boostrap JS --><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>