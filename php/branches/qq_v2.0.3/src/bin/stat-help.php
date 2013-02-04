<?php
define('ROOT_DIR', realpath('../'));
require(ROOT_DIR . '/bin/config.php');
try {
	//修复奇迹飞机场没有气球
	$date  = date('Ymd', strtotime("-1 day"));
	$dir = '/home/www/stat_log';
	Hapyfish2_Stat_Bll_NewUser::calcUserHelp($date, $dir);
}
catch (Exception $e) {
	info_log($e->getMessage(),'stat.userhelp');
}