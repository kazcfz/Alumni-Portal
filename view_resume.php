<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment 2 | View Resume</title>
</head>
<body>
    
</body>
    <?php 
        session_start();
        include 'logged_user.php';

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (isset($_GET["resume"])){
                $alumnusToViewResume = htmlspecialchars($_GET["resume"]);

                // Load resume from path in storage
                $filePath = "resume/".$alumnusToViewResume;
                
                if (file_exists($filePath)) {
                    // Headers
                    header('Content-type: application/pdf');
                    header('Content-Disposition: inline; filename="' . $filePath . '"');
                    header('Content-Transfer-Encoding: binary');
                    header('Accept-Ranges: bytes');
                    
                    // Read PDF file
                    @readfile($filePath);
                } else{
                    header('Location: main_menu.php');
                }
            }
        }
    ?>
</html>
