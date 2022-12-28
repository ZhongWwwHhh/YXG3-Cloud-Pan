<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__FILE__) . '/phpmailer/Exception.php';
require_once dirname(__FILE__) . '/phpmailer/PHPMailer.php';
require_once dirname(__FILE__) . '/phpmailer/SMTP.php';

function sendEmail($title, $content, $reciver)
{
    $mail = new PHPMailer(true);
    try {
        $mail->CharSet = "UTF-8";
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.163.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'yxg3pan@163.com';
        $mail->Password = 'KQMLRQFCWKTZVMIY';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('yxg3pan@163.com', 'yxg3pan');
        $mail->addAddress($reciver);
        $mail->addReplyTo('pan@yxg3.xyz', 'yxg3pan');

        //Content
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body    = $content . date('Y-m-d H:i:s');
        $mail->AltBody = $content . date('Y-m-d H:i:s');

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
