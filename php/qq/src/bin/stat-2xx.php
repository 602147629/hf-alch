<?php
define('ROOT_DIR', realpath('../'));
require(ROOT_DIR . '/bin/config.php');
try {
	//修复奇迹飞机场没有气球
	$date  = date('Ymd', strtotime("-1 day"));
	$dir = '/home/admin/stat/data/alchemy/weibo';
	Hapyfish2_Stat_Bll_EventGift::calcGift($date, $dir);
	Hapyfish2_Stat_Bll_Stat2x::calcMutual($date, $dir);
	Hapyfish2_Stat_Bll_Stat2x::calcRepair($date, $dir);
	Hapyfish2_Stat_Bll_Stat2x::calcFight($date, $dir);
	Hapyfish2_Stat_Bll_Upgrade::calUpgrade($date, $dir);
	
}
catch (Exception $e) {
	info_log($e->getMessage(),'stat.2x');
}