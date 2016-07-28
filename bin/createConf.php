<?php
/**
 * @todo 自动构建配置文件脚本
 * @author Menory
 * @date 2016年7月28日 03:02:49
 */

$appName = $argv[1];

echo "\033[32mCreating the 'etc/", $appName, ".ini' file\033[0m \n";


$array = [
    ['explain' => 'host (127.0.0.1):', 'default' => '127.0.0.1'],
    ['explain' => 'port (9500):', 'default' => '9500'],
    ['explain' => 'document_root (/home/nginx/apps):', 'default' => '/home/nginx/apps']
];

$replace = [];

foreach ($array as $val) {
    echo "\033[32m", $val['explain'], " \033[0m";
    $in = trim(fgets(STDIN));
    if (!empty($in))
        $replace[] = $in;
    else
        $replace[] = $val['default'];
}

$ini = <<<php
[server]
host={{host}}
port={{port}}
; 应用根目录
document_root={{documentRoot}}
protocol=http
[db]
; 数据库配置
dsn='mysql:host=localhost;dbname=wx'
user=root
passwd=123456
[redis]
host=127.0.0.1
port=6379
[person_settings]
; 个人信息
email='hhw_menory@yeah.net'
author='黄惠威'
php;

$ini = str_replace(
    ['{{host}}', '{{port}}', '{{documentRoot}}'],
    $replace,
    $ini
);

// echo $ini;
file_put_contents('../etc/'.$appName.'.ini', $ini);
echo "\033[31m\n\nYou've successfully created ", $appName, ".ini on etc\033[0m \n\n";

