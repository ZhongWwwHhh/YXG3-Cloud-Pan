<?php
session_start();
$time = 1800; // time out 6 min
setcookie(session_name(), session_id(), time() + $time, "/");
// already login
if (isset($_SESSION['lightname'])) {
    header("Location:/file/panel.php");
    exit;
}

// already at other step
if ($_SESSION['step'] != 3 || $_SESSION['register'] != true) {
    echo 'Wrong page. Please go back';
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="register.css">
</head>

<body>
    <div class="main">
        <?php if (!$_SESSION['err'] == 0) {
            if ($_SESSION['err'] == 3) {
                echo '<div class="warn">验证邮件发送失败，请重试</div>';
            } else {
                echo '<div class="warn">验证码错误</div>';
            }
        } ?>

        <form action="register.php" method="post">
            <label for="verifyCode">Verify Code</label>
            <input required id="verifyCode" type="text" name="verifyCodeBack">
            <p style="color: red; font-size: 12px;">邮件接收慢，请耐心等待，有效30分钟</p>
            <input id="submit" type="submit" value="验证">
        </form>
        <form action="register.php" method="post">
            <input required id="resend" type="text" name="resend" value="true" readonly hidden>
            <input id="submit" type="submit" value="重新发送验证码">
        </form>
    </div>

    <div class="offloading" id="loading">
        <iframe src="/loading/loading.html" width="100%" height="100%" frameborder="0">
            loading...
        </iframe>
    </div>
    <script>
        var submitbtn = document.getElementById('submit');
        var loading = document.getElementById('loading');

        submitbtn.onclick = function() {
            loading.className = 'onloading';
        };
    </script>

</body>

</html>