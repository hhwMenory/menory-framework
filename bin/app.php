<?php
/**
 * @todo 应用启动脚本
 * @author Menory
 * @date 2016年4月14日 23:32:50
 */

// 开启严格模式
declare (strict_types = 1);

// 接收要启动的应用名称
$appName = $argv[1];

// 是否传入 app_name 
if (empty($appName))
    exit('Error: app_name not null!'.PHP_EOL);

// PHP版本
if (version_compare(PHP_VERSION, '7.0.0', '<'))
    exit('Error: php version >=7.0.0'.PHP_EOL);

// 是否安装了 swoole 扩展
if (!get_extension_funcs('swoole'))
    exit('Error: need swoole extension'.PHP_EOL);

// 设置时区 
date_default_timezone_set('PRC');

// 定义 root_PATH 目录
define('ROOT_PATH', __DIR__);

// 定义应用名称
define('APP_NAME', $appName);

// 导入 composer
$loader = require ROOT_PATH.'/vendor/autoload.php';

// 初始化，获取在 worker 启动之前加载代码 worker 共享， 且不会再加载
$php = \Menory\Menory::getInstance();

// 加载系统配置
$sysConfigs = \Menory\Config::loadSysConfigs();

// 定义应用根目录
define('APP_PATH', $sysConfigs['server']['document_root'].'/'.APP_NAME);

// app 自动加载
$loader->addPsr4('App\\Lib\\', APP_PATH.'/Libs/');
$loader->addPsr4('Hooks\\', APP_PATH.'/Hooks/');
$loader->addPsr4('Controller\\', APP_PATH.'/Controller/');

// 加载钩子
\Menory\Hook::loadHooks(\Menory\Config::loadAppConfigs('Hook'));

// 加载内置函数
require ROOT_PATH.'/func.php';

// 接管致命错误处理
register_shutdown_function(function () {
    $error = error_get_last();
    \Menory\Hook::callUniqueHook('response', $error['message']);
});

// 接管运行错误处理
set_error_handler(function ($errorNO, $errorStr, $errorFile, $errorLine) {
    $error = "runing error: [#{$errorNO}] msg: {$errorStr}, {$errorFile} {$errorLine}\n";
    var_dump($error);
    // 暂无记录日志
    \Menory\Hook::callUniqueHook('response', $error);
    _die();
});

// 启动 protocol_server
$map = [
    'http'       => 'HttpServer',
    'web_socket' => 'WebSocketServer'
];
$protocol = $sysConfigs['server']['protocol'];
if (isset($map[$protocol]))
    $protocolClass = '\\Menory\\Protocol\\'.$map[$protocol];
else 
    exit('Error: protocol service is not supported!'.PHP_EOL);
$protocolSvr = new $protocolClass($sysConfigs);
$protocolSvr->run();
