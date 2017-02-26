<?php

require 'config.php';

require 'model/getSysInfo.php';

$sysinfo = getSysInfo($config);

$sysinfo_json = json_encode($sysinfo);

require 'model/saveSysInfo.php';
saveSysInfo($sysinfo_json, $config);

require 'model/checkSysInfo.php';
$error_messages = checkSysInfo($config);

if ($error_messages['error_code'] !== 0) {
    require 'model/sendNotice.php';
    sendNotice($error_messages, $config);
}

echo json_encode($error_messages, JSON_PRETTY_PRINT).','."\n";
