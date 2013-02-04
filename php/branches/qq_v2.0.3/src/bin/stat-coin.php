<?php

define('ROOT_DIR', realpath('../'));
require(ROOT_DIR . '/bin/config.php');
try {
    //每日统计金币产出和消耗
    $date = date('Ymd', strtotime("-1 day"));
    $dir = '/home/www/stat_log/';
    Hapyfish2_Stat_Bll_CoinStatistics::calcDayData($date, $dir);
} catch (Exception $e) {
    info_log($e->getMessage(), 'stat.CoinStatistics');
}