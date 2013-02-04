<?php

//  每日金币产出 消耗 统计
class Hapyfish2_Stat_Bll_CoinStatistics {

    public static function calcDayData($dt, $dir) {
        $strDate = $dt;
        $fileName = $dir . 'coin/' . $strDate . '/all-coin-' . $strDate . '.log';
        try {
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_DayCoinStatistics');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_DayCoinStatistics');
                return false;
            }
            $lines = explode("\n", $content);
            $dataTodayGold = $dataTodayCoinTemp3 = $dataTodayCoinTemp4 = array();
            foreach ($lines as $line) {
                $aryLine = explode("\t", $line);
                //判断 金币是产出 还是 消耗
                if ($aryLine[0] != '') {
                    if ($aryLine[5] != 0) {
                        if ($aryLine[4] == 1) {
                            $dataTodayCoinTemp1 += $aryLine[3];
                            $dataTodayCoinTemp3[$aryLine[5]]+=$aryLine[3];
                        } else {
                            $dataTodayCoinTemp2 +=$aryLine[3];
                            $dataTodayCoinTemp4[$aryLine[5]]+=$aryLine[3];
                        }
                    }
                }
            }
            $dataTodayGold['log_time'] = $dt;
            $dataTodayGold['goldOutput'] = $dataTodayCoinTemp1;
            $dataTodayGold['goldConsume'] = $dataTodayCoinTemp2;
            $dataTodayGold['goldType'] = json_encode($dataTodayCoinTemp3);
            $dataTodayGold['goldType1'] = json_encode($dataTodayCoinTemp4);
            $dal = Hapyfish2_Stat_Dal_CoinStatistics::getDefaultInstance();
            $dal->insert($dataTodayGold);
        } catch (Exception $e) {
            info_log($e->getMessage(), 'stat_CoinStatistics');
            return false;
        }
        return true;
    }

    public static function getDayCoinRecord($day) {
        $dal = Hapyfish2_Stat_Dal_CoinStatistics::getDefaultInstance();
        $result = $dal->getRow($day);
        return $result;
    }

    public static function getDayCoinRecordTime($begin, $end) {
        $dal = Hapyfish2_Stat_Dal_CoinStatistics::getDefaultInstance();
        $result = $dal->listData($begin, $end);
        return $result;
    }

}

?>
