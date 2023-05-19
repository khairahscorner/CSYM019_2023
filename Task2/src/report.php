<?php
// Check if the user is authenticated
session_start();
if ($_SESSION["authenticated"] !== true) {
    header("Location: index.php"); // PHP docs
} else {
    require('config_db.php'); // include db setup (https://www.geeksforgeeks.org/how-to-include-content-of-a-php-file-into-another-php-file/)

}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Report</title>
    <link rel="stylesheet" href="./layout.css">
</head>
<body>
    <?php include("header.html"); ?>
    <main>
        <?php
			if (isset($error)) {
				echo "<p class='error'> $error</p>";
			}
			?>
        <h3>Sample Course Reoprt</h3>
        <div class="sketch">
            <img src="./sampleReport.png" alt="Sample Course Report">
        </div>
    </main>
    
    <footer>&copy; CSYM019 2023</footer>
</body>
</html>