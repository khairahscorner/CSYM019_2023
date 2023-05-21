<?php // all php code needed to execute before output is shown in browser needs to be at the top
session_start(); //(PHP Documentation)

if ($_SESSION["authenticated"] === true) {
	header("Location: courselist.php"); // PHP docs
	exit();
} else {
	require_once('config_db.php'); // include db setup (https://www.geeksforgeeks.org/how-to-include-content-of-a-php-file-into-another-php-file/)

	if (isset($_POST['submit'])) { // Muawya code
		$username = $_POST['username'];
		$password = $_POST['password'];

		$stmt = $pdo->prepare('SELECT * FROM auth WHERE username = :username AND password = :password');
		$values = [
			"username" => $_POST['username'],
			"password" => $_POST['password']
		];

		$stmt->execute($values);
		$result = $stmt->fetch(PDO::FETCH_ASSOC); //(PHP doc) - does it find a row match, return as array with keys
		if ($result) {
			$_SESSION['authenticated'] = true;
			header('Location: courselist.php'); //PHP documentation on header()
			exit();
		} else {
			$error = "Invalid username or password";
		}
	}
}
?>

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
			<!-- Muawya code -->
			<div class="input-group">
				<label for="username">Username:</label>
				<input type="text" name="username" placeholder="username" required>
			</div>
			<div class="input-group">
				<label for="password">Password:</label>
				<input type="password" name="password" placeholder="password" required>
			</div>

			<?php
			if (isset($error)) {
				echo "<p class='error'> $error</p>";
			}
			?>
			<button type="submit" name="submit">Login</button>
		</form>
	</div>
</body>

</html>