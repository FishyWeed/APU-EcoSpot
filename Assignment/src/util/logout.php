<?php
session_start();
error_reporting(E_ALL);

$_SESSION = array();

session_destroy();

header("Location: /Assignment/src/pages/index.php");
exit();
?>
