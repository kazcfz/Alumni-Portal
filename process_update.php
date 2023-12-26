<?php
    session_start();
    include 'logged_user.php';
    include 'db_controller.php';
    $conn->select_db("Alumni");

    // DELETE request
    if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        // Delete alumnus' profile picture
        if ($data['alumnusToViewProfilePicture'] !== null) {
            $alumnusToViewProfilePicture = $data['alumnusToViewProfilePicture'];

            // Remove from storage
            $uploadDir = "profile_images/";
            if (file_exists($uploadDir . $alumnusToViewProfilePicture))
                unlink($uploadDir.$alumnusToViewProfilePicture);

            // Remove from user_table (Alumnus' row)
            try {
                if ($conn->query("UPDATE user_table SET profile_image = NULL WHERE profile_image = '$alumnusToViewProfilePicture'")) {
                    // Save updated user data
                    $SQLGetUserInfo = $conn->prepare("SELECT * FROM user_table WHERE email = ?");
                    $SQLGetUserInfo->bind_param("s", $_SESSION['logged_account']['email']);
                    $SQLGetUserInfo->execute();
                    $userInfo = $SQLGetUserInfo->get_result()->fetch_assoc();
                    $SQLGetUserInfo->close();
                    $_SESSION['logged_user'] = $userInfo;

                    // Save updated account data
                    $SQLGetAccountInfo = $conn->prepare("SELECT email, type FROM account_table WHERE email = ?");
                    $SQLGetAccountInfo->bind_param("s", $_SESSION['logged_account']['email']);
                    $SQLGetAccountInfo->execute();
                    $accountInfo = $SQLGetAccountInfo->get_result()->fetch_assoc();
                    $_SESSION['logged_account'] = $accountInfo;

                    // Prepare flash message
                    $_SESSION['flash_mode'] = "alert-success";
                    $_SESSION['flash'] = "Profile Picture successfully removed.";
                    header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                } else {
                    $_SESSION['flash_mode'] = "alert-warning";
                    $_SESSION['flash'] = "An error has occured while removing your Profile Picture.";
                    header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                }
            } catch (Exception $e) {
                $_SESSION['flash_mode'] = "alert-warning";
                $_SESSION['flash'] = "An error has occured while removing your Profile Picture.";
                header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
            }
        }

        // Delete alumnus' resume
        elseif ($data['alumnusToViewResume'] !== null) {
            $alumnusToViewResume = $data['alumnusToViewResume'];

            // Remove from storage
            $uploadDir = "resume/";
            if (file_exists($uploadDir . $alumnusToViewResume))
                unlink($uploadDir.$alumnusToViewResume);

            // Remove from user_table (Alumnus' row)
            try {
                if ($conn->query("UPDATE user_table SET resume = NULL WHERE resume = '$alumnusToViewResume'")) {
                    // Save updated user data
                    $SQLGetUserInfo = $conn->prepare("SELECT * FROM user_table WHERE email = ?");
                    $SQLGetUserInfo->bind_param("s", $_SESSION['logged_account']['email']);
                    $SQLGetUserInfo->execute();
                    $userInfo = $SQLGetUserInfo->get_result()->fetch_assoc();
                    $SQLGetUserInfo->close();
                    $_SESSION['logged_user'] = $userInfo;

                    // Save updated account data
                    $SQLGetAccountInfo = $conn->prepare("SELECT email, type FROM account_table WHERE email = ?");
                    $SQLGetAccountInfo->bind_param("s", $_SESSION['logged_account']['email']);
                    $SQLGetAccountInfo->execute();
                    $accountInfo = $SQLGetAccountInfo->get_result()->fetch_assoc();
                    $_SESSION['logged_account'] = $accountInfo;

                    // Prepare flash message
                    $_SESSION['flash_mode'] = "alert-success";
                    $_SESSION['flash'] = "Resume successfully removed.";
                    header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                } else {
                    $_SESSION['flash_mode'] = "alert-warning";
                    $_SESSION['flash'] = "An error has occured while removing your Resume.";
                    header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                }
            } catch (Exception $e) {
                $_SESSION['flash_mode'] = "alert-warning";
                $_SESSION['flash'] = "An error has occured while removing your Resume.";
                header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
            }
        }
    }

    // POST request
    else if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // If it's just the image, process it only
        if (isset($_FILES["profileImage"]) && $_FILES["profileImage"]["error"] == 0) {
            $uploadDir = "profile_images/";
            $fileExtension = pathinfo($_FILES["profileImage"]["name"], PATHINFO_EXTENSION);
            $fileName = $_SESSION['logged_account']['email'].".".$fileExtension;
            
            if ($_FILES["profileImage"]["size"] <= 5242880) { // Ensure image is less than 5MB (5MB = 5242880 bytes)
                if (in_array($fileExtension, ["jpg", "jpeg", "png"])) { // Ensure file type is jpg, jpeg, png
                    // Find and delete the profile image before saving the uploaded one
                    $uploadDir = "profile_images/";
                    $extensions = ['png', 'jpg', 'jpeg'];
                    foreach ($extensions as $extension) {
                        $oldPhotoName = $_SESSION['logged_account']['email'] . '.' . $extension;
                        if (file_exists($uploadDir . $oldPhotoName)) {
                            unlink($uploadDir.$oldPhotoName);
                            break;
                        }
                    }

                    // Save and apply the uploaded profile image
                    if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $uploadDir . $fileName)) { // Move image to profile_images folder
                        // Update the profile_image column in the user_table
                        try {
                            $SQLupdateImage = $conn->prepare("UPDATE user_table SET profile_image = ? WHERE email = ?");
                            $SQLupdateImage->bind_param("ss", $fileName, $_SESSION['logged_account']['email']);
                            if ($SQLupdateImage->execute()) {
                                // Save updated user data
                                $SQLGetUserInfo = $conn->prepare("SELECT * FROM user_table WHERE email = ?");
                                $SQLGetUserInfo->bind_param("s", $_SESSION['logged_account']['email']);
                                $SQLGetUserInfo->execute();
                                $userInfo = $SQLGetUserInfo->get_result()->fetch_assoc();
                                $SQLGetUserInfo->close();
                                $_SESSION['logged_user'] = $userInfo;

                                // Save updated account data
                                $SQLGetAccountInfo = $conn->prepare("SELECT email, type FROM account_table WHERE email = ?");
                                $SQLGetAccountInfo->bind_param("s", $_SESSION['logged_account']['email']);
                                $SQLGetAccountInfo->execute();
                                $accountInfo = $SQLGetAccountInfo->get_result()->fetch_assoc();
                                $_SESSION['logged_account'] = $accountInfo;
                                $SQLupdateImage->close();
                                $conn->close();

                                // Prepare flash message
                                $_SESSION['flash_mode'] = "alert-success";
                                $_SESSION['flash'] = "Profile Photo successfully updated.";
                                header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                            } else {
                                $_SESSION['flash_mode'] = "alert-warning";
                                $_SESSION['flash'] = "An error has occured while updating your Profile Photo.";
                                header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                            }
                        } catch (Exception $e) {
                            $_SESSION['flash_mode'] = "alert-warning";
                            $_SESSION['flash'] = "An error has occured while updating your Profile Photo. Please try again.";
                            header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                        }

                    } else {
                        $_SESSION['flash_mode'] = "alert-warning";
                        $_SESSION['flash'] = "An error has occured while updating your Profile Photo. Please try again.";
                        header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                    }
                // Invalid file type
                } else {
                    $_SESSION['flash_mode'] = "alert-warning";
                    $_SESSION['flash'] = "Invalid file type. Only jpg, jpeg, and png are allowed.";
                    header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                }
            // Exceeding photo size
            } else {
                $_SESSION['flash_mode'] = "alert-warning";
                $_SESSION['flash'] = "Photo size exceeds the maximum allowed (5MB).";
                header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
            }
        // Partially uploaded photo
        } elseif (isset($_FILES["profileImage"]) && $_FILES["profileImage"]["error"] == 3) {
            $_SESSION['flash_mode'] = "alert-warning";
            $_SESSION['flash'] = "Photo was only partially uploaded. Please try again.";
            header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
        // No photo uploaded (Can only happen on client-side modification)
        } elseif (isset($_FILES["profileImage"]) && $_FILES["profileImage"]["error"] == 4) {
            $_SESSION['flash_mode'] = "alert-warning";
            $_SESSION['flash'] = "No photo uploaded. Please try again.";
            header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
        // Some other error
        } elseif (isset($_FILES["profileImage"]) && $_FILES["profileImage"]["error"] != 0) {
            $_SESSION['flash_mode'] = "alert-warning";
            $_SESSION['flash'] = "An error has occured while updating your Profile Photo. Please try again.";
            header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
        }

        // If it's just the resume, process it only
        elseif (isset($_FILES["resume"]) && $_FILES["resume"]["error"] == 0) {
            $uploadDir = "resume/";
            $fileExtension = pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION);
            $fileName = $_SESSION['logged_account']['email'].".".$fileExtension;
            
            if ($_FILES["resume"]["size"] <= 7340032) { // Ensure image is less than 7MB (7MB = 7340032 bytes)
                if (in_array($fileExtension, ["pdf"])) { // Ensure file type is pdf
                    // Find and delete the profile image before saving the uploaded one
                    $uploadDir = "resume/";
                    $extensions = ['pdf'];
                    foreach ($extensions as $extension) {
                        $oldPhotoName = $_SESSION['logged_account']['email'] . '.' . $extension;
                        if (file_exists($uploadDir . $oldPhotoName)) {
                            unlink($uploadDir.$oldPhotoName);
                            break;
                        }
                    }

                    // Save and apply the uploaded profile image
                    if (move_uploaded_file($_FILES["resume"]["tmp_name"], $uploadDir . $fileName)) { // Move image to profile_images folder
                        try {
                            // Update the profile_image column in the user_table
                            $SQLupdateImage = $conn->prepare("UPDATE user_table SET resume = ? WHERE email = ?");
                            $SQLupdateImage->bind_param("ss", $fileName, $_SESSION['logged_account']['email']);
                            if ($SQLupdateImage->execute()) {
                                // Save updated user data
                                $SQLGetUserInfo = $conn->prepare("SELECT * FROM user_table WHERE email = ?");
                                $SQLGetUserInfo->bind_param("s", $_SESSION['logged_account']['email']);
                                $SQLGetUserInfo->execute();
                                $userInfo = $SQLGetUserInfo->get_result()->fetch_assoc();
                                $SQLGetUserInfo->close();
                                $_SESSION['logged_user'] = $userInfo;

                                // Save updated account data
                                $SQLGetAccountInfo = $conn->prepare("SELECT email, type FROM account_table WHERE email = ?");
                                $SQLGetAccountInfo->bind_param("s", $_SESSION['logged_account']['email']);
                                $SQLGetAccountInfo->execute();
                                $accountInfo = $SQLGetAccountInfo->get_result()->fetch_assoc();
                                $_SESSION['logged_account'] = $accountInfo;
                                $SQLupdateImage->close();
                            $conn->close();

                                // Prepare flash message
                                $_SESSION['flash_mode'] = "alert-success";
                                $_SESSION['flash'] = "Resume successfully updated.";
                                header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                            } else {
                                $_SESSION['flash_mode'] = "alert-warning";
                                $_SESSION['flash'] = "An error has occured while updating your Resume.";
                                header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                            }
                        } catch (Exception $e) {
                            $_SESSION['flash_mode'] = "alert-warning";
                            $_SESSION['flash'] = "An error has occured while updating your Resume.";
                            header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                        }
                        
                    } else {
                        $_SESSION['flash_mode'] = "alert-warning";
                        $_SESSION['flash'] = "An error has occured while updating your Resume. Please try again.";
                        header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                    }
                // Invalid file type
                } else {
                    $_SESSION['flash_mode'] = "alert-warning";
                    $_SESSION['flash'] = "Invalid file type. Only PDF is allowed.";
                    header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                }
            // Exceeding resume size
            } else {
                $_SESSION['flash_mode'] = "alert-warning";
                $_SESSION['flash'] = "Resume size exceeds the maximum allowed (7MB).";
                header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
            }
        // Partially uploaded resume
        } elseif (isset($_FILES["resume"]) && $_FILES["resume"]["error"] == 3) {
            $_SESSION['flash_mode'] = "alert-warning";
            $_SESSION['flash'] = "Resume was only partially uploaded. Please try again.";
            header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
        // No resume uploaded
        } elseif (isset($_FILES["resume"]) && $_FILES["resume"]["error"] == 4) {
            $_SESSION['flash_mode'] = "alert-warning";
            $_SESSION['flash'] = "No resume uploaded. Please try again.";
            header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
        // Some other error
        } elseif (isset($_FILES["resume"]) && $_FILES["resume"]["error"] != 0) {
            $_SESSION['flash_mode'] = "alert-warning";
            $_SESSION['flash'] = "An error has occured while saving your Resume. Please try again.";
            header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
        }
        
        // If it's not photo or resume, it's the other text fields
        elseif (!isset($_FILES["profileImage"]) && !isset($_FILES["resume"]) && isset($_POST['email'])) {
            $errors = array();
            foreach ($_POST as $key => $value) {
                // trim all exept passwords
                if ($key != 'password' || $key != 'confirmPassword')
                    $_POST[$key] = trim($_POST[$key]);

                // nullify empty values
                if ($_POST[$key] == "")
                    $_POST[$key] = null;

                // Check for required fields and validation
                include "validation_field.php";
            }

            include 'db_controller.php';
            $conn->select_db("Alumni");

            // If the email is the same being logged in with, ignore error
            if ($_POST['email'] == $_SESSION['logged_account']['email'])
                unset($errors['email']);

            if (!empty($errors)) {
                $_SESSION['form_data'] = $_POST;
                $_SESSION['errors'] = $errors;
                header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
            } else {
                try {
                    // Update user with new data in db
                    $SQLUpdateUser = $conn->prepare("UPDATE user_table SET 
                        email = ?,
                        first_name = ?,
                        last_name = ?,
                        dob = ?,
                        gender = ?,
                        contact_number = ?,
                        hometown = ?,
                        current_location = ?,
                        university = ?,
                        qualification = ?,
                        year = ?,
                        job_position = ?,
                        company = ?
                        WHERE email = ?
                    ");
                    $SQLUpdateUser->bind_param("ssssssssssssss", $_POST['email'], $_POST['firstName'], $_POST['lastName'], $_POST['dob'], $_POST['gender'], $_POST['contactNo'], $_POST['hometown'], $_POST['currentLocation'], $_POST['university'], $_POST['degreeProgram'], $_POST['yearGraduated'], $_POST['jobPosition'], $_POST['company'], $_SESSION['logged_account']['email']);
                    
                    if($SQLUpdateUser->execute()){
                        // Find and rename the profile image with the new email
                        $uploadDir = "profile_images/";
                        $extensions = ['png', 'jpg', 'jpeg'];

                        foreach ($extensions as $extension) {
                            $oldPhotoName = $_SESSION['logged_account']['email'] . '.' . $extension;
                            if (file_exists($uploadDir . $oldPhotoName)) {
                                $newPhotoName = $_POST['email'] . '.' . $extension;
                                rename($uploadDir.$oldPhotoName, $uploadDir.$newPhotoName);

                                // Update image path in db
                                $SQLupdateImage = $conn->prepare("UPDATE user_table SET profile_image = ? WHERE email = ?");
                                $SQLupdateImage->bind_param("ss", $newPhotoName, $_POST['email']);
                                $SQLupdateImage->execute();
                                $SQLupdateImage->close();

                                break;
                            }
                        }

                        // Get and newly updated user details
                        $SQLGetUserInfo = $conn->prepare("SELECT * FROM user_table WHERE email = ?");
                        $SQLGetUserInfo->bind_param("s", $_POST['email']);
                        $SQLGetUserInfo->execute();
                        $userInfo = $SQLGetUserInfo->get_result()->fetch_assoc();
                        $SQLGetUserInfo->close();

                        // Check if it's the same as logged in user
                        if ($loggedUser == $userInfo){
                            $_SESSION['flash_mode'] = "alert-secondary";
                            $_SESSION['flash'] = "No changes saved.";
                            $_SESSION['logged_user'] = $userInfo;
                            header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                        } else {
                            $_SESSION['flash_mode'] = "alert-success";
                            $_SESSION['flash'] = "Profile successfully updated.";
                            $_SESSION['logged_user'] = $userInfo;

                            // Get and save account info from account_table into session
                            $SQLGetAccountInfo = $conn->prepare("SELECT email, type FROM account_table WHERE email = ?");
                            $SQLGetAccountInfo->bind_param("s", $_POST['email']);
                            $SQLGetAccountInfo->execute();
                            $accountInfo = $SQLGetAccountInfo->get_result()->fetch_assoc();
                            $SQLGetAccountInfo->close();
                            $_SESSION['logged_account'] = $accountInfo;

                            header('Location: update_profile.php?email='.$_POST['email']);
                        }
                    }else {
                        $_SESSION['flash_mode'] = "alert-warning";
                        $_SESSION['flash'] = "An error has occured while updating your profile.";
                        header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                    }
                } catch (Exception $e) {
                    $_SESSION['flash_mode'] = "alert-warning";
                    $_SESSION['flash'] = "An error has occured while updating your profile.";
                    header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
                }
            }
            $conn->close();
        }
        header('Location: update_profile.php?email='.$_SESSION['logged_account']['email']);
    }
?>