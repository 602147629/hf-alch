<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	$prefix1 = 341;
	$dtYesterday = strtotime("-1 day");
	$logDate = date('Ymd', $dtYesterday);
    //$logDate = '20110716';
    $dir = '/home/admin/stat/data/alchemy/kaixin/'.$prefix1.'/';
    //home/admin/logs/alchemy/stat-data/341/

	$rst = Hapyfish2_Stat_Bll_Mix::calMix($logDate, $dir);
	echo $logDate."-$prefix1-" . ($rst ? 'OK' : 'NG');
}
catch (Exception $e) {
	err_log($e->getMessage());
}