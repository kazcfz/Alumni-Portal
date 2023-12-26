<?php
    include 'db_controller.php';
    $conn->select_db("Alumni");

    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $errors = array();
        foreach ($_POST as $key => $value) {
            // Clean input
            if ($key != 'password' || $key != 'confirmPassword') {
                $_POST[$key] = trim($_POST[$key]); //remove extra spaces, tabs, newlines
                $value = trim($value);
            }

            // Check for required fields and validation
            include "validation_field.php";
        }
        // If there are invalid data, return to Registration page with its data, errors, verified fields
        if (!empty($errors)) {
            $_SESSION['form_data'] = $_POST;
            $_SESSION['errors'] = $errors;
            $_SESSION['verified'] = $verified;
            header('Location: registration.php');
        // If all data are valid, save to file and go to Login page
        } else {
            try {
                // [DB] Save to user_table
                $SQLRegisterUser = $conn->prepare("INSERT INTO user_table (email, first_name, last_name, dob, gender, hometown) VALUES (?, ?, ?, ?, ?, ?)");
                $SQLRegisterUser->bind_param("ssssss",$_POST['email'],$_POST['firstName'],$_POST['lastName'],$_POST['dob'],$_POST['gender'],$_POST['hometown']);

                // [DB] Save to account_table
                $hashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT); //hash password using BCrypt
                $SQLRegisterAccount = $conn->prepare("INSERT INTO account_table (email, password, type, status) VALUES (?, ?, 'user', 'Pending')");
                $SQLRegisterAccount->bind_param("ss",$_POST['email'],$hashedPassword);
            
                // Execute prepared statement and flash appropriate message on success/fail
                if ($SQLRegisterUser->execute() && $SQLRegisterAccount->execute() == true) {
                    $_SESSION['flash_mode'] = "alert-success";
                    $_SESSION['flash'] = "You have successfully registered.";
                    header('Location: login.php');
                } else {
                    $_SESSION['form_data'] = $_POST;
                    $_SESSION['errors'] = $errors;
                    $_SESSION['verified'] = $verified;
                    $_SESSION['flash_mode'] = "alert-warning";
                    $_SESSION['flash'] = "An error has occured while registering.";
                    header('Location: registration.php');
                }
            } catch (Exception $e) {
                $_SESSION['flash_mode'] = "alert-warning";
                $_SESSION['flash'] = "An error has occured while registering.";
                header('Location: registration.php');
            }
        }
    }
?>