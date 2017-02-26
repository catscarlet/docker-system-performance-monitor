<?php

function checkSysInfo($config)
{
    $filepath = $config['main']['filepath'];

    if ($config['main']['test']) {
        $error_description = 'Test System Performance Alerter.';
    } else {
        $error_description = '';
    }

    $readcpustathistory = $config['check']['cpu']['readcpustathistory'];
    $threshold_times = $config['check']['cpu']['threshold_times'];
    $threshold_percent = $config['check']['cpu']['threshold_percent'];

    exec("tail -n $readcpustathistory $filepath", $system_performance_monitor);

    $readhistorymax = min(count($system_performance_monitor), $readcpustathistory);
    $i = $readhistorymax;
    foreach ($system_performance_monitor as $key => $value) {
        --$i;
        $monitor[$i] = json_decode($value, true);
    }
    //print_r($monitor);
    $time = $monitor[0]['TIME'];

    if ($config['check']['cpu']['on']) {
        $error_description .= cpucheck($monitor, $threshold_times, $threshold_percent);
    }

    if ($config['check']['memory']['on']) {
        $error_description .= memcheck($monitor, $readhistorymax);
    }

    if ($config['check']['disk']['on']) {
        $error_description .= dfcheck($monitor);
    }

    if ($error_description !== '') {
        $hostname = $monitor[0]['HOSTNAME'];
        $error_messages = array('time' => $time ,'hostname' => $hostname,'error_code' => 1, 'error_description' => $error_description);

        $attachmentsInfo = array('error_messages' => $error_messages ,'monitor' => json_decode($value, true));

        $f_open = fopen('/tmp/formated_monitor.txt', 'w') or die('Temp File create failed.');
        fwrite($f_open, json_encode($attachmentsInfo, JSON_PRETTY_PRINT));
        fclose($f_open);
    } else {
        $error_messages = array('time' => $time ,'error_code' => 0, 'error_description' => 'Your system running normally.');
    }

    return($error_messages);
}

function memcheck($monitor, $readhistorymax)
{
    if ($readhistorymax > 1) {
        if ($monitor[0]['MEMFREE']['Swap_used'] > $monitor[1]['MEMFREE']['Swap_used']) {
            return 'Swap has been used. You system may be out of memory.'."\n";
        }
    }
}

function dfcheck($monitor)
{
    foreach ($monitor[0]['DISKINFO'] as $filesystem => $filesysteminfo) {
        if ($filesysteminfo['Used_percent'] > 90) {
            return 'Filesystem "'.$filesystem.' "used more than 90%.'."\n";
        }
    }
}

function cpucheck($monitor, $threshold_times, $threshold_percent)
{
    $cpu_warning_count = 0;
    foreach ($monitor as $monitorhistoryID => $monitorhistory) {
        foreach ($monitorhistory['CPUSTAT'] as $cpuid => $cpustat) {
            $cpu_idlepercent = $cpustat['idle'];
            if ($cpu_idlepercent < $threshold_percent) {
                if ($cpustat['user'] != 0 || $cpustat['sys'] != 0 || $cpustat['io'] != 0 || $cpustat['idle'] != 0) {
                    ++$cpu_warning_count;
                }
            }
        }
    }

    if ($cpu_warning_count >= $threshold_times) {
        return 'CPU_IDLE may be less than '.$threshold_percent.' since last '.$threshold_times.' times check.'."\n";
    }
}
