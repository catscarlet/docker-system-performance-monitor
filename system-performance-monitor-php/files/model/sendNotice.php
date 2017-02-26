<?php

function sendNotice($error_messages, $config)
{
    if ($config['email']['on']) {
        require 'mailer.php';
        sendMail($error_messages, $config['email']);
    }
}
