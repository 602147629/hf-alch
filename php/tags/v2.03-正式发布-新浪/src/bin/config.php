<?php

require (ROOT_DIR . '/app/config/define.php');
set_include_path(LIB_DIR . ':' . get_include_path());

ini_set('display_errors', false);

date_default_timezone_set('Asia/Shanghai');

// register autoload class function
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

function err_log($msg)
{
	$logfile = LOG_DIR . '/err.bin.log';

	$time = date('Y-m-d H:i:s');

	file_put_contents($logfile, $time . "\t" . $msg . "\r\n", FILE_APPEND);
}

function debug_log($msg)
{
	$logfile = LOG_DIR . '/debug.bin.log';

	$time = date('Y-m-d H:i:s');

	file_put_contents($logfile, $time . "\t" . $msg . "\r\n", FILE_APPEND);
}

function info_log($msg, $prefix = 'default')
{
	$logfile = LOG_DIR . '/info.' . $prefix . '.bin.log';

	$time = date('Y-m-d H:i:s');

	file_put_contents($logfile, $time . "\t" . $msg . "\r\n", FILE_APPEND);
}
