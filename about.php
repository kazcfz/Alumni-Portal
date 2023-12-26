<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | About</title>

    <link rel="stylesheet" href="css/styles.css">

    <!-- Bootstrap CSS --><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons --><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <h1>About</h1>
    </div>

    <div class="container mb-5 py-4 px-4 mainView bg-white slide-left">
        <h4>What are the tasks not attempted or incomplete?</h4>
        <ul>
            <li>N/A. All tasks completed.</li>
        </ul>

        <h4 class="mt-5">Which parts did I have trouble with?</h4>
        <ul>
            <li>Tables. I had to learn how to implement DataTables and use jQuery to interact with it, but their documentation and forums were sufficient.</li>
        </ul>

        <h4 class="mt-5">What would I like to do better next time?</h4>
        <ul>
            <li>Use Axios for making requests within pages instead. This should eliminate the need to reload the page for minor changes.</li>
            <li>Use WebSocket to update table data and badge counters. This should retrieve the rapid changes made to the database by multiple users and admins, without the need to reload.</li>
        </ul>

        <h4 class="mt-5">What extension features/extra challenges have I done, or attempted, when creating the site?</h4>
        <ul>
            <h5>User pages</h5>
            <li>[<a href="view_events.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">view_events.php</a>] Implemented search function to search for events by any info.</li>
            <li>[<a href="view_events.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">view_events.php</a>] Implemented modal instead of JavaScript alert to display the popup messages when registering for events.</li>
            <br/>
            <li>[<a href="update_profile.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">update_profile.php</a>] Implemented features to Preview and Delete profile photo and resume that are uploaded.</li>
            <li>[<a href="update_profile.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">update_profile.php</a>] Implemented alerts for when any field is edited, or when profile photo/resume is added/updated/removed.</li>
            <br/>
            <li>[<a href="view_advertisements.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">view_advertisements.php</a>] Implemented status and category filter, and search function to retrieve advertisements by any info.</li>
            <li>[<a href="view_advertisements.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">view_advertisements.php</a>] Created the page for users to view advertisements.</li>
            <li>[<a href="advertisement_apply.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">advertisement_apply.php</a>] Created the page for users to apply for their selected advertisement. Fields are auto-filled and validations are implemented.</li>
            <br/>

            <h5>Admin pages</h5>
            <li>[<span class="fst-italic fw-light">All admin pages</span>] Implemented navigation items in the navigation bar to get to pages easier, and with current page indicator.</li>
            <li>[<span class="fst-italic fw-light">All admin pages</span>] Implemented breadcrumbs to indicate user's location from their current page.</li>
            <br/>
            <li>[<a href="manage_events.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">manage_events.php</a>] Implemented DataTables as a more powerful table tool. Features applied includes row display limit, ordering, pagination, fixed headers, expandable rows to display additional info.</li>
            <li>[<a href="manage_events.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">manage_events.php</a>] Implemented alerts for when an event/news is created/edited/removed.</li>
            <li>[<a href="add_event.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">add_event.php</a>] Styled the page to mimic what the created event/news would look like.</li>
            <li>[<a href="edit_event.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">edit_event.php</a>] Styled the page to mimic what the edited event/news would look like.</li>
            <br/>
            <li>[<a href="manage_accounts.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">manage_accounts.php</a>] Implemented DataTables as a more powerful table tool. Features applied includes row display limit, ordering, pagination, fixed headers, expandable rows to display additional info.</li>
            <li>[<a href="manage_accounts.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">manage_accounts.php</a>] Implemented badge in the navigation bar's <i class="bi bi-people"></i> (for all admin pages) to indicate the number of Pending user accounts.</li>
            <li>[<a href="manage_accounts.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">manage_accounts.php</a>] Implemented status filter and search function to retrieve users by any info.</li>
            <li>[<a href="manage_accounts.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">manage_accounts.php</a>] Implemented alerts for when a user is deleted.</li>
            <br/>
            <li>[<a href="manage_advertisements.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">manage_advertisements.php</a>] Created 'Manage Advertisements' page to manage all advertisements created in a DataTable.</li>
            <li>[<a href="manage_advertisements.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">manage_advertisements.php</a>] Implemented the feature and alert for when an advertisement is created/edited/removed.</li>
            <li>[<a href="add_advertisement.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">add_advertisement.php</a>] Created the page for admins to create new advertisements.</li>
            <li>[<a href="edit_advertisement.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">edit_advertisement.php</a>] Created 'Edit Advertisements' page to edit the selected advertisements.</li>
            <br/>
            <li>[<a href="maintenance.php" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover fst-italic fw-light">maintenance.php</a>] Created 'Maintenance' page to be redirected to when database connection fails, prevents outputting undesirable error messages and better handles user experience.</li>
            <br/>
            <li>[<span class="fst-italic fw-light">Database: advertisement_table</span>] Advertisement table to store each advertisement's ID, Title, Description, Date added, Button message, Button link, Photo path, category, status, advertiser, and Date to hide (hides the ad after this date).</li>
            <li>[<span class="fst-italic fw-light">Database: advertisement_registration_table</span>] Advertisement registration table to store each user's data applied to the advertisements linked by the advertisement's ID.</li>
        </ul>

        <h4 class="mt-5">Video presentation link.</h4>
        <ul>
            <li class="fst-italic"><a href="https://youtu.be/6UqgTSCfUrs" class="link-underline link-underline-opacity-0 link-underline-opacity-0 link-underline-opacity-75-hover">https://youtu.be/6UqgTSCfUrs</a></li>
        </ul>

        <div class="row justify-content-center"><div class="col-auto"><a role="button" class="btn btn-outline-primary mt-5 py-2" href="index.php">Return to Home</a></div></div>
    </div>
    
    <!-- Boostrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>