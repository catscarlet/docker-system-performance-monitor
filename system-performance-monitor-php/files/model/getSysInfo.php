<?php

function getSysInfo($config)
{
    $time = time();
    $mem = getmemfree();
    if ($config['main']['cpustat'] == 'sysstat') {
        $cpu = getcpustat();
    } elseif ($config['main']['cpustat'] == 'php_cpustat') {
        $cpu = php_cpustat();
    } else {
        $cpu = getcpustat();
    }
    $disk = getdiskusage();
    $hostname = getrealhostname($config['main']['host_alias']);
    //$psinfo = getps();

    $sysinfo = array(
        'TIME' => $time,
        'HOSTNAME' => $hostname,
        'CPUSTAT' => $cpu,
        'MEMFREE' => $mem,
        'DISKINFO' => $disk,
        //'PSINFO' => $psinfo,
        );

    return $sysinfo;
}

/* DISK STAT*/
function getdiskusage()
{
    exec('df', $dfoutput);
    foreach ($dfoutput as $key => $value) {
        if (preg_match('/(\S+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)%\s+(\S+)/', $value, $matches)) {
            if ($matches[1] == 'Filesystem') {
                /* Pass the head line . This will never run while there was a if_preg_match*/
                continue;
            }
            $tmp = array(
                '1K-blocks' => (int) $matches[2],
                'Used' => (int) $matches[3],
                'Avaliable' => (int) $matches[4],
                'Used_percent' => (int) $matches[5],
                'Mounted_on' => $matches[6],
            );

            $diskfilesystem[$matches[1]] = $tmp;
        }
    }

    return $diskfilesystem;
}

/* MEMORY STAT*/
function getmemfree()
{
    exec('free', $memfreeoutput);
    $tmp = null;
    foreach ($memfreeoutput as $key => $value) {
        if (preg_match('/\S+:\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/', $value, $tmp)) {
            $mem_matches = $tmp;
        } elseif (preg_match('/\-\/\+\sbuffers\/cache:\s+(\d+)\s+(\d+)/', $value, $tmp)) {
            $buffers_matches = $tmp;
        } elseif (preg_match('/Swap:\s+(\d+)\s+(\d+)\s+(\d+)/', $value, $tmp)) {
            $swap_matches = $tmp;
        }
    }

    if (!isset($buffers_matches)) {
        $tmp = array(
            'Mem_total' => $mem_matches[1],
            'Mem_used' => $mem_matches[2],
            'Mem_free' => $mem_matches[3],
            'Mem_shared' => $mem_matches[4],
            'Mem_buffers' => $mem_matches[5],
            'Mem_cached' => $mem_matches[6],
            'Swap_total' => $swap_matches[1],
            'Swap_used' => $swap_matches[2],
            'Swap_free' => $swap_matches[3],
        );
    } else {
        $tmp = array(
            'Mem_total' => $mem_matches[1],
            'Mem_used' => $mem_matches[2],
            'Mem_free' => $mem_matches[3],
            'Mem_shared' => $mem_matches[4],
            'Mem_buffers' => $mem_matches[5],
            'Mem_cached' => $mem_matches[6],
            'Buffers_used' => $buffers_matches[1],
            'Buffers_free' => $buffers_matches[2],
            'Swap_total' => $swap_matches[1],
            'Swap_used' => $swap_matches[2],
            'Swap_free' => $swap_matches[3],
        );
    }

    $memfree = $tmp;

    return $memfree;
}

/* CPU STAT*/
function getcpustat()
{
    exec('nproc', $nproc);
    $nproc = $nproc[0];

    for ($i = 0; $i < $nproc; ++$i) {
        unset($sarcpuoutput);
        exec('sar -P '.$i.' |tail -n 2', $sarcpuoutput);
        preg_match('/\d\d:\d\d:\d\d\s+\S+\s+\d+\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)/', $sarcpuoutput[0], $sarcpuarray);
        $tmp = array(
                'user' => (int) $sarcpuarray[1],
                'nice' => (int) $sarcpuarray[2],
                'sys' => (int) $sarcpuarray[3],
                'io' => (int) $sarcpuarray[4],
                'steal' => (int) $sarcpuarray[5],
                'idle' => (int) $sarcpuarray[6],
            );
        $cpustat['cpu'.$i] = $tmp;
    }

    return $cpustat;
}

function php_cpustat()
{
    exec('nproc', $nproc);
    $nproc = $nproc[0];
    exec("php /root/docker-system-performance-monitor/model/php_cpustat.php |tail -n $nproc", $php_cpustat_output);
    foreach ($php_cpustat_output as $i => $php_cpustat_value) {
        preg_match('/(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)/', $php_cpustat_value, $php_cpustat_array);
        $tmp2 = array(
                'user' => (int) $php_cpustat_array[2],
                'nice' => (int) $php_cpustat_array[3],
                'sys' => (int) $php_cpustat_array[4],
                'idle' => (int) $php_cpustat_array[5],
                'io' => (int) $php_cpustat_array[6],
                'irq' => (int) $php_cpustat_array[7],
                'softirq' => (int) $php_cpustat_array[8],
                'steal' => (int) $php_cpustat_array[9],
                'guest' => (int) $php_cpustat_array[10],
                'guest_nice' => (int) $php_cpustat_array[11],
            );
        $cpustat['cpu'.$i] = $tmp2;
    }

    return $cpustat;
}

function getps()
{
    exec('ps auxf', $psinfo);

    return $psinfo;
}

function getrealhostname($aliasname)
{
    if ($aliasname !== '') {
        return $aliasname;
    }
    if (file_exists('/root/docker-system-performance-monitor/hosthostname')) {
        exec('cat /root/docker-system-performance-monitor/hosthostname', $rst);
    } else {
        exec('cat /etc/hostname', $rst);
    }
    $hostname = $rst[0];

    return $hostname;
}
