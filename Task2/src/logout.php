<?php
session_start(); //function to resume currently existing session (session_start)
if ($_SESSION["authenticated"] === true) { //if the authenticated exists from the session, this means a user is logged in
    session_destroy(); //destroy the session so authenticated variable is deleted
    header("Location: index.php"); //redirect to the index page
    exit(); //end script
}
?>