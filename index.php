<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | Index</title>

    <link rel="stylesheet" href="css/styles.css">

    <!-- Bootstrap CSS --><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons --><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Alumni Portal</h1>
    </div>
    
    <div class="container mb-5 py-4 px-4 mainView bg-white">
        <div class="row mb-5">
            <!-- Image -->
            <div class="col-auto me-3">
                <img class="img-fluid profilePicture" src="profile_images/profilepic.png" alt="Profile Picture" width="250" height="250"/>
            </div>

            <!-- Name, ID, Email -->
            <div class="col">
                <div class="col mb-1">
                    <h2>Kazumasa Chong Foh-Zin</h2>
                </div>
                <div class="col mb-1">
                    <span><strong>Student ID:</strong> 102762373</span>
                </div>
                <div class="col">
                    <span><strong>Email:</strong> <a class="link-underline link-underline-opacity-0" href="mailto:102762373@students.swinburne.edu.my">102762373@students.swinburne.edu.my</a></span>
                </div>
            </div>
        </div>

        <hr/>
        
        <!-- Assessment Declaration -->
        <div class="container">
            <div class="col">
                <h4>Assessment Declaration</h4>
                <input class="form-check-input" type="checkbox" value="" aria-label="Checkbox for assignment declaration" checked disabled>
                I declare that this assignment is my individual work. I have not work collaboratively nor have I copied from any other student's work or from any other source. I have not engaged another party to complete this assignment. I am aware of the University's policy with regards to plagiarism. I have not allowed, and will not allow, anyone to copy my work with the intention of passing it off as his or her own work.
            </div>

            <div class="row justify-content-center mt-5">
                <div class="col-auto">
                    <a role="button" href="main.php" class="btn btn-primary py-2">Main Page</a>
                </div>
                <div class="col-auto">
                    <a role="button" href="about.php" class="btn btn-outline-primary py-2">About this assignment</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Boostrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>