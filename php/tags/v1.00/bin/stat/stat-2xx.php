<?php
define('ROOT_DIR', realpath('../../'));
require(ROOT_DIR . '/bin/config.php');
try {
	//修复奇迹飞机场没有气球
	$data = 20120706;
	$dir = STAT_LOG_DIR;
	Hapyfish2_Stat_Bll_EventGift::calcGift($data, $dir);
//	Hapyfish2_Stat_Bll_Stat2x::calcMutual($data);
//	Hapyfish2_Stat_Bll_Stat2x::calcRepair($data);
//	Hapyfish2_Stat_Bll_Stat2x::calcFight($data);
	
}
catch (Exception $e) {
	err_log($e->getMessage());
}