<?php
//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();
$time = 600; // time out 6 min
setcookie(session_name(), session_id(), time() + $time, "/");
// not from register.html
if (!$_SESSION['register'] = true) {
    echo 'Wrong page. Please go back';
    exit;
}

// time control
if (!isset($_SESSION['outtime'])) {
    $_SESSION['outtime'] = time() + 1800;
}
if (time() > $_SESSION['outtime']) {
    echo 'Timeout. Please restart';
}

$_SESSION['err'] = 0;
if ($_SESSION['step'] == 1) {
    $newLightName = $_POST["lightname"];
    $agreeLicense = $_POST["agreeLicense"];
    $vaptchaToken = $_POST["vaptchaToken"];
    $vaptchaServer = $_POST['vaptchaServer'];

    // clean data
    function check_str($str)
    {
        $res = preg_match('/^[A-Za-z0-9]+$/u', $str);
        return $res ? TRUE : FALSE;
    }
    if (strlen($newLightName) > 10 || check_str($newLightName) == FALSE) {
        $_SESSION['err'] = 1;
        header("location:information.php");
        exit;
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
        echo 'Vaptcha fail. Please try again';
        exit;
    }

    // agree and name
    if ($agreeLicense != 'on') {
        $_SESSION['err'] = 1;
        header("location:information.php");
        exit;
    }

    // information for mysql
    $host = 'localhost';
    $db_username = 'pan';
    $db_pwd = 'jA5R2P7fZySfT2Kt';
    $db_name = 'pan';
    // start connect to mysql db
    $conn = mysqli_connect($host, $db_username, $db_pwd, $db_name);
    $check_query = mysqli_query($conn, "select filepath from user where lightname='$newLightName'");
    $arr = mysqli_fetch_assoc($check_query);
    mysqli_free_result($check_query);
    mysqli_close($conn);

    if ($arr) {
        $_SESSION['err'] = 2;
        header("location:information.php");
        exit;
    } else {
        $_SESSION['newlightname'] = $newLightName;
    }

    // to step 2
    $_SESSION['step'] = 2;
    header("location:email.php");
    exit;
} elseif ($_SESSION['step'] == 2 || $_POST['resend'] == true) {
    // get email and set verify code
    if (isset($_POST['email'])) {
        // clean data
        function check_email($email)
        {
            $result = trim($email);
            if (filter_var($result, FILTER_VALIDATE_EMAIL)) {
                return "true";
            } else {
                return "false";
            }
        }
        if (check_email($_SESSION['newemail']) == false) {
            $_SESSION['err'] = 1;
            header("location:email.php");
            exit;
        }
        $_SESSION['newemail'] = $_POST['email'];
        $_SESSION['verifyCode'] = random_int(10000000, 99999999);
    }


    // send email
    require './phpmailer/Exception.php';
    require './phpmailer/PHPMailer.php';
    require './phpmailer/SMTP.php';

    $mail = new PHPMailer(true);
    try {
        //服务器配置
        $mail->CharSet = "UTF-8";                     //设定邮件编码
        $mail->SMTPDebug = 0;                        // 调试模式输出
        $mail->isSMTP();                             // 使用SMTP
        $mail->Host = 'smtp.163.com';                // SMTP服务器
        $mail->SMTPAuth = true;                      // 允许 SMTP 认证
        $mail->Username = 'yxg3pan@163.com';                // SMTP 用户名  即邮箱的用户名
        $mail->Password = 'KQMLRQFCWKTZVMIY';             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
        $mail->SMTPSecure = 'ssl';                    // 允许 TLS 或者ssl协议
        $mail->Port = 465;                            // 服务器端口 25 或者465 具体要看邮箱服务器支持

        $mail->setFrom('yxg3pan@163.com', 'yxg3pan');  //发件人
        $mail->addAddress($_SESSION['newemail']);  // 收件人
        $mail->addReplyTo('pan@yxg3.xyz', 'yxg3pan'); //回复的时候回复给哪个邮箱 建议和发件人一致

        $verifyCode = $_SESSION['verifyCode'];

        //Content
        $mail->isHTML(true);                                  // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容
        $mail->Subject = '邮箱验证';
        $mail->Body    = "<p>您的注册验证码是：</p><p style='font-size: 25px;'>$verifyCode</p><p>验证码在30分钟内有效，请根据页面提示输入。</p>" . date('Y-m-d H:i:s');
        $mail->AltBody = $verifyCode;

        $mail->send();
    } catch (Exception $e) {
        $_SESSION['err'] = 3;
        if ($_SESSION['step'] == 2) {
            header("location:email.php");
        } else {
            header("location:verify.php");
        }
        exit;
    }

    // to step 3
    $_SESSION['step'] = 3;
    header("location:verify.php");
    exit;
} elseif ($_SESSION['step'] == 3) {
    // verify
    if ($_POST['verifyCodeBack'] != $_SESSION['verifyCode']) {
        $_SESSION['err'] = 1;
        header("location:verify.php");
        exit;
    }
    // create account
    echo 'success';
    // show uuid
} else {
    echo 'Wrong. Unkonwn problem. Please restart your browser';
}
