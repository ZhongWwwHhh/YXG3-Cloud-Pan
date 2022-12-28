<?php
$loginname = $_POST["loginname"];

// no name
if ($loginname == null) {
    header("Location:/html/login.html");
}

// check name
require_once '../../function/inputcheck.php';

if (checkStr($loginname) == true) {
    if (strlen($loginname) == 16) {
        //uuid
        require_once '../../function/mysqli.php';
        $sqlRow = sqliSelect($loginname, 'uuid', 'user');
        if (sqliNumRow($sqlRow) != 1) {
            sqliClose();
            header('location:/');
            exit;
        }
        $userInformation = sqliGetArray($sqlRow);
        sqliClose();
        $write = true;
    } elseif (strlen($loginname) <= 10) {
        // lightname
        require_once '../../function/mysqli.php';
        $sqlRow = sqliSelect($loginname, 'lightname', 'user');
        if (sqliNumRow($sqlRow) != 1) {
            sqliClose();
            header('location:/');
            exit;
        }
        $userInformation = sqliGetArray($sqlRow);
        sqliClose();
        $write = false;
    } else {
        // wrong
        header('location:/');
        exit;
    }
    session_start();
    $time = 120; // 2 minute timeout
    // set cookie
    setcookie(session_name(), session_id(), time() + $time, "/");
    $_SESSION['lightname'] = $userInformation['lightname'];
    $_SESSION['filepath'] = $userInformation['filepath'];
    $_SESSION['write'] = $write;
    header('location:/file/panel/panel.php');
    exit;
} else {
    header('location:/');
    exit;
}
