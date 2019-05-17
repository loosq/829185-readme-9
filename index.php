<?php

include_once 'init.php';

$userEmail = $_POST['email'];
$userPassword = $_POST['password'];

echo loginFormValidation($con, $userEmail , $userPassword);
