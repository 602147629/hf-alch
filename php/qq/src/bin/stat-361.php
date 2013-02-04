<?php

define('ROOT_DIR', realpath('../'));

require(ROOT_DIR . '/bin/config.php');

    $v = $_SERVER["argv"][1];
    $calTime = $v;
    if ( !$calTime ) {
        $calTime = 0;
    }
try {
	$prefix1 = 361;
	$dtYesterday = strtotime("-0 day");
	$logDate = date('Ymd', $dtYesterday);
    //$logDate = '2011071600';
    $dir = '/home/admin/stat/data/alchemy/weibo/';
    //home/admin/logs/alchemy/stat-data/361/

    if ( $calTime == 0 ) {
	    $nowTime = time();
	    $nowHour = date('Y-m-d H:00:00', $nowTime);
	    $statTime = strtotime($nowHour);
	    $statTime -= 3600;
	    
	    $logDate = date('Ymd', $statTime);
		$rst = Hapyfish2_Stat_Bll_Statmainhour::calStatMainhour($logDate, $dir, $statTime);
		echo $logDate."-$prefix1-" . ($rst ? 'OK' : 'NG');
    }
    else {
	    $nowTime = time();
	    $nowHour = date('Y-m-d H:00:00', $nowTime);
	    $nowHourTime = strtotime($nowHour);
	    $day = date('Y-m-d 00:00:00', $nowTime);
	    $dayTime = strtotime($day);
	    $statTime = $dayTime;
	    for ( $i=0;$i<24;$i++ ) {
	    	if ( $statTime < $nowHourTime ) {
				$rst = Hapyfish2_Stat_Bll_Statmainhour::calStatMainhour($logDate, $dir, $statTime);
				echo $logDate."-$prefix1-" . ($rst ? 'OK' : 'NG');
				echo '<br/>';
				$statTime += 3600;
	    	}
	    }
    }

	//$rst = Hapyfish2_Stat_Bll_Statmainhour::calStatMainhour($logDate, $dir, $statTime);
	echo $logDate."-$prefix1-" . ($rst ? 'OK' : 'NG');
}
catch (Exception $e) {
	err_log($e->getMessage());
}
