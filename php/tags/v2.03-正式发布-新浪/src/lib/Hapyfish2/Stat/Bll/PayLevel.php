<?php

class Hapyfish2_Stat_Bll_PayLevel
{

    public static function calcPayLevel($dt, $dir)
    {
       $data = array();
       $data1 = array();
       $data2 = array();
    	$num = 0;
    	$pnum = 0;
        $fileName = $dir .'/payLevel/'. $dt . '/all-payLevel-' . $dt . '.log';
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
                $uid = $aryLine[2];
                $level = $aryLine[3];
                $gem = $aryLine[4];
                if(!isset($data['gem'][$gem][$level][$uid])){
                	$data1[$gem][$level] = 1;
                	$data['gem'][$gem][$level][$uid] = 1;
                }else{
                	$data1[$gem][$level] += 1;
                }
        		if(!isset($data['level'][$level][$uid])){
                	$data2[$level] = 1;
                	$data['level'][$level][$uid] = 1;
                }else{
                	$data2[$level] += 1;
                }
                //经营等级分布
            }
        }   catch (Exception $e) {
            return false;
        }
        $data = array('date'=>$dt, 'level'=>json_encode($data2), 'detail'=>json_encode($data1));
        $dal = Hapyfish2_Stat_Dal_PayLevel::getDefaultInstance();
        $dal->inset('day_PayLevel',$data);
    }
    
    public static function calcGemLevel($dt, $dir)
    {
       	$data = array();
       	$data1 = array();
        $data2 = array();
        $data3 = array();
    	$num = 0;
    	$pnum = 0;
        $fileName = $dir .'/GemLevel/'. $dt . '/all-GemLevel-' . $dt . '.log';
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
                if (empty($line) || $line == '-100') {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $uid = $aryLine[2];
                $level = $aryLine[3];
                $gem = $aryLine[4];
                $type = $aryLine[5];
                if(!isset($data[$level])){
                	$data[$level] = $gem;
                }else{
                	$data[$level] += $gem;
                }
                if(!isset($data1[$type])){
                	$data1[$type] = $gem;
                        $data2[$type] = 1;
                }else{
                	$data1[$type] += $gem;
                        $data2[$type] += 1;
                }
                if(count($data3[$type])> 0 ){
                     array_push($data3[$type], $uid);
                } else{
                    $data3[$type][] = $uid;
                }
            }
        }   catch (Exception $e) {
            return false;
        }
        $list = array('date' => $dt, 'level' => json_encode($data), 'type' => json_encode($data1),'num'=>  json_encode($data2),'count'=>json_encode($data3));
        $dal = Hapyfish2_Stat_Dal_PayLevel::getDefaultInstance();
        $dal->inset('day_GemLevel',$list);
    }
    
    public static function calcBuyLevel($dt, $dir)
    {
    	$data = array();
    	$num = 0;
    	$pnum = 0;
        $fileName = $dir .'/buyLevel/'. $dt . '/all-buyLevel-' . $dt . '.log';
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
                if (empty($line) || $line == '-100') {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $uid = $aryLine[2];
                $level = $aryLine[3];
                $cid = $aryLine[4];
                $num = $aryLine[5];
                if(!isset($data[$cid][$level])){
                	$data[$cid][$level] = $num;
                }else{
                	$data[$cid][$level] += $num;
                }
            }
        }   catch (Exception $e) {
            return false;
        }
        $data = array('date'=>$dt, 'list'=>json_encode($data));
        $dal = Hapyfish2_Stat_Dal_PayLevel::getDefaultInstance();
        $dal->inset('day_BuyLevel',$data);
    }
    
    public static function getPayLevel($dt)
    {
    	$dal = Hapyfish2_Stat_Dal_PayLevel::getDefaultInstance();
    	$data = $dal->getPayLevel($dt);
    	return $data;
    }
    
    public static function getGemLevel($dt)
    {
    	$dal = Hapyfish2_Stat_Dal_PayLevel::getDefaultInstance();
    	$data = $dal->getGemLevel($dt);
    	return $data;
    }
    
    public static function getBuyLevel($dt)
    {
    	$dal = Hapyfish2_Stat_Dal_PayLevel::getDefaultInstance();
    	$data = $dal->getBuyLevel($dt);
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
}