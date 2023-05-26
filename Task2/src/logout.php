<?php
session_start(); //function to resume currently existing session (session_start)

//if the authenticated exists from the session, this means a user is logged in
if ($_SESSION["authenticated"] === true) {
    session_destroy(); //destroy the session so authenticated variable is deleted
    header("Location: index.php");
    exit();
}
?>