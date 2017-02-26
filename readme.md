# README

A performance monitor.

## How to use

### Basic

Use the docker-compose.yml to start.

You can use your `/root/docker-system-performance-monitor/config.json`, and the compose file will mount it to the container.

Edit the `docker-compose.yml` if you want to change the `config.json's` path.

### Advance

#### cpustat

cpustat can be set only `php_cpustat` or `sysstat`

- `php_cpustat`: Use php_cpustat to monitor the cpu. See: <https://github.com/catscarlet/cpustat>
- `sysstat`: Use sar to monitor the cpu. See: <https://github.com/sysstat/sysstat>

## Logs

Use `docker logs` to check the monitor log. Notice that this log didn't show the detail, only a short description about which part(cpu, memory, etc) has performance problems.

Set a email to config.json to receive the detail about your system.

## Notice

### The Build Setup

This project use `mirrors.aliyun.com` as apt's source. **Change it if you have a better source.**

### The Memory Monitor

Memory monitor now only detect your SWAP. If it is more than the last result, it alert.

## Links

This project's idea is from my old project <https://github.com/catscarlet/system_performance_monitor>, but this one use Docker to deploy, much easier!
