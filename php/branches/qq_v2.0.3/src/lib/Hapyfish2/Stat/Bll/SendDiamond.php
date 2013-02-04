<?php

//  每日金币产出 消耗 统计
class Hapyfish2_Stat_Bll_SendDiamond {

    public static function calcDayData($dt, $dir) {
        $strDate = $dt;
        $fileName = $dir . 'adddemond/' . $strDate . '/all-adddemond-' . $strDate . '.log';
        try {
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_adddemond');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_adddemond');
                return false;
            }
            $lines = explode("\n", $content);
            $dataTodayGold = $dataTodayCoinTemp3 = array();
            foreach ($lines as $line) {
                $aryLine = explode("\t", $line);
                if ($aryLine[0] != '') {
                    if($aryLine[5] == 1 || $aryLine[5] == 2 ){
                        $dataTodayCoinTemp1+=$aryLine[3];  //计算3日宝石 和 cdkey 赠送的宝石量
                    }
                    $dataTodayCoinTemp3[$aryLine[5]]+=$aryLine[3];
                }
            }
            $dataTodayGold['log_time'] = $dt;
            $dataTodayGold['sendDiamond'] = $dataTodayCoinTemp1;
            $dataTodayGold['sendDiamondJson'] = json_encode($dataTodayCoinTemp3);
            $dal = Hapyfish2_Stat_Dal_SendDiamond::getDefaultInstance();
            $dal->insert($dataTodayGold);
        } catch (Exception $e) {
            info_log($e->getMessage(), 'stat_CoinStatistics');
            return false;
        }
        return true;
    }

    public static function getSendDiamondRecord($day) {
        $dal = Hapyfish2_Stat_Dal_SendDiamond::getDefaultInstance();
        $result = $dal->getRow($day);
        return $result;
    }

    public static function getSendDiamondRecordTime($begin, $end) {
        $dal = Hapyfish2_Stat_Dal_SendDiamond::getDefaultInstance();
        $result = $dal->listData($begin, $end);
        return $result;
    }

}

?>
