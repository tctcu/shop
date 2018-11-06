<?php
require_once(dirname(__FILE__).'/PHPMailer/class.phpmailer.php');

class SendMail {
    function send($to_arr, $title, $content) {
        $mail = new PHPMailer(true);
        var_dump($mail);die;
        $mail->IsSMTP();
        $mail->CharSet='UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
        $mail->SMTPAuth = true; //开启认证
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 993;
        $mail->Host = "smtp.mxhichina.com";
        $mail->Username = "postmaster@clhuo.com";
        $mail->Password = "Wzzsl.com";//QDrenduan18
        $mail->From = "zsl@clhuo.com";
        $mail->FromName = "头号试玩-外放";
        foreach ($to_arr as $to){
            $mail->AddAddress($to);
        }
        $mail->Subject = $title;
        $mail->Body = $content;
        $mail->WordWrap = 80; // 设置每行字符串的长度
        $mail->IsHTML(true);
        $ret = $mail->Send();
        return $ret;
    }

}
?>