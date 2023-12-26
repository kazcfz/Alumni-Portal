<!-- Ensures account type 'admin' is logged in to access any page that includes this -->

<?php
    include 'db_controller.php';
    $conn->select_db("Alumni");

    if(!isset($_SESSION['logged_account']) || (isset($_SESSION['logged_account']) && $_SESSION['logged_account']['type'] != 'admin')){
        // Logs the user out to prevent infinite redirect from login page
        unset($_SESSION['logged_user']);
        unset($_SESSION['logged_account']);
        unset($_SESSION['form_data']);
        unset($_SESSION['login_errors']);
        unset($_SESSION['verified']);
        
        $_SESSION['flash_mode'] = "alert-primary";
        $_SESSION['flash'] = "You must log in as admin to continue.";
        // $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        die();
    } else {
        // Pending counter for approve/reject for navbar badge
        $rows = $conn->query("SELECT COUNT(*) AS count FROM account_table WHERE status = 'Pending' AND type != 'admin'");
        if ($rows) {
            $rowResults = $rows->fetch_assoc();
            $pendingCount = $rowResults['count'];
        }
    }
?>