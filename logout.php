<?php
    session_start();

    // Return flash message
    if (isset($_SESSION['logged_user']) || isset($_SESSION['logged_account'])) {
        $_SESSION['flash_mode'] = "alert-success";
        $_SESSION['flash'] = "You have successfully logged out.";
    }

    // Unset most Session objects (Flash messages are still needed)
    unset($_SESSION['logged_user']);
    unset($_SESSION['logged_account']);
    unset($_SESSION['form_data']);
    unset($_SESSION['login_errors']);
    unset($_SESSION['verified']);

    header('Location: main.php'); // Redirect to main
?>