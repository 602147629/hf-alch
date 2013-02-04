<?php

//  新增用户 等级分布
class Hapyfish2_Stat_Bll_UserLevel {

    public static function calcDayData($dt, $dir) {
        $strDate = $dt;
        $fileName = $dir . '/newuserlevel/' . $strDate . '/all-newuserlevel-' . $strDate . '.log';
        try {
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_UserLevel');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_UserLevel');
                return false;
            }
            $lines = explode("\n", $content);
            $array = array();
            foreach ($lines as $line) {
                $aryLine = explode("\t", $line);
                if ($aryLine[3] > 0) {
                    if (count($array[$aryLine[3]]) > 0) {
                        array_push($array[$aryLine[3]], $aryLine[2]);
                    } else {
                        $array[$aryLine[3]][] = $aryLine[2];
                    }
                }
            }
            $newarray = array();
            for ($i = 1; $i <= 100; $i++) {
                if (count($array[$i]) > 0) {
                    $count = count($array[$i]);
                } else {
                    $count = 0;
                }
                $num = $i + 1;
                if ($count > 0) {
                    foreach ($array[$i] as $v_array) {
                        if (count($array[$num]) > 0) {
                            if (in_array($v_array, $array[$num])) {
                                $count--;
                            }
                        }
                    }
                }
                $newarray[$i] = $count;
            }
            $fina_level = array_sum($newarray);
            $data['log_time'] = $dt;
            $data['level'] = json_encode($newarray);
            $data['fina_num'] = $fina_level;
            $dal = Hapyfish2_Stat_Dal_UserLevel::getDefaultInstance();
            $dal->insert($data);
        } catch (Exception $e) {
            info_log($e->getMessage(), 'stat_UserLevel');
            return false;
        }
        return true;
    }

    public static function getDayNewUserRecord($day) {
        $dal = Hapyfish2_Stat_Dal_UserLevel::getDefaultInstance();
        $result = $dal->getRow($day);
        return $result;
    }

    public static function getDayNewUserRecordTime($begin, $end) {
        $dal = Hapyfish2_Stat_Dal_UserLevel::getDefaultInstance();
        $result = $dal->listData($begin, $end);
        return $result;
    }

}

?>
