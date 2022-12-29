<?php
session_start();
$time = 600; // time out 6 min
setcookie(session_name(), session_id(), time() + $time, "/");
// not from register.php
if ($_SESSION['register'] != true) {
    echo 'Wrong page. Please go back';
    exit;
}

// to register
function back()
{
    header("location:register.php");
    exit;
}

// retry times control
if (!isset($_SESSION['retryTimes'])) {
    $_SESSION['retryTimes'] = 0;
} elseif ($_SESSION['retryTimes'] <= 2) {
    $_SESSION['retryTimes'] += 1;
} else {
    // retry too many times
    $_SESSION = array();
    $_SESSION['register'] = true;
    $_SESSION['step'] = 1;
    $_SESSION['err'] = 11;
    back();
}

// time control
if (!isset($_SESSION['outtime'])) {
    $_SESSION['outtime'] = time() + 1800;
}
if (time() > $_SESSION['outtime']) {
    // timeout
    $_SESSION = array();
    $_SESSION['register'] = true;
    $_SESSION['step'] = 1;
    $_SESSION['err'] = 10;
    back();
}

// err reset
$_SESSION['err'] = 0;

if ($_SESSION['step'] == 1) {
    $newLightName = $_POST['lightname'];
    $agreeLicense = $_POST['agreeLicense'];
    $vaptchaToken = $_POST['vaptchaToken'];
    $vaptchaServer = $_POST['vaptchaServer'];

    // clean data
    require '../../function/inputcheck.php';
    if (strlen($newLightName) > 10 || checkStr($newLightName) == FALSE) {
        $_SESSION['err'] = 1;
        back();
    }

    // vaptcha
    function getIP()
    {
        static $realip;
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }
        return $realip;
    }
    $ip = getIP();

    function curl_post_https($url, $data)
    { // 模拟提交数据函数
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl); //捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据，json格式
    }

    $post_data = array(
        'id' => '638c93e9cdf7d074d80a4dd0',
        'secretkey' => 'e34a244f95aa4b3f80f5b208f5064388',
        'scene' => 0,
        'token' => $vaptchaToken,
        'ip' => $ip,
    );

    $vaptchaResult = json_decode(curl_post_https($vaptchaServer, $post_data), true);

    if ($vaptchaResult['success'] != 1 || $vaptchaResult['score'] < 90) {
        $_SESSION['err'] = 6;
        back();
    }

    // agree and name
    if ($agreeLicense != 'on') {
        $_SESSION['err'] = 1;
        back();
    }

    // check name
    require '../../function/mysqli.php';
    $sqlselect = sqliSelect($newLightName, 'lightname', 'user');
    $sqlnum = sqliNumRow($sqlselect);
    if ($sqlnum != 0) {
        $_SESSION['err'] = 2;
        back();
    }

    // to step 2
    $_SESSION['step'] = 2;
    $_SESSION['retryTimes'] = 0;
    back();
} elseif ($_SESSION['step'] == 2) {
    $_SESSION['newemail'] = $_POST['email'];
    $_SESSION['verifyCode'] = random_int(10000000, 99999999);

    // check email
    function check_email($email)
    {
        $result = trim($email);
        if (filter_var($result, FILTER_VALIDATE_EMAIL)) {
            return "true";
        } else {
            return "false";
        }
    }
    if (!check_email($_SESSION['newemail'])) {
        $_SESSION['err'] = 1;
        back();
    }

    // send email
    $_SESSION['emailContent'] = '<p>您的注册验证码是：</p><p style="font-size: 25px;">' . $_SESSION['verifyCode'] . '</p><p>验证码在30分钟内有效，请根据页面提示输入。</p>';
    require '../../function/email.php';
    if (sendEmail('邮箱验证', $_SESSION['emailContent'], $_SESSION['newemail'])) {
        // to step 3
        $_SESSION['step'] = 3;
        $_SESSION['retryTimes'] = 0;
        back();
    } else {
        $_SESSION['err'] = 4;
        back();
    }
} elseif ($_SESSION['step'] == 3) {
    // resend
    if (isset($_POST['resend'])) {
        if ($_POST['resend'] == true) {
            require '../../function/email.php';
            sendEmail('邮箱验证', $_SESSION['emailContent'], $_SESSION['newemail']);
            back();
        }
    }

    // verify
    if ($_POST['verifyCodeBack'] != $_SESSION['verifyCode']) {
        $_SESSION['err'] = 5;
        back();
    }

    // account information
    $newlightname = $_SESSION['newlightname'];
    $email = $_SESSION['newemail'];
    $uuid1 = substr(hash('sha256', rand(0, 2147483647)), rand(0, 56), 8);
    $uuid2 = substr(hash('sha256', rand(0, 2147483647)), rand(0, 56), 8);
    $uuid = $uuid1 . $uuid2;
    $filepath = substr($uuid, rand(0, 10), 5);

    // prevent reflash
    $_SESSION['register'] = false;

    // create account
    $newUser = array('lightname' => $newlightname, 'uuid' => $uuid, 'filepath' => $filepath, 'email' => $email);

    require '../../function/mysqli.php';
    $sqlselect = sqliSelect($newlightname, 'lightname', 'user');
    // recheck
    $sqlnum = sqliNumRow($sqlselect);
    if ($sqlnum != 0) {
        sqliClose();
        $_SESSION = array();
        $_SESSION['register'] = true;
        $_SESSION['step'] = 1;
        $_SESSION['err'] = 2;
        back();
    }
    sqliInsert($newUser, 'user');
    sqliClose();

    // show uuid
    $_SESSION = array();
    $_SESSION['newlightname'] = $newlightname;
    $_SESSION['uuid'] = $uuid;
    header("location:success.php");
    exit;
} else {
    echo 'Wrong. Unkonwn problem. Please restart your browser';
}
