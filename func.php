<?php
//-----------------------------------------------------------------------------------------
// Desc: 框架自定义函数
// Version: 1.0.0
// Author: Menory
//-----------------------------------------------------------------------------------------

/**
 * @todo 终止程序运行(执行该函数之前必须有response)
 *
 * @return Void
 */
function _die()
{
    throw new \Exception('', PHP_INT_MAX);
}

/**
 * @todo 程序正常退出
 *
 * @return Void
 */
function _exit($msg = '')
{
    throw new \Exception($msg, 0);
}