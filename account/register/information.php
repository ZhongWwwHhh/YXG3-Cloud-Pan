<?php
session_start();
$time = 600; // time out 6 min
setcookie(session_name(), session_id(), time() + $time, "/");
// already login
if (isset($_SESSION['lightname'])) {
    header("Location:/file/panel.php");
    exit;
}

if (!isset($_SESSION['register'])) {
    $_SESSION['register'] = true;
    $_SESSION['step'] = 1;
    $_SESSION['err'] = 0;
}

// already at other step
if ($_SESSION['step'] != 1) {
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
    <script src="https://v-cn.vaptcha.com/v3.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
</head>

<body>
    <div class="main">
        <?php if (!$_SESSION['err'] == 0) {
            if ($_SESSION['err'] == 2) {
                echo '<div class="warn">用户名已存在</div>';
            } else {
                echo '<div class="warn">请按要求输入</div>';
            }
        } ?>

        <form action="register.php" method="post">
            <label for="lightname">新用户名(十个字以内 只允许a-z A-Z 0-9)</label>
            <input required id="lightname" type="text" name="lightname">

            <input required id="vaptchaToken" type="hidden" name="vaptchaToken" readonly>
            <input required id="vaptchaServer" type="hidden" name="vaptchaServer" readonly>
            <div id="VAPTCHAContainer">
                <!-- 预加载动画 -->
                <div class="VAPTCHA-init-main">
                    <div class="VAPTCHA-init-loading">
                        <a href="/" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="48px" height="60px" viewBox="0 0 24 30" style="enable-background: new 0 0 50 50; width: 14px; height: 14px; vertical-align: middle" xml:space="preserve">
                                <rect x="0" y="9.22656" width="4" height="12.5469" fill="#CCCCCC">
                                    <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0s" dur="0.6s" repeatCount="indefinite"></animate>
                                    <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0s" dur="0.6s" repeatCount="indefinite"></animate>
                                </rect>
                                <rect x="10" y="5.22656" width="4" height="20.5469" fill="#CCCCCC">
                                    <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0.15s" dur="0.6s" repeatCount="indefinite"></animate>
                                    <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0.15s" dur="0.6s" repeatCount="indefinite"></animate>
                                </rect>
                                <rect x="20" y="8.77344" width="4" height="13.4531" fill="#CCCCCC">
                                    <animate attributeName="height" attributeType="XML" values="5;21;5" begin="0.3s" dur="0.6s" repeatCount="indefinite"></animate>
                                    <animate attributeName="y" attributeType="XML" values="13; 5; 13" begin="0.3s" dur="0.6s" repeatCount="indefinite"></animate>
                                </rect>
                            </svg>
                        </a>
                        <span class="VAPTCHA-text">Vaptcha Initializing...</span>
                    </div>
                </div>
            </div>

            <script>
                vaptcha({
                    vid: '638c93e9cdf7d074d80a4dd0',
                    mode: 'click',
                    scene: 0,
                    container: '#VAPTCHAContainer',
                    area: 'auto',
                }).then(function(VAPTCHAObj) {
                    obj = VAPTCHAObj;
                    VAPTCHAObj.render();
                    VAPTCHAObj.listen('pass', function() {
                        serverToken = VAPTCHAObj.getServerToken();
                        var elementVaptchaToken = document.getElementById("vaptchaToken");
                        var elementVaptchaServer = document.getElementById("vaptchaServer");
                        elementVaptchaToken.value = serverToken.token;
                        elementVaptchaServer.value = serverToken.server;
                    })
                })
            </script>

            <input required id="agreeLicense" type="checkbox" name="agreeLicense">
            <label for="agreeLicense">我已阅读并遵守<a href="/text/license.html" target="_blank">使用规范</a></label>
            <input id="submit" type="submit" value="注册">
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