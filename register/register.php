<?php
header('cache-control:no-store');

require_once '../function/session.php';
sessionStart();

// already login
if (isset($_SESSION['lightname'])) {
    header('Location:/file/panel/panel.php');
    exit;
}

isset($_SESSION['register'], $_SESSION['step'], $_SESSION['err']) || ($_SESSION['register'] = true) . ($_SESSION['step'] = 1) . ($_SESSION['err'] = 0);
?>

<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="register.css">
    <?php
    if ($_SESSION['step'] == 1) {
        echo <<<EOT
            <script src="https://v-cn.vaptcha.com/v3.js"></script>
            <script src="https://cdn.bootcdn.net/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
        EOT;
    } ?>
</head>

<body>
    <div class="main">
        <div class="warn">FireFox人机验证存在问题，建议使用webkit或Blink内核的浏览器，如<a href="https://www.google.cn/chrome/">Chrome</a>或<a href="https://www.microsoft.com/edge">Edge</a></div>
        <?php
        if ($_SESSION['err'] != 0) {
            echo '<div class="warn">';
            if ($_SESSION['err'] == 1) {
                echo '请按要求输入';
            } elseif ($_SESSION['err'] == 2) {
                echo '用户名已存在';
            } elseif ($_SESSION['err'] == 3) {
                echo '邮箱已存在';
            } elseif ($_SESSION['err'] == 4) {
                echo '验证邮件发送失败，请重试';
            } elseif ($_SESSION['err'] == 5) {
                echo '验证码错误';
            } elseif ($_SESSION['err'] == 6) {
                echo '人机验证失败，禁止使用代理';
            } elseif ($_SESSION['err'] == 10) {
                echo '超时';
            } elseif ($_SESSION['err'] == 11) {
                echo '重试次数过多';
            }
            echo '</div>';
        } ?>

        <form action="registerhandle.php" method="post">
            <?php
            if ($_SESSION['step'] == 1) {
                print <<<EOT
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
                    <label for="agreeLicense">我已阅读并遵守<a href="/license/license.html" target="_blank">使用规范</a></label>
                EOT;
            } elseif ($_SESSION['step'] == 2) {
                print <<<EOT
                    <label for="email">E-mail</label>
                    <input required id="email" type="text" name="email">
                EOT;
            } elseif ($_SESSION['step'] == 3) {
                print <<<EOT
                    <label for="verifyCode">Verify Code</label>
                    <input required id="verifyCode" type="text" name="verifyCodeBack">
                    <p class="tip">邮件接收慢，请耐心等待，有效30分钟</p>
                EOT;
            } else {
                print <<<EOT
                    <p class="warn">Wrong. Unknown problem<p>
                EOT;
            }
            ?>

            <input id="submit" type="submit" value="注册">
        </form>
        <?php
        if ($_SESSION['step'] == 3) {
            print <<<EOT
                <form action="registerhandle.php" method="post">
                    <input required id="resend" type="text" name="resend" value="true" readonly hidden>
                    <input id="submit" type="submit" value="重新发送验证码">
                </form>
            EOT;
        }
        ?>
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

<script async defer src="https://analytics.umami.is/script.js" data-website-id="96af1f91-0871-4ee8-a50c-5070f8ce57bb"></script>

</html>