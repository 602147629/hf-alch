<?php

define('ROOT_DIR', realpath('../'));
require(ROOT_DIR . '/bin/config.php');
try {
    //每日统计金币产出和消耗
    $date = date('Ymd', strtotime("-1 day"));
    $dir = '/home/admin/stat/data/alchemy/weibo/';
    Hapyfish2_Stat_Bll_SendDiamond::calcDayData($date, $dir);
} catch (Exception $e) {
    info_log($e->getMessage(), 'stat.SendDiamond');
}