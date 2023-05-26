<?php
// Set up parameters to access the database
$server = 'db'; // database server to access, named "db" in the docker-compose.yml file
$username = 'root'; // username to access the server, using default 'root' user
$password = 'csym019'; //password set up for server in the docker-compose.yml file
$schema = 'CSYM019'; //name of database to use
//PDO is used to create connections to databases (Eldaw M, 2023)
$pdo = new PDO('mysql:dbname=' . $schema . ';host=' . $server, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
?>