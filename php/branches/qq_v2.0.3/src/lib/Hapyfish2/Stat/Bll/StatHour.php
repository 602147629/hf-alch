<?php

class Hapyfish2_Stat_Bll_StatHour
{

    public static function calcUserHour($dt, $dir)
    {
       $data = array();
       $data1 = array();
       $data2 = array();
       $data3 = array();
       $num1 = 0;
       $num2 = 0;
       $num3 = 0;
        $fileName = $dir .'/userHour/'. $dt . '/all-userHour-' . $dt . '.log';
        for($i=0;$i<=23;$i++){
        	$data1[$i.':00'] = 0;//全部玩家
        	$data2[$i.':00'] = 0;
        	$data3[$i.':00'] = 0;//第二天登陆的新用户
        }
		$today = strtotime($dt.' 00:00:00');
		$yesday = strtotime($dt.' 00:00:00 -1day');
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'payLevel');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'payLevel');
                return false;
            }
            $lines = explode("\n", $content);
        	foreach ($lines as $line) {
                if (empty($line) || $line == '-100') {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $stTime = $aryLine[0];//登陆时间
                $uid = $aryLine[2];
                $isPayUser = Hapyfish2_Alchemy_HFC_User::getTotalPay($uid);
                $joinTime = $aryLine[3];//加入游戏时间
                $hour = date('G',$stTime);
                $data1[$hour.':00'] += 1;
                if($joinTime <= $today){
                	$data3[$hour.':00'] += 1;
                }
                if($isPayUser > 0){
                	$data2[$hour.':00'] += 1;
                }
                //经营等级分布
            }
        }   catch (Exception $e) {
            return false;
        }
        return array('allUser'=>$data1,'payUser'=>$data2,'oldUser'=>$data3);
    }
    
 	public static function calcDauHour($dt, $dir)
    {
       $data = array();
       $data1 = array();
       $data2 = array();
       $data3 = array();
       $num1 = 0;
       $num2 = 0;
       $num3 = 0;
        $fileName = $dir .'/101/'. $dt . '/all-101-' . $dt . '.log';
        for($i=0;$i<=23;$i++){
        	$data1[$i.':00'] = 0;//全部玩家
        	$data2[$i.':00'] = 0;
        	$data3[$i.':00'] = 0;//第二天登陆的新用户
        }
		$today = strtotime($dt.' 00:00:00');
		$yesday = strtotime($dt.' 00:00:00 -1day');
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'payLevel');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'payLevel');
                return false;
            }
            $lines = explode("\n", $content);
        	foreach ($lines as $line) {
                if (empty($line) || $line == '-100') {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $stTime = $aryLine[0];//登陆时间
                $uid = $aryLine[2];
                $joinTime = $aryLine[3];//加入游戏时间
                $hour = date('G',$stTime);
                $data1[$hour.':00'] += 1;
                if($joinTime <= $today && $joinTime >= $yesday){
                	$data3[$hour.':00'] += 1;
                }
                //经营等级分布
            }
        }   catch (Exception $e) {
            return false;
        }
        return array('allUser'=>$data1,'oldUser'=>$data3);
    }
    
    public static function calcNewUser($dt, $dir)
    {
       $data1 = array();
       $num1 = 0;
        $fileName = $dir .'/100/'. $dt . '/all-100-' . $dt . '.log';
        for($i=0;$i<=23;$i++){
        	$data1[$i.':00'] = 0;//全部玩家
        }
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'payLevel');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'payLevel');
                return false;
            }
            $lines = explode("\n", $content);
        	foreach ($lines as $line) {
                if (empty($line) || $line == '-100') {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $stTime = $aryLine[0];//登陆时间
                $uid = $aryLine[2];
                $hour = date('G',$stTime);
                $data1[$hour.':00'] += 1;
                }
                //经营等级分布
        }   catch (Exception $e) {
            return false;
        }
       return $data1;
    }
    
 	public static function calcPayLevel($dt, $dir)
    {
       $data = array();
       $data1 = array();
       $data2 = array();
       $data3 = array();
       $num = 0;
       $pnum = 0;
        $fileName = $dir .'/payLevel/'. $dt . '/all-payLevel-' . $dt . '.log';
     	for($i=0;$i<=23;$i++){
        	$data[$i.':00']['num'] = 0;
            $data[$i.':00']['times'] = 0;
            $data[$i.':00']['total'] = 0;
        }
        info_log(json_encode($data),'pay');
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'payLevel');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'payLevel');
                return false;
            }
            $lines = explode("\n", $content);
        	foreach ($lines as $line) {
                if (empty($line) || $line == '-111') {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $stTime = $aryLine[0];//登陆时间
                $hour = date('G',$stTime);
                $uid = $aryLine[2];
                $gem = $aryLine[4]/100;
                if(!isset($data1[$hour]['num'][$uid])){
                	$data1[$hour]['num'][$uid] = 1;
                	$data[$hour.':00']['num'] += 1;
                }
                $data[$hour.':00']['times'] += 1;
                $data[$hour.':00']['total'] += $gem;
                //经营等级分布
            }
        }   catch (Exception $e) {
            return false;
        }
        return $data;
    }
    
    public static function calcStatHour($dt, $dir)
    {
    	$old = self::calcUserHour($dt, $dir);
    	$new = self::calcNewUser($dt, $dir);
    	$pay = self::calcGem($dt, $dir);
    	$dau = self::calcDauHour($dt, $dir);
    	$data = array('date'=>$dt,'allUser'=>json_encode($old['allUser']),'payUser'=>json_encode($old['payUser']),'oldUser'=>json_encode($old['oldUser']),'newUser'=>json_encode($new),'pay'=>json_encode($pay),'dau'=>json_encode($dau));
        $dal = Hapyfish2_Stat_Dal_StatHour::getDefaultInstance();
        $dal->insert('day_hour_user',$data);
    }
    
    public static function calcGem($dt, $dir)
    {
    	 $data = array();
       $data1 = array();
       $data2 = array();
       $data3 = array();
       $num = 0;
       $pnum = 0;
        $fileName = $dir .'/GemLevel/'. $dt . '/all-GemLevel-' . $dt . '.log';
     	for($i=0;$i<=23;$i++){
        	$data[$i.':00']['num'] = 0;
            $data[$i.':00']['times'] = 0;
            $data[$i.':00']['total'] = 0;
        }
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'GemLevel');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'GemLevel');
                return false;
            }
            $lines = explode("\n", $content);
        	foreach ($lines as $line) {
                if (empty($line) || $line == '-111') {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $stTime = $aryLine[0];//登陆时间
                $hour = date('G',$stTime);
                $uid = $aryLine[2];
                $gem = $aryLine[4];
                if(!isset($data1[$hour]['num'][$uid])){
                	$data1[$hour]['num'][$uid] = 1;
                	$data[$hour.':00']['num'] += 1;
                }
                $data[$hour.':00']['times'] += 1;
                $data[$hour.':00']['total'] += $gem;
                //经营等级分布
            }
        }   catch (Exception $e) {
            return false;
        }
        return $data;
    }
    
    
    public static function getBuyItem()
    {
    	$data = Hapyfish2_Alchemy_Cache_Basic::getGoodsList();
    	foreach ($data as $item) {
    		if($item['can_buy'] == 1){
	    		$temp = array(
					'cid' => $item['cid'],
					'name' => $item['name'],
				);
				$info[] = $temp;
    		}
		}
		return $info;
    }
    
    public static function getUserHour($day)
    {
    	$dal = Hapyfish2_Stat_Dal_StatHour::getDefaultInstance();
    	$data = $dal->getHour($day);
    	return $data;
    }
}