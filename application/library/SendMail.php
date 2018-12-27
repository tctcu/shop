<?php
require_once(dirname(__FILE__).'/PHPMailer/class.phpmailer.php');

class SendMail {

    function send($to_arr, $title, $content) {
        $mail_config = Yaf_Registry::get("config")->get('mail');
        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->CharSet='UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
        $mail->SMTPAuth = true; //开启认证
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->Host = $mail_config->host;
        $mail->Username = $mail_config->username;
        $mail->Password = $mail_config->password;
        $mail->From = $mail_config->from;
        $mail->FromName = $mail_config->fromName;
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