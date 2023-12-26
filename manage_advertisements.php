<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Manage Advertisement</title>

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
    </style>
</head>
<body class="admin-bg">
    <?php
        include 'db_controller.php';
        $conn->select_db("Alumni");

        session_start();

        include 'logged_admin.php';
    ?>

    <!-- Top navigation bar -->
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
                        <a class="nav-link nav-admin-link px-5" href="manage_accounts.php"><i class="bi bi-people nav-bi-admin position-relative">
                            <?php if (isset($pendingCount) && $pendingCount > 0) { ?> <span class="position-absolute top-0 start-100 badge rounded-pill bg-danger fst-normal fw-medium small-badge"><?php echo $pendingCount; ?></span><?php } ?>
                        </i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link nav-admin-link px-5" aria-current="page" href="manage_events.php"><i class="bi bi-calendar-event nav-bi-admin"></i></a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link nav-admin-link nav-main-admin-active px-5" href="manage_advertisements.php"><i class="bi bi-megaphone-fill nav-bi"></i></a>
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
                <li class="breadcrumb-item"><a class="breadcrumb-link text-secondary link-underline link-underline-opacity-0" href="main_menu_admin.php">Home</a></li>
                <li class="breadcrumb-item breadcrumb-active" aria-current="page">Manage Advertisements</li>
            </ol>
        </nav>
    </div>

    <?php
        // POST request (also handles the deletes)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['filterStatus']))
                $_GET['filterStatus'] = $_POST['filterStatus'];
            if (isset($_POST['search']))
                $_GET['search'] = $_POST['search'];

            if (isset($_POST['id']) && isset($_POST['action'])) {
                if ($_POST['action'] == 'delete') {
                    $id = $_POST['id'];

                    // Remove from storage
                    $uploadDir = "images/";
                    $result = $conn->query("SELECT * FROM advertisement_table WHERE id = $id");
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        if ($row['photo'] != "default_advertisement.jpg" && file_exists($uploadDir . $row['photo']))
                            unlink($uploadDir.$row['photo']);
    
                        // Delete row from table
                        if ($conn->query("DELETE FROM advertisement_table WHERE id = $id")) {
                            $_SESSION['flash_mode'] = "alert-success";
                            $_SESSION['flash'] = "<span class='fw-medium'>Advertisement ".$row['id']."</span> deleted successfully.";
                        } else {
                            $_SESSION['flash_mode'] = "alert-warning";
                            $_SESSION['flash'] = "An error has occured deleting <span class='fw-medium'>Advertisement ".$row['id']."</span>";
                        }
                    }
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

    <!-- Flash message -->
    <div class="container-fluid">
        <?php if (isset($_SESSION['flash_mode']) || isset($tempFlash)){ ?>
            <div class="row justify-content-center position-absolute top-1 start-50 translate-middle">
                <div class="col-auto">
                    <div class="alert <?php echo (isset($_SESSION['flash_mode'])) ? $_SESSION['flash_mode'] : '' . (isset($tempFlash) ? $tempFlash : ''); ?> mt-4 py-2 fade-out-alert row align-items-center" role="alert">
                        <i class="bi <?php echo (isset($tempFlash) && $tempFlash == "alert-success" ? "bi-check-circle" : ((isset($tempFlash) && ($tempFlash == "alert-primary" || $tempFlash == "alert-secondary") ? "bi-info-circle" : ((isset($tempFlash) && $tempFlash == "alert-warning" ? "bi-exclamation-triangle" : ""))))) ?> login-bi col-auto px-0"></i><div class="col ms-1"><?php echo isset($_SESSION['flash']) ? $_SESSION['flash'] : '' ?></div>
                    </div>
                    <div id="flash-message-container"></div> <!-- Special JS alert for delete -->
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="container mb-5">
        <div class="row <?php echo (isset($_POST['eventID']) || isset($_GET['filterType']) || isset($_GET['filterTime']) || isset($_GET['search'])) ? NULL : 'slide-left' ?>">
            <div class="col"><h1>Manage Advertisements</h1></div>
            <div class="col-auto align-self-center"><a role="button" href="add_advertisement.php" class="btn btn-primary fw-medium px-4 py-2"><i class="bi bi-plus-lg me-2" style="-webkit-text-stroke: 0.25px;"></i>Add Advertisements</a></div>
        </div>

        <!-- Filter -->
        <div class="container mt-3 py-3 px-4 card bg-white fw-medium <?php echo (isset($_POST['eventID']) || isset($_GET['filterStatus']) || isset($_GET['filterCategory']) || isset($_GET['search'])) ? NULL : 'slide-left' ?>">
            <form id="eventsFilterForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
                <!-- Status (All, Active, Inactive) -->
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="filterStatus" id="filterStatus1" value="All" <?php echo (isset($_GET['filterStatus']) && $_GET['filterStatus'] == 'All') ? 'checked' : ((!isset($_GET['filterCategory'])) ? 'checked' : NULL ) ?>>
                    <label class="form-check-label" for="filterStatus1">All</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="filterStatus" id="filterStatus2" value="Active" <?php echo (isset($_GET['filterStatus']) && $_GET['filterStatus'] == 'Active') ? 'checked' : NULL ?>>
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

        <!-- Table (DataTables) -->
        <div class="table-container table-responsive px-5 pt-4 pb-5 mt-3 <?php echo (isset($_POST['eventID']) || isset($_GET['filterType']) || isset($_GET['filterTime']) || isset($_GET['search'])) ? NULL : "" ?>">
            <?php 
                $filterStatus = "";
                $filterCategory = "";
                $filterSearch = "";

                // Type filter for Events/News
                if (isset($_GET['filterStatus']) && $_GET['filterStatus'] != 'All')
                    $filterStatus = "status = '" . $_GET['filterStatus'] . "'";

                // Time filter for Events/News
                if (isset($_GET['filterCategory']) && $_GET['filterCategory'] != 'All') {
                    $filterCategory = "category = '" . $_GET['filterCategory'] . "'";
                }

                // Search filter for Events/News
                if (isset($_GET['search']) && $_GET['search'] != "") {
                    $trimSearch = strtolower(trim($_GET['search']));
                    $filterSearch = "(
                        LOWER(id) LIKE '%$trimSearch%' OR 
                        LOWER(title) LIKE '%$trimSearch%' OR 
                        LOWER(description) LIKE '%$trimSearch%' OR 
                        LOWER(date_added) LIKE '%$trimSearch%' OR 
                        DATE_FORMAT(date_added, '%d/%m/%Y') LIKE '%$trimSearch%' OR 
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

                // Retrieve from db
                $allAdvertisements = $conn->query("SELECT * FROM advertisement_table $whereClause ORDER BY id DESC");
                $conn->close();
            ?>

            <table id="eventTable" class="table table-hover">
                <thead>
                    <tr class="table-primary fs-5">
                        <th class="pe-4"></th>
                        <th>#</th>
                        <th class="pe-5">Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date Added</th>
                        <th></th>
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
        // Display description when opening rows' slider
        function format(row) {
            // If custom button is set
            if (row[10] != "" || row[9] != "")
                buttonMsgLink = '<a role="button" class="btn btn-primary ms-4 fw-medium px-4" href="'+row[10]+'" target="_blank">'+row[9]+'<i class="bi bi-box-arrow-up-right ms-2" style="-webkit-text-stroke: 0.25px;"></i></a>\
                    <span class="ms-2 fst-italic fw-light text-secondary">(<a class="link-underline link-underline-opacity-0 link-underline-opacity-75-hover" href="'+row[10]+'" target="_blank">'+row[10]+'</a>)</span><br/>';
            else
                buttonMsgLink = "";

            if (row[12] == 1)
                $appliable = '<span>*User application enabled</span><br/>';
            else
                $appliable = '';

            return ('<div class="slider">\
                <div class="row">\
                    <div class="col-auto ms-3 my-3 me-0 pe-0">\
                        <div class="image-table-container">\
                            <img src="images/'+row[8]+'" class="img-fluid" alt="ad_photo">\
                        </div>\
                    </div>\
                    <div class="col mx-0 px-0">\
                        <p class="fw-light ps-4 pe-5 pt-2 pb-3">'+row[7]+'</p>\
                        '+buttonMsgLink+'\
                        <p class="fst-italic fw-light mt-3 text-end me-5">'
                            +$appliable+'\
                            Added by: \
                            <a class="link-underline link-underline-opacity-0 link-underline-opacity-75-hover" href="mailto:'+row[11]+'">'+row[11]+'</a>\
                        </p>\
                    </div>\
                </div>\
            </div>');
        }

        // DataTables init
        DataTable.datetime('D/MM/Y');
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
            order: [[1, "desc"]],
            columnDefs: [
                {
                    "targets": [0, 6],
                    "orderable": false,
                },
                {
                    "targets": '_all',
                    "createdCell": function (td, cellData, rowData, row, col) {
                        $(td).css('padding', '15px');
                    }
                },
            ],
        });

        // Onclick listener for the description displayer
        $('#eventTable tbody').on('click', 'a.dt-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
        
            if (row.child.isShown()) {
                // This row is already open - close it
                $('div.slider', row.child()).slideUp( function () {
                    row.child.hide();
                    tr.removeClass('shown');
                });
                $(this).html('<i class="bi bi-chevron-down"></i>');
            }
            else {
                // Open this row
                row.child( format(row.data()), 'no-padding' ).show();
                tr.addClass('shown');
                $('div.slider', row.child()).slideDown();
                $(this).html('<i class="bi bi-chevron-up"></i>');
            }
        });
        
        var table = $('#eventTable').DataTable();
        <?php
            // Retrieve GET values (filters) and POST them to retain the filtered results
            $POSTtheGET = '';
            if (isset($_GET['filterStatus']))
                $POSTtheGET .= "<input type='hidden' name='filterStatus' value='".htmlspecialchars($_GET['filterStatus'])."'>";
            if (isset($_GET['search']))
                $POSTtheGET .= "<input type='hidden' name='search' value='".htmlspecialchars($_GET['search'])."'>";

            // Populate the DataTables with data from DB
            foreach ($allAdvertisements as $row) {
                // Format date
                $adDate = date('m/d/Y', strtotime($row['date_added']));
                echo "var adDate = new Date('{$adDate}');\n";
                
                // Dropdown actions to edit and delete
                $actionDropdown = '
                    <div class="dropstart me-4">
                        <div class="float-end" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots text-secondary"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-normal mt-1 px-2">
                            <li><form action="edit_advertisement.php" method="GET">
                                <input type="hidden" name="id" value="'.$row['id'].'">
                                <button type="submit" class="dropdown-item py-2 pe-5"><i class="bi bi-pencil me-3" style="font-size: 1.25rem; -webkit-text-stroke: 0.25px;"></i><div class="fw-medium">Edit</div></button>
                            </a></form></li>
                            <li><form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="POST">
                                <input type="hidden" name="id" value="'.htmlspecialchars($row['id']).'">
                                <input type="hidden" name="action" value="delete">
                                '.$POSTtheGET.'
                                <button type="submit" class="dropdown-item py-2 pe-5">
                                <i class="bi bi-trash me-3 text-danger" style="font-size: 1.25rem; -webkit-text-stroke: 0.25px;"></i><div class="fw-medium text-danger">Delete</div>
                            </button></li>
                        </ul>
                    </div>
                ';

                // Set badges for Active and Inactive
                if ($row['status'] == "Active")
                    $status = '<span class="form-check-label badge text-bg-success" for="filterType2">Active</span>';
                elseif ($row['status'] == "Inactive")
                    $status = '<span class="form-check-label badge text-bg-secondary" for="filterType3">Inactive</span>';

                $category = '<span class="form-check-label badge text-bg-info" for="filterType2">'.$row['category'].'</span>';

                // Expandable rows to display more information
                $expandCollapse = '<a class="dt-control text-secondary" style="cursor: pointer;"><i class="bi bi-chevron-down"></i></a>';

                // Add the row
                echo "table.row.add([`$expandCollapse`, {$row['id']}, '{$row['title']}', `$category`, `$status`, '".date('d/m/Y', strtotime($row['date_added']))."', `$actionDropdown`, '{$row['description']}', '{$row['photo']}', '{$row['button_message']}', '{$row['button_link']}', '{$row['advertiser']}', '{$row['appliable']}', '{$row['date_to_hide']}']).draw().nodes().to$().hide().fadeIn();\n";
            }
        ?>

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