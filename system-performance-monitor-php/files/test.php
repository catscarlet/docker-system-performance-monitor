<?php

$default_config_file_path = '/var/www/sunfu_git/docker-system-performance-monitor/system-performance-monitor-php/files/config-default.json';
$default_config_file = file_get_contents($default_config_file_path);
$array = json_decode($default_config_file, true);
print_r($array);
extract($array);
