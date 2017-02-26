<?php

$default_config_file_path = '/root/docker-system-performance-monitor/config-default.json';
$default_config_file = file_get_contents($default_config_file_path);
extract(json_decode($default_config_file, true));

$config_file_path = '/root/docker-system-performance-monitor/config.json';
if (is_file($config_file_path)) {
    $config_file = file_get_contents($config_file_path);
    extract(json_decode($config_file, true), EXTR_OVERWRITE);
}
