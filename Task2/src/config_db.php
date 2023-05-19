<?php
// Muawya code 
$server = 'db';
$username = 'root';
$password = 'csym019';
$schema = 'CSYM019';
$pdo = new PDO('mysql:dbname=' . $schema . ';host=' . $server, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

?>