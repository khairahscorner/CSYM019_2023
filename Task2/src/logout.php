<?php
session_start();
if ($_SESSION["authenticated"] === true) {
    session_destroy();
    header("Location: index.php");
}
?>