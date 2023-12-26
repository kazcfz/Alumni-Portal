<!-- Dropdown menu on the top nav bar (User button) -->
<div class="d-flex">
    <div class="dropdown">
        <!-- Set image to display according to set profile image, or gender -->
        <div class="nav-user" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?php 
                // Give admins an icon as indication
                $adminIcon = ($_SESSION['logged_account']['type'] == 'admin') ? '<img src="images/admin-icon.png" class="admin-icon" alt="admin">' : "";

                if (isset($_SESSION['logged_user']['profile_image']))
                    echo '<div class="image-container-nav"><img src="profile_images/'.$_SESSION['logged_user']['profile_image'].'" class="img-fluid nav-user" alt="profile_picture"></div>';
                elseif ($_SESSION['logged_user']['gender'] == "Male")
                    echo '<img src="profile_images/default-male-user-profile-icon.jpg" class="img-fluid nav-user" alt="profile_picture">';
                elseif ($_SESSION['logged_user']['gender'] == "Female")
                    echo '<img src="profile_images/default-female-user-profile-icon.jpg" class="img-fluid nav-user" alt="profile_picture">';

                echo $adminIcon;
            ?>
        </div>

        <!-- Menu option -->
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-nav mt-1 px-2">
            <!-- If logged account type is 'user', enable options to view own profile and update profile -->
            <?php if ($_SESSION['logged_account']['type'] == 'user') { ?>
                <!-- Your profile -->
                <li><a class="dropdown-item py-2 pe-5" href="<?php echo 'profile_detail.php?email='.htmlspecialchars($_SESSION['logged_account']['email']) ?>">
                    <div class="circle-container me-2"><i class="bi bi-person-fill nav-user-bi"></i></div><div class="fw-medium">Your profile</div>
                </a></li>
                
                <!-- Update profile -->
                <li><a class="dropdown-item py-2 pe-5" href="<?php echo 'update_profile.php?email='.htmlspecialchars($_SESSION['logged_account']['email'])?>">
                    <div class="circle-container me-2"><i class="bi bi-pencil-fill nav-user-bi"></i></div><div class="fw-medium">Update profile</div>
                </a></li>
                <li><hr class="dropdown-divider border-2 mx-2"></li>
            <?php } ?>

            <!-- Logout -->
            <li><a class="dropdown-item py-2 pe-5" href="logout.php">
                <div class="circle-container me-2"><i class="bi bi-door-open-fill nav-user-bi"></i></div><div class="fw-medium">Log out</div>
            </a></li>
        </ul>
    </div>
</div>