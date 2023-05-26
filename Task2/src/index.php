<?php // all php code needed to execute before output is shown in browser needs to be at the top
session_start(); // function to start a session/resume an existing one, to retrieve stored session variables (PHP Documentation)

if ($_SESSION["authenticated"] === true) { //checks if there is a set "authentication" session variable which means a user is already logged in
	header("Location: courselist.php"); // so, redirect to default page - page showing the courses list
	exit(); //end script
} else { // if not, user needs to log in, so stay on the page
	require_once('config_db.php'); // include db setup from another PHP file (rohanmittal1366, 2022)

	if (isset($_POST['submit'])) { // if submit button with name "submit" is clicked (Eldaw M, 2023)
		$username = $_POST['username']; //save username input field to variable
		$password = $_POST['password']; //save password input field to variable

		//use prepared statements to prepare the SQL command to use to for the auth table (Eldaw M, 2023)
		$stmt = $pdo->prepare('SELECT * FROM auth WHERE username = :username AND password = :password');

		//store the values to be injected into the prepared statement
		$values = [
			"username" => $_POST['username'],
			"password" => $_POST['password']
		];
		$stmt->execute($values); // send the query to the database with the required values
		$result = $stmt->fetch(PDO::FETCH_ASSOC); // return the results as an associated array with keys (PHP Documentation, )

		if ($result) { //if it finds a row, the result array contains the first match
			$_SESSION['authenticated'] = true; // set an "authenticated" session variable to true, this will exist until the session is destroyed
			header('Location: courselist.php'); //redirect to course listing page as user is now successfully logged in (PHP documentation)
			exit(); //end running of script
		} else {
			$error = "Invalid username or password"; //else, show error to user
		}
	}
}
?>

<!-- html code for the page  -->
<!DOCTYPE html>
<html>

<head>
	<title>Login</title>
	<link rel="stylesheet" href="./layout.css">
</head>

<body class="login">
	<div class="wrapper">
		<h2>CSYM019 - Portal Login</h2>
		<form method="POST">
			<!--input fields with assigned names picked up by PHP -->
			<div class="input-group">
				<label for="username">Username:</label>
				<input type="text" name="username" placeholder="username" required>
			</div>
			<div class="input-group">
				<label for="password">Password:</label>
				<input type="password" name="password" placeholder="password" required>
			</div>

			<?php
			if (isset($error)) { // if the error variable is set, show the error element
				echo "<p class='error'> $error</p>";
			}
			?>
			<button type="submit" name="submit">Login</button> <!-- submit button -->
		</form>
	</div>
</body>

</html>