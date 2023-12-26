<?php
    include 'db_controller.php';
    $conn->select_db("Alumni");

    // Validate firstName (Only letters and white space allowed)
    if ($key == 'firstName' && $value == '')
        $errors[$key] = '*First name is required.';
    elseif ($key == 'firstName' && !preg_match("/^[a-zA-Z-' ]*$/",$value))
        $errors[$key] = '*Only letters and white space allowed.';
    elseif ($key == 'firstName')
        $verified[$key] = true;

    // Validate lastName (Only letters and white space allowed)
    if ($key == 'lastName' && $value == '')
        $errors[$key] = '*Last name is required.';
    elseif ($key == 'lastName' && !preg_match("/^[a-zA-Z-' ]*$/",$value))
        $errors[$key] = '*Only letters and white space allowed.';
    elseif ($key == 'lastName')
        $verified[$key] = true;

    // Validate dob
    if ($key == 'dob' && $value == '')
        $errors[$key] = '*Date of Birth is required.';
    elseif ($key == 'dob' && (strtotime($value) > strtotime(date('Y-m-d'))))
        $errors[$key] = '*Date of Birth cannot be later than today.';
    elseif ($key == 'dob')
        $verified[$key] = true;

    // Validate gender
    if ($key == 'gender' && $value == '')
        $errors[$key] = '*Gender is required.';
    elseif ($key == 'gender')
        $verified[$key] = true;

    // Validate email (Check email format and registered email)
    if ($key == 'email' && $value == '')
        $errors[$key] = '*Email is required.';
    elseif ($key == 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL))
        $errors[$key] = "*Invalid email format";
    elseif ($key == 'email'){
        // [DB] Find duplicate email from account_table
        $SQLAccountTable = $conn->prepare("SELECT email FROM account_table WHERE email = ?");
        $SQLAccountTable->bind_param("s", $value);
        $SQLAccountTable->execute();
        if ($SQLAccountTable->get_result()->num_rows > 0) {
            $errors[$key] = '*Email is already registered.';
        } else {
            $verified[$key] = true;
        };
    }

    // Validate hometown
    if ($key == 'hometown' && $value == '')
        $errors[$key] = '*Hometown is required.';
    elseif ($key == 'hometown')
        $verified[$key] = true;

    // Validate password (at least 8 characters, 1 number, 1 symbol)
    if ($key == 'password' && $value == '')
        $errors[$key] = '*Password is required.';
    elseif ($key == 'password' && !preg_match('/^(?=.*[0-9])(?=.*[\W_]).{8,}$/', $value))
        $errors[$key] = "*Password must contain at least 8 characters, 1 number, 1 symbol";
    elseif ($key == 'password')
        $verified[$key] = true;
        
    // Validte confirmPassword (matching password)
    if ($key == 'confirmPassword' && $value == '')
        $errors[$key] = '*Please confirm your password.';
    elseif ($key == 'confirmPassword' && $_POST['password'] != $value)
        $errors[$key] = "*Password and confirm password do not match.";
    elseif ($key == 'confirmPassword')
        $verified[$key] = true;
?>