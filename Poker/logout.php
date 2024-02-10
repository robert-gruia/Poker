<link rel="icon" href="../Images/Icon.ico">
<?php
session_start();

$_SESSION = array();

session_destroy();

header("Location: ../index.php");
exit();
?>