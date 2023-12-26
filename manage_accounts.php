<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Manage Events</title>

    <link rel="stylesheet" href="css/styles.css">

    <!-- Bootstrap CSS --><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons --><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Animate.css --><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- DataTables Bootstrap 5 --><link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
    <!-- DataTables Bootstrap 5 fixedHeader --><link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css" />
    <style>
        .card {
            width: 100%;
            border: none;
            box-shadow: 0 2px 2px rgba(0,0,0,.08), 0 0 6px rgba(0,0,0,.05);
        }

        /* Vertical align row cells to middle */
        td, th {
            vertical-align: middle;
        }

        .deleteButton:hover {
            background-color: firebrick;
            color: white;
        }

        .deleteButton {
            height: 142px;
            display: flex;
            align-items: center;
            border-radius: 0px;
            padding: 0;
            color: indianred;
            transition: background-color 0.15s, color 0.15s;
        }

        .deleteCell {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            padding-right: 0 !important;
        }
    </style>
</head>
<body class="admin-bg">
    <?php
        include 'db_controller.php';
        $conn->select_db("Alumni");

        session_start();

        include 'logged_admin.php';
    ?>

    <?php
        // POST request (also handles the deletes)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Assigns GET from POST (which was assigned from GET)
            if (isset($_POST['filterStatus']))
                $_GET['filterStatus'] = $_POST['filterStatus'];
            if (isset($_POST['search']))
                $_GET['search'] = $_POST['search'];

            // Set SQL statement depending on the action selected
            if (isset($_POST['email']) && isset($_POST['action'])) {
                if ($_POST['action'] == 'approve')
                    $SQLUpdateAccount = "UPDATE account_table SET status = 'Approved' WHERE email = ?";

                elseif ($_POST['action'] == 'reject')
                    $SQLUpdateAccount = "UPDATE account_table SET status = 'Rejected' WHERE email = ?";

                elseif ($_POST['action'] == 'delete')
                    $SQLUpdateAccount = "DELETE FROM user_table WHERE email = ?";

                // Modify the user in db
                try {
                    $SQLUpdateAccount = $conn->prepare($SQLUpdateAccount);
                    $SQLUpdateAccount->bind_param("s", $_POST['email']);

                    if ($SQLUpdateAccount->execute()) {
                        // Flash message for delete
                        if ($_POST['action'] == 'delete'){
                            $_SESSION['flash_mode'] = "alert-success";
                            $_SESSION['flash'] = "User <span class='fw-medium'>".$_POST['name']."</span> removed successfully.";
                        }

                        // Update Pending counter for approve/reject for navbar badge
                        $rows = $conn->query("SELECT COUNT(*) AS count FROM account_table WHERE status = 'Pending' AND type != 'admin'");
                        if ($rows) {
                            $rowResults = $rows->fetch_assoc();
                            $pendingCount = $rowResults['count'];
                        }
                    } else {
                        // Flash message for delete
                        if ($_POST['action'] == 'delete'){
                            $_SESSION['flash_mode'] = "alert-warning";
                            $_SESSION['flash'] = "An error has occured removing User <span class='fw-medium'>".$_POST['name']."</span>.";
                        }
                    }
                } catch (Exception $e) {
                    $_SESSION['flash_mode'] = "alert-warning";
                    $_SESSION['flash'] = "An error has occured modifying User <span class='fw-medium'>".$_POST['name']."</span>.";
                }
            }
        }

        // Prepare flash message
        $tempFlash;
        if (isset($_SESSION['flash_mode'])){
            $tempFlash = $_SESSION['flash_mode'];
            unset($_SESSION['flash_mode']);
        }
    ?>

    <!-- Top nav bar -->
    <nav class="navbar sticky-top navbar-expand-lg" style="background-color: #002c59;">
        <div class="container">
            <a class="navbar-brand mx-0 mb-0 h1 text-light" href="main_menu_admin.php">Alumni Portal</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse me-5" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item mx-1">
                        <a class="nav-link nav-admin-link px-5" href="main_menu_admin.php"><i class="bi bi-house-door nav-bi-admin"></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link nav-admin-link nav-main-admin-active px-5" href="manage_accounts.php"><i class="bi bi-people-fill nav-bi position-relative">
                            <?php if (isset($pendingCount) && $pendingCount > 0) { ?> <span class="position-absolute top-0 start-100 badge rounded-pill bg-danger fst-normal fw-medium small-badge"><?php echo $pendingCount; ?></span><?php } ?>
                        </i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link nav-admin-link px-5" aria-current="page" href="manage_events.php"><i class="bi bi-calendar-event nav-bi-admin"></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link nav-admin-link px-5" href="manage_advertisements.php"><i class="bi bi-megaphone nav-bi-admin"></i></a>
                    </li>
                </ul>
            </div>
            <?php include 'nav_user.php' ?>
        </div>
    </nav>

    <!-- Breadcrumbs -->
    <div class="container my-3">
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="breadcrumb-link text-secondary link-underline link-underline-opacity-0" href="main_menu_admin.php">Home</a></li>
                <li class="breadcrumb-item breadcrumb-active" aria-current="page">Manage Accounts</li>
            </ol>
        </nav>
    </div>

    <!-- Flash message -->
    <div class="container-fluid">
        <?php if (isset($_SESSION['flash_mode']) || isset($tempFlash)){ ?>
            <div class="row justify-content-center position-absolute top-1 start-50 translate-middle">
                <div class="col-auto">
                    <div class="alert <?php echo (isset($_SESSION['flash_mode'])) ? $_SESSION['flash_mode'] : '' . (isset($tempFlash) ? $tempFlash : ''); ?> mt-4 py-2 fade-out-alert row align-items-center" role="alert">
                        <i class="bi <?php echo (isset($tempFlash) && $tempFlash == "alert-success" ? "bi-check-circle" : ((isset($tempFlash) && ($tempFlash == "alert-primary" || $tempFlash == "alert-secondary") ? "bi-info-circle" : ((isset($tempFlash) && $tempFlash == "alert-warning" ? "bi-exclamation-triangle" : ""))))) ?> login-bi col-auto px-0"></i><div class="col ms-1"><?php echo isset($_SESSION['flash']) ? $_SESSION['flash'] : '' ?></div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="row justify-content-center position-absolute top-1 start-50 translate-middle">
            <div class="col-auto">
                <div id="flash-message-container"></div> <!-- Special JS alert for delete -->
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row <?php echo ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['filterStatus']) || isset($_GET['search'])) ? NULL : 'slide-left' ?>">
            <div class="col"><h1>Manage Users' Account</h1></div>
        </div>

        <!-- Filter -->
        <div class="container card mt-3 py-3 ps-0 bg-white fw-medium <?php echo ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['filterStatus']) || isset($_GET['search'])) ? NULL : 'slide-left' ?>">
            <form id="eventsFilterForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
                <!-- Status (Pending, Approved, Rejected) -->
                <div class="form-check-inline ms-4">
                    <div class="input-group">
                        <label class="input-group-text" for="filterStatus"><i class="bi bi-funnel" style="-webkit-text-stroke: 0.25px;"></i></label>
                        <select class="form-select fw-medium <?php echo (isset($_GET['filterStatus']) && $_GET['filterStatus'] == 'Approved') ? 'text-success' : ((isset($_GET['filterStatus']) && $_GET['filterStatus'] == 'Rejected') ? 'text-danger' : ((isset($_GET['filterStatus']) && $_GET['filterStatus'] == 'Pending') ? 'text-dark' : 'text-dark')) ?>" id="filterStatus" name="filterStatus" aria-label="Time filter" onchange="changedSelectOptionColor()">
                            <option value="All" class="fw-medium text-dark" <?php echo (isset($_GET['filterStatus']) && $_GET['filterStatus'] == 'All') ? 'selected' : NULL ?>>All</option>
                            <option value="Pending" class="fw-medium text-dark" <?php echo (isset($_GET['filterStatus']) && $_GET['filterStatus'] == 'Pending') ? 'selected' : (!isset($_GET['filterStatus']) ? 'selected' : NULL) ?>>Pending</option>
                            <option value="Approved" class="fw-medium text-success" <?php echo (isset($_GET['filterStatus']) && $_GET['filterStatus'] == 'Approved') ? 'selected' : NULL ?>>Approved</option>
                            <option value="Rejected" class="fw-medium text-danger" <?php echo (isset($_GET['filterStatus']) && $_GET['filterStatus'] == 'Rejected') ? 'selected' : NULL ?>>Rejected</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-outline-primary fw-medium mb-1">Display List</button>

                <!-- Search box -->
                <div class="form-check-inline me-0 float-end">
                    <div class="input-group">
                        <input type="text" class="form-control py-2" placeholder="Search users" name="search" aria-label="Search" aria-describedby="button-addon2" value="<?php echo (isset($_GET['search'])) ? trim($_GET['search']) : NULL; ?>">
                        <button class="btn btn-outline-primary px-3 py-2" type="submit" id="button-addon2"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table (DataTables) -->
        <div class="table-container table-responsive px-5 pt-4 pb-5 mt-3 <?php echo ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['filterStatus']) || isset($_GET['search'])) ? NULL : '' ?>">
            <?php 
                $filterStatus = "";
                $filterSearch = "";

                // Status filter for Status
                if (isset($_GET['filterStatus']) && $_GET['filterStatus'] != 'All')
                    $filterStatus = "account_table.status = '" . $_GET['filterStatus'] . "'";
                elseif (!isset($_GET['filterStatus']))
                    $filterStatus = "account_table.status = 'Pending'";

                // Search filter for Users
                if (isset($_GET['search']) && $_GET['search'] != "") {
                    $trimSearch = strtolower(trim($_GET['search']));
                    $filterSearch = "(
                        LOWER(user_table.email) LIKE '%$trimSearch%' OR 
                        LOWER(user_table.first_name) LIKE '%$trimSearch%' OR 
                        LOWER(user_table.last_name) LIKE '%$trimSearch%' OR 
                        LOWER(user_table.dob) LIKE '%$trimSearch%' OR 
                        DATE_FORMAT(user_table.dob, '%d/%m/%Y') LIKE '%$trimSearch%' OR 
                        LOWER(user_table.gender) LIKE '%$trimSearch%' OR 
                        LOWER(user_table.contact_number) LIKE '%$trimSearch%' OR 
                        LOWER(user_table.hometown) LIKE '%$trimSearch%' OR 
                        LOWER(user_table.current_location) LIKE '%$trimSearch%' OR 
                        LOWER(user_table.job_position) LIKE '%$trimSearch%' OR 
                        LOWER(user_table.qualification) LIKE '%$trimSearch%' OR 
                        LOWER(user_table.year) LIKE '%$trimSearch%' OR 
                        LOWER(user_table.university) LIKE '%$trimSearch%' OR 
                        LOWER(user_table.company) LIKE '%$trimSearch%'
                    )";
                }

                // Puts WHERE and AND to the query appropriately
                $conditions = array_filter([$filterStatus, $filterSearch]);
                if (!empty($conditions))
                    $whereClause = "WHERE " . implode(" AND ", $conditions);
                else
                    $whereClause = "";

                // Query the database with the WHERE from previous
                $allEventsNews = $conn->query("SELECT user_table.*, account_table.type, account_table.status FROM user_table 
                    JOIN account_table 
                    ON user_table.email = account_table.email 
                    $whereClause 
                    AND account_table.type != 'admin'
                ");
                $conn->close();
            ?>

            <!-- Table -->
            <table id="eventTable" class="table table-hover">
                <thead>
                    <tr class="table-primary fs-5">
                        <th class="pe-0">Profile Image</th>
                        <th class="pe-5">Name</th>
                        <th class="pe-5">Hometown</th>
                        <th class="pe-5">Email</th>
                        <th class="pe-5"></th>
                        <th class="ms-4 pe-0"></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    
    <!-- Boostrap JS --><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- jQuery --><script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- DataTables --><script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
    <!-- DataTables Bootstrap 5 --><script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Moment.js --><script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <!-- DataTables Bootstrap 5 fixedHeader --><script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
    <!-- Axios --><script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // DataTables init
        $('#eventTable').DataTable({
            paging: true,
            lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
            info: true,
            searching: false,
            responsive: true,
            fixedHeader: {
                header: true,
                headerOffset: $('.navbar').height()
            },
            pagingType: 'full_numbers',
            order: [[1, "asc"]],
            columnDefs: [
                {
                    "targets": [0, 4, 5],
                    "orderable": false,
                },
                {
                    "targets": 5,
                    "className": 'deleteCell',
                },
            ],
        });

        var table = $('#eventTable').DataTable();
        <?php
            // Populate the DataTables with data from DB
            foreach ($allEventsNews as $row) {
                // Show profile image, otherwise use default photo
                if (isset($row['profile_image']))
                    $profileImage = '<div class="image-table-container"><img src="profile_images/'.$row['profile_image'].'" class="img-fluid" alt="profile_picture"></div>';
                elseif ($row['gender'] == "Male")
                    $profileImage = '<div class="image-table-container"><img src="profile_images/default-male-user-profile-icon.jpg" class="img-fluid" alt="profile_picture"></div>';
                elseif ($row['gender'] == "Female")
                    $profileImage = '<div class="image-table-container"><img src="profile_images/default-female-user-profile-icon.jpg" class="img-fluid" alt="profile_picture"></div>';

                // Approve Reject buttons styling and text
                if ($row['status'] == "Approved"){
                    $approveBtnClass = "btn-success disabled";
                    $approveBtnText = "Approved";
                    $rejectBtnClass = "btn-outline-danger";
                    $rejectBtnText = "Reject";
                } elseif ($row['status'] == "Rejected"){
                    $approveBtnClass = "btn-outline-success";
                    $approveBtnText = "Approve";
                    $rejectBtnClass = "btn-danger disabled";
                    $rejectBtnText = "Rejected";
                } elseif ($row['status'] == "Pending") {
                    $approveBtnClass = "btn-outline-success";
                    $approveBtnText = "Approve";
                    $rejectBtnClass = "btn-outline-danger";
                    $rejectBtnText = "Reject";
                }

                // Retrieve GET values (filters) and POST them to retain the filtered results
                $POSTtheGET = '';
                if (isset($_GET['filterStatus']))
                    $POSTtheGET .= "<input type='hidden' name='filterStatus' value='".htmlspecialchars($_GET['filterStatus'])."'>";
                if (isset($_GET['search']))
                    $POSTtheGET .= "<input type='hidden' name='search' value='".htmlspecialchars($_GET['search'])."'>";

                // Initialize Approve & Reject buttons
                $approveReject = "<div class='d-inline-flex gap-2'>
                    <form action='".htmlspecialchars($_SERVER["PHP_SELF"])."' method='POST'>
                        <input type='hidden' name='email' value='".htmlspecialchars($row['email'])."'>
                        <input type='hidden' name='action' value='approve'>
                        ".$POSTtheGET."
                        <button type='submit' class='btn ".$approveBtnClass."'><div class='d-inline-flex align-middle'><i class='bi bi-check-lg me-2' style='-webkit-text-stroke: 0.25px;'></i>".$approveBtnText."</div></button>
                    </form>
                    <form action='".htmlspecialchars($_SERVER["PHP_SELF"])."' method='POST'>
                        <input type='hidden' name='email' value='".htmlspecialchars($row['email'])."'>
                        <input type='hidden' name='action' value='reject'>
                        ".$POSTtheGET."
                        <button type='submit' class='btn ".$rejectBtnClass."'><div class='d-inline-flex align-middle'><i class='bi bi-x me-1' style='-webkit-text-stroke: 0.5px;'></i>".$rejectBtnText."</div></button>
                    </form>
                </div>";

                // Delete button
                $delete = "<form action='".htmlspecialchars($_SERVER["PHP_SELF"])."' method='POST'>
                    <input type='hidden' name='email' value='".htmlspecialchars($row['email'])."'>
                    <input type='hidden' name='name' value='".htmlspecialchars($row['first_name'].' '.$row['last_name'])."'>
                    <input type='hidden' name='action' value='delete'>
                    ".$POSTtheGET."
                    <button type='submit' class='btn deleteButton ms-4'><i class='bi bi-trash mx-2' style='font-size: 1.5em;'></i></button>
                </form>";
                
                // Fade-in for GET. Normal for POST. Add the row
                if ($_SERVER['REQUEST_METHOD'] == 'GET')
                    echo "table.row.add(['$profileImage', '{$row['first_name']} {$row['last_name']}', '{$row['hometown']}', '{$row['email']}', `$approveReject`, `$delete`]).draw().nodes().to$().hide().fadeIn();\n";
                if ($_SERVER['REQUEST_METHOD'] == 'POST')
                    echo "table.row.add(['$profileImage', '{$row['first_name']} {$row['last_name']}', '{$row['hometown']}', '{$row['email']}', `$approveReject`, `$delete`]).draw();\n";
            }
        ?>

        // Change text color according to selected option (JS onchange)
        function changedSelectOptionColor() {
            var selectElement = document.getElementById('filterStatus');
            var selectedValue = selectElement.value;

            // Check the selected value and add the class name "text-dark" if it's "All"
            selectElement.classList.remove('text-dark', 'text-warning', 'text-success', 'text-danger');
            if (selectedValue == 'All')
                selectElement.classList.add('text-dark');
            else if (selectedValue == 'Pending')
                selectElement.classList.add('text-dark');
            else if (selectedValue == 'Approved')
                selectElement.classList.add('text-success');
            else if (selectedValue == 'Rejected')
                selectElement.classList.add('text-danger');
        };

        // JS alert function
        function displayFlashMessage(mode, message) {
            const flashMessageContainer = document.getElementById("flash-message-container");
            flashMessageContainer.innerHTML = `
                <div class="alert ${mode} mt-4 py-2 fade-out-alert row align-items-center" role="alert">
                    <i class="bi ${mode === 'alert-success' ? "bi-check-circle" : (mode === 'alert-primary' || mode === 'alert-secondary' ? "bi-info-circle" : (mode === 'alert-warning' ? "bi-exclamation-triangle" : ""))} login-bi col-auto px-0"></i>
                    <div class="col ms-1">${message}</div>
                </div>
            `;

            setTimeout(() => {
                flashMessageContainer.innerHTML = '';
            }, 5000);
        }
        
        // Ignore confirm form resubmission after POST request to register event
        if (window.history.replaceState)
            window.history.replaceState(null, null, window.location.href);
    </script>
</body>
</html>