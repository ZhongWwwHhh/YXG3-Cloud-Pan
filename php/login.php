<?php
$loginname = $_POST["loginname"];

// unexpected uid
if ($loginname == null) {
    header("Location:/html/login.html");
}

// information for mysql
$host = 'localhost';
$db_username = 'pan';
$db_pwd = 'jA5R2P7fZySfT2Kt';
$db_name = 'pan';
// start connect to mysql db
$conn = mysqli_connect($host, $db_username, $db_pwd, $db_name);

if (!$conn) {
    // server error
    header("Location:/500.html");
} else {
    // connect mysql success
    $check_query = mysqli_query($conn, "select filepath from user where lightname='$loginname'");
    $arr = mysqli_fetch_assoc($check_query);

    if ($arr) {
        // login success
        // start session
        session_start();
        $time = 120; // 2 minute timeout
        // set cookie
        setcookie(session_name(), session_id(), time() + $time, "/");
        $_SESSION['lightname'] = $loginname;
        $_SESSION['filepath'] = $arr['filepath'];
        // redirect
        header("Location:/php/file.php");
    } else {
        // login fail no user
        header("Location:/html/login.html");
    }
}
mysqli_free_result($check_query);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta charset="UTF-8">
    <title>Logining</title>
    <link rel="stylesheet" type="text/css" href="/css/loading.css">
</head>

<body>
    <div class="loadingThree">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
</body>

</html>