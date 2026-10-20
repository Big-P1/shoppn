<?php
require_once '../core/core.php';

session_unset();
session_destroy();

header("Location: ../views/login.php");
exit();
?>