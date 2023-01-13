<?php
header('cache-control:no-store');

require_once '../function/session.php';
sessionStart();

if (!isset($_SESSION['uuid'], $_SESSION['newlightname'])) {
    session_destroy();
    header('location:/');
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Success</title>
    <link rel="stylesheet" type="text/css" href="success.css">
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.10/dist/clipboard.min.js"></script>
</head>

<body>
    <div>
        <p id="congratulate">Register Success!</p>
        <p id="lightname">欢迎 <?php echo $_SESSION['newlightname']; ?></p>
        <label for="uuid">您的UUID是：</label><br>
        <button id="uuid" data-clipboard-target="#uuid"><?php echo $_SESSION['uuid']; ?></button>
        <p id="remind">请妥善保存此UUID，这是您以可写权限登陆的唯一凭证。<br>您可以复制后转到登录页，并利用浏览器自带的密码保存功能</p>
        <p><a id="finish" href="/">已保存UUID 转到登录页</a></p>
    </div>
</body>

<script>
    var clipboard = new ClipboardJS('#uuid');

    clipboard.on('success', function(e) {
        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);
    });

    clipboard.on('error', function(e) {
        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);
    });
</script>

</html>

<?php
session_destroy();
?>