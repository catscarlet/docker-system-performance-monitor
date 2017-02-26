<?php

function saveSysInfo($sysinfo_json, $config)
{
    $filepath = $config['main']['filepath'];
    if (file_exists($filepath)) {
        jsonlogrotate($filepath);
    }

    sysinfosave($filepath, $sysinfo_json);
}

function sysinfosave($filepath, $sysinfo_json)
{
    $fopen = fopen($filepath, 'a') or die('File error !');
    fwrite($fopen, $sysinfo_json);
    fwrite($fopen, "\n");
    fclose($fopen);
}

/* logrotate */
function jsonlogrotate($filepath)
{
    $filepath_new = $filepath.'_new';
    exec("wc -l $filepath", $wc);
    preg_match('/(\d+)\s+(\S+)/', $wc[0], $match);
    $filelinecount = $match[1];
    if ($filelinecount > 480) {
        //echo 'Do jsonlogrotate'."\n";

        exec("tail -n 5 $filepath", $system_performance_monitor);
        $fopen = fopen($filepath_new, 'w') or die('File error !');
        foreach ($system_performance_monitor as $key => $value) {
            fwrite($fopen, $value);
            fwrite($fopen, "\n");
        }
        fclose($fopen);
        copy($filepath, $filepath.'_old');
        copy($filepath_new, $filepath);
    }
}
