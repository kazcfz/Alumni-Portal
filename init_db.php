<?php
    include 'db_controller.php';

    // Check connection before executing anything DB related
    if (!$conn->connect_error) {
        try {
            $conn->query("CREATE DATABASE IF NOT EXISTS Alumni"); // DB creation
            $conn->select_db("Alumni"); // DB selection
        } catch (Exception $e) {
            $_SESSION['flash_mode'] = "alert-danger";
            $_SESSION['flash'] = "Failed to create Database.";
            die();
        }

        try{
            // user_table creation
            $conn->query("CREATE TABLE IF NOT EXISTS user_table (
                email VARCHAR(50) NOT NULL PRIMARY KEY,
                first_name VARCHAR(50) NOT NULL,
                last_name VARCHAR(50) NOT NULL,
                dob DATE NULL,
                gender VARCHAR(6) NOT NULL,
                contact_number VARCHAR(15) NULL,
                hometown VARCHAR(50) NOT NULL,
                current_location VARCHAR(50) NULL,
                profile_image VARCHAR(100) NULL,
                job_position VARCHAR(50) NULL,
                qualification VARCHAR(70) NULL,
                year INT(4) NULL,
                university VARCHAR(50) NULL,
                company VARCHAR(50) NULL,
                resume VARCHAR(100) NULL
            )");

            // account_table creation
            $conn->query("CREATE TABLE IF NOT EXISTS account_table (
                email VARCHAR(50) NOT NULL,
                password VARCHAR(255) NOT NULL,
                type VARCHAR(5) NOT NULL,
                status VARCHAR(8) NOT NULL,
                FOREIGN KEY (email) REFERENCES user_table(email) ON DELETE CASCADE ON UPDATE CASCADE
            )");

            // event_table creation
            $conn->query("CREATE TABLE IF NOT EXISTS event_table (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(100) NOT NULL,
                location VARCHAR(50) NOT NULL,
                description VARCHAR(700) NOT NULL,
                event_date DATE NOT NULL,
                photo VARCHAR(100) NULL,
                type VARCHAR(10) NOT NULL
            )");

            // event_registration_table creation
            $conn->query("CREATE TABLE IF NOT EXISTS event_registration_table (
                event_id INT NOT NULL,
                participant_email VARCHAR(50) NOT NULL,
                FOREIGN KEY (event_id) REFERENCES event_table(id) ON DELETE CASCADE ON UPDATE CASCADE
            )");

            // advertisement_table creation
            $conn->query("CREATE TABLE IF NOT EXISTS advertisement_table (
                id INT(4) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(100) NOT NULL,
                description VARCHAR(700) NOT NULL,
                date_added DATE NOT NULL,
                button_message VARCHAR(50) NULL,
                button_link VARCHAR(700) NULL,
                photo VARCHAR(100) NULL,
                category VARCHAR(50) NOT NULL,
                status VARCHAR(20) NOT NULL,
                advertiser VARCHAR(50) NOT NULL,
                appliable BOOLEAN,
                date_to_hide TIMESTAMP NULL,
                FOREIGN KEY (advertiser) REFERENCES user_table(email) ON DELETE CASCADE ON UPDATE CASCADE
            )");

            // advertisement_registration_table creation
            $conn->query("CREATE TABLE IF NOT EXISTS advertisement_registration_table (
                advertisement_id INT NOT NULL,
                email VARCHAR(50) NOT NULL,
                first_name VARCHAR(50) NOT NULL,
                last_name VARCHAR(50) NOT NULL,
                dob DATE NULL,
                gender VARCHAR(6) NOT NULL,
                contact_number VARCHAR(15) NULL,
                hometown VARCHAR(50) NOT NULL,
                current_location VARCHAR(50) NULL,
                profile_image VARCHAR(100) NULL,
                job_position VARCHAR(50) NULL,
                qualification VARCHAR(70) NULL,
                year INT(4) NULL,
                university VARCHAR(50) NULL,
                company VARCHAR(50) NULL,
                resume VARCHAR(100) NULL,
                FOREIGN KEY (advertisement_id) REFERENCES advertisement_table(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (email) REFERENCES user_table(email) ON DELETE CASCADE ON UPDATE CASCADE
            )");





            // user_table dummy data populate
            if ($conn->query("SELECT COUNT(*) as count FROM user_table")->fetch_assoc()['count'] == 0){
                $conn->query("INSERT INTO user_table (email, first_name, last_name, dob, gender, hometown)
                    VALUES
                        ('admin@swin.edu.my', 'Admin', 'User', '2023-10-25', 'Male', 'Kuching'),
                        ('user0@test.com', 'Ellen', 'Ripley', '2023-12-25', 'Female', '2_fort'),
                        ('pootispow@gmail.com', 'Pootis', 'Pow', '2023-11-25', 'Male', 'Turbine'),
                        ('user1@test.com', 'Beatrix', 'Kiddo', '2023-10-25', 'Female', 'Texas'),
                        ('user2@test.com', 'John', 'McClane', '2023-09-25', 'Male', 'New Jersey'),
                        ('user3@test.com', 'Judge Joe', 'Dredd', '2023-08-25', 'Male', 'Perth'),
                        ('user4@test.com', 'Alex', 'Murphy', '2023-07-25', 'Male', 'Brunei'),
                        ('user5@test.com', 'Thomas A.', 'Anderson', '2023-06-25', 'Male', 'Singapore'),
                        ('user6@test.com', 'James', 'Johnson', '2023-05-25', 'Male', 'Johor'),
                        ('user7@test.com', 'Cordell', 'Walker', '2023-04-25', 'Male', 'Kota Kinabalu'),
                        ('user8@test.com', 'B. A.', 'Baracus', '2023-03-25', 'Male', 'Kuching'),
                        ('user9@test.com', 'Exodia the Forbidden', 'One', '2023-02-25', 'Male', 'Perlis'),
                        ('user10@test.com', 'John James', 'Rambo', '2023-01-25', 'Male', 'Miri')"
                );
            }

            // account_table dummy data populate
            $hashedDefaultUserPassword = password_hash("user", PASSWORD_BCRYPT); //hash "user" password using BCrypt
            $hashedDefaultAdminPassword = password_hash("admin", PASSWORD_BCRYPT); //hash "admin" password using BCrypt
            if ($conn->query("SELECT COUNT(*) as count FROM account_table")->fetch_assoc()['count'] == 0){
                $conn->query("INSERT INTO account_table (email, password, type, status)
                    VALUES
                        ('admin@swin.edu.my', '$hashedDefaultAdminPassword', 'admin', 'Approved'),
                        ('user0@test.com', '$hashedDefaultUserPassword', 'user', 'Approved'),
                        ('pootispow@gmail.com', '$hashedDefaultUserPassword', 'user', 'Approved'),
                        ('user1@test.com', '$hashedDefaultUserPassword', 'user', 'Approved'),
                        ('user2@test.com', '$hashedDefaultUserPassword', 'user', 'Approved'),
                        ('user3@test.com', '$hashedDefaultUserPassword', 'user', 'Rejected'),
                        ('user4@test.com', '$hashedDefaultUserPassword', 'user', 'Rejected'),
                        ('user5@test.com', '$hashedDefaultUserPassword', 'user', 'Approved'),
                        ('user6@test.com', '$hashedDefaultUserPassword', 'user', 'Rejected'),
                        ('user7@test.com', '$hashedDefaultUserPassword', 'user', 'Rejected'),
                        ('user8@test.com', '$hashedDefaultUserPassword', 'user', 'Pending'),
                        ('user9@test.com', '$hashedDefaultUserPassword', 'user', 'Pending'),
                        ('user10@test.com', '$hashedDefaultUserPassword', 'user', 'Pending')"
                );
            }

            // event_table dummy data populate
            if ($conn->query("SELECT COUNT(*) as count FROM event_table")->fetch_assoc()['count'] == 0){
                $conn->query("INSERT INTO event_table (title, location, description, event_date, photo, type)
                    VALUES
                        ('Glamping Event', 'Miri, Sarawak', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-10-25', 'glamping_event.jpg', 'Event'),
                        ('Tournament', 'Kuching, Sarawak', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-11-15', 'tournament.PNG', 'Event'),
                        ('Mentorship', 'Perth, Australia', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-12-05', 'mentorship.PNG', 'News'),
                        ('Exciting Stuff', 'Sydney, Australia', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-12-05', 'default_events.jpg', 'Event'),
                        ('Party', 'Kota Kinabalu, Sabah', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-12-05', 'default_events.jpg', 'Event'),
                        ('OH NO', 'Texas, USA', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-12-05', 'default_news.png', 'News'),
                        ('Robot Invasion', '2_fort', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-12-05', 'default_events.jpg', 'News')"
                );
            }

            // advertisement_table dummy data populate
            if ($conn->query("SELECT COUNT(*) as count FROM advertisement_table")->fetch_assoc()['count'] == 0){
                $conn->query("INSERT INTO advertisement_table (title, description, date_added, button_message, button_link, photo, category, status, advertiser, appliable)
                    VALUES
                        ('Lecturers and Tutors!', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-10-15', 'Visit us!', 'https://www.swinburne.edu.my/courses/engineering', 'default_advertisement.jpg', 'Engineering', 'Active', 'admin@swin.edu.my', TRUE),
                        ('Cloud Administrators!', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-10-20', 'More info!', 'https://www.swinburne.edu.my/courses/ict', 'default_advertisement.jpg', 'IT', 'Active', 'admin@swin.edu.my', TRUE),
                        ('Accountants!', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-10-25', 'Check me!', 'https://www.swinburne.edu.my/courses/business', 'default_advertisement.jpg', 'Business', 'Inactive', 'admin@swin.edu.my', TRUE),
                        ('Are you a designer?', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-10-31', 'Gain better understanding!',  'https://www.swinburne.edu.my/courses/design', 'default_advertisement.jpg', 'Design', 'Inactive', 'admin@swin.edu.my', FALSE),
                        ('We talk business!', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-10-31', 'Know us better!',  'https://www.swinburne.edu.my/courses/business', 'default_advertisement.jpg', 'Business', 'Active', 'admin@swin.edu.my', FALSE),
                        ('Building architects!', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-10-31', 'Acknowledge us!',  'https://www.swinburne.edu.my/courses/engineering', 'default_advertisement.jpg', 'Engineering', 'Active', 'admin@swin.edu.my', FALSE),
                        ('Calling all artists!', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', '2023-10-31', 'View me!',  'https://www.swinburne.edu.my/courses/design', 'default_advertisement.jpg', 'Design', 'Inactive', 'admin@swin.edu.my', TRUE)"
                );
            }

            $conn->close(); // close DB connection

        } catch (Exception $e) {
            $_SESSION['flash_mode'] = "alert-warning";
            $_SESSION['flash'] = "Failed to populate database with dummy data.";
        }
    } else {
        header('Location: maintenance.php');
        die();
    }
?>
