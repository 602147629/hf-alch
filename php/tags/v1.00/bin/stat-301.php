<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	$prefix1 = 301;
	$dtYesterday = strtotime("-1 day");
	$logDate = date('Ymd', $dtYesterday);
    //$logDate = '20110716';
    $dir = '/home/admin/data/stat-data/alchemy/'.$prefix1.'/';
    //home/admin/logs/alchemy/stat-data/301/

	$rst = Hapyfish2_Stat_Bll_Mercenary::calHireMercenary($logDate, $dir);
	echo $logDate."-$prefix1-" . ($rst ? 'OK' : 'NG');
}
catch (Exception $e) {
	err_log($e->getMessage());
}