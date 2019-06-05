<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

//Общие настройки
date_default_timezone_set('Europe/Moscow');
setlocale(LC_ALL, 'ru_RU');

//Подключение внешних файлов
include_once 'vendor/autoload.php';
include_once 'functions/sqlQueries.php';
include_once 'functions/notify.php';
include_once 'functions/helpers.php';
include_once 'functions/database.php';
include_once 'functions/routines.php';
include_once 'functions/formValidation.php';

//Подключение к БД
$con = dbConnect('localhost', 'root', '', 'readme');
mysqli_set_charset($con, 'utf8');

//Начинаем сессию
session_start();
