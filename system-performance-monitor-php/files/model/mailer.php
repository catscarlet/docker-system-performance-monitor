<?php

function sendMail($error_messages, $mailconfig)
{
    $error_description = $error_messages['error_description'];
    $hostname = $error_messages['hostname'];
    sendemailbysmtp('Your server('.$hostname.') may have performance problems', $error_description, '/tmp/formated_monitor.txt', $mailconfig);
}

function sendemailbysmtp($mailtitle, $mailcontent, $mailattachment, $mailconfig)
{
    require 'PHPMailer-5.2.22/class.phpmailer.php';
    require 'PHPMailer-5.2.22/class.smtp.php';

    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->Host = $mailconfig['smtpserver'];
    $mail->SMTPAuth = true;
    $mail->Username = $mailconfig['smtpuser'];
    $mail->Password = $mailconfig['smtppass'];
    $mail->SMTPSecure = $mailconfig['smtpsecure'];
    $mail->Port = $mailconfig['smtpserverport'];
    $mail->From = $mailconfig['smtpusermail'];
    foreach ($mailconfig['to'] as $to) {
        $mail->addAddress($to);
    }
    $mail->addAttachment($mailattachment, 'system-performance-monitor-php-log.txt');
    $mail->isHTML(true);
    $mail->Subject = $mailtitle;
    $mail->Body = $mailcontent;
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    if (!$mail->send()) {
        echo 'Mail could not be sent.'."\n";
        echo 'Error: '.$mail->ErrorInfo."\n";
    } else {
        echo 'Mail has been sent'."\n";
    }
}
