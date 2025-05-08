<?php
session_start();
unset($_SESSION["email"]);
unset($_SESSION["password"]);
session_destroy();
header("Location: Login/login.php");
exit(); // Always include exit after a header redirect