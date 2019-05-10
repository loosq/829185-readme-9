<?php

include_once 'init.php';
include_once 'helpers.php';
include_once 'functions.php';
session_start();

echo loginFormValidation($con, $_POST['email'], $_POST['password']);
