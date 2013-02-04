<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

try {
	$dtYesterday = strtotime("-1 day");
	$logDate = date('Ymd', $dtYesterday);
    //$logDate = '20110716';
    $dir = '/home/admin/stat/data/alchemy/weibo/useraction/';///home/admin/logs/weibo/stat-data/useraction/

	$rst = Hapyfish2_Stat_Bll_UserAction::calcDayData($logDate, $dir);	
	echo $logDate."-useraction-" . ($rst ? 'OK' : 'NG');

}
catch (Exception $e) {
	err_log($e->getMessage());
}