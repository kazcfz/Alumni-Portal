<!-- Gets the selected alumnus info by their email -->

<?php
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if (isset($_GET["email"])){
            $SQLGetAlumnusInfo = $conn->prepare("SELECT * FROM user_table WHERE email = ?");
            $SQLGetAlumnusInfo->bind_param("s", $_GET['email']);
            $SQLGetAlumnusInfo->execute();
            $result = $SQLGetAlumnusInfo->get_result();

            if ($result->num_rows > 0) {
                $accountInfo = $result->fetch_assoc();

                $alumnusToView = $accountInfo;
                $alumnusToViewFirstName = $accountInfo['first_name'];
                $alumnusToViewLastName = $accountInfo['last_name'];
                $alumnusToViewName = $accountInfo['first_name']." ".$accountInfo['last_name'];
                $alumnusToViewDOB = $accountInfo['dob'];
                $alumnusToViewEmail = $accountInfo['email'];
                $alumnusToViewGender = $accountInfo['gender'];
                $alumnusToViewContactNo = $accountInfo['contact_number'];
                $alumnusToViewHometown = $accountInfo['hometown'];
                $alumnusToViewJobPosition = $accountInfo['job_position'];
                $alumnusToViewCompany = $accountInfo['company'];
                $alumnusToViewCurrentLocation = $accountInfo['current_location'];
                $alumnusToViewDegree = $accountInfo['qualification'];
                $alumnusToViewDegreeYearGraduated = $accountInfo['year'];
                $alumnusToViewCampus = $accountInfo['university'];
                $alumnusToViewProfilePicture = $accountInfo['profile_image'];
                $alumnusToViewResume = $accountInfo['resume'];
            }
        } else{
            header('Location: view_alumni.php');
        }
    }
?>