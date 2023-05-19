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
    <title>All Courses</title>
    <link rel="stylesheet" href="./layout.css">
</head>
<body>
    <?php include("header.html"); ?>
    <main>
            <h3> All Courses</h3>
            <div class="table">
                here
            </div>
            <form action="./report.php" class="addmore">
                <input type="submit" value="Create Course Report" />
            </form>
            <?php
			if (isset($error)) {
				echo "<p class='error'> $error</p>";
			}
			?>
        </main>
    
        <footer>&copy; CSYM019 2023</footer>
</body>
</html>