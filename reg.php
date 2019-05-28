<?php

include_once 'init.php';

$title = 'Registration';
$getTab = $_GET['tab'] ?? '';
$search = $_GET['q'] ?? '';
$userSession = $_SESSION;
$userPicFile = $_FILES['userpic-file']['name'] ?? '';
$userPicFilePath = $_FILES['userpic-file']['tmp_name'] ?? '';
$email = $_POST['email'] ?? '';
$userName = $_POST['userName'] ?? '';
$pwd = $_POST['password'] ?? '';
$copyPwd = $_POST['password-repeat'] ?? '';
$contactInfo = $_POST['contact-info'] ?? '';

if (!isUserLoggedIn()) {

    $content = regFormValidation($con, $contactInfo, $copyPwd, $pwd, $userName, $email, $userPicFilePath, $userPicFile);

    $html = include_template('layout.php', [
        'userSession' => $userSession,
        'getTab'      => $getTab,
        'content'     => $content,
        'title'       => $title,
        'search'      => $search,
    ]);
    echo $html;

} else {
    redirectHome();
}
