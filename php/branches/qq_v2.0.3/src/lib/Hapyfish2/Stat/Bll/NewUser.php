<?php

class Hapyfish2_Stat_Bll_NewUser
{

    public static function calcNewUser($dt, $dir)
    {
    	$data = array();
    	$list = array();
        $fileName1 = $dir .'/newuser/'. $dt . '/all-newuser-' . $dt . '.log';
        try {
        	$content1 = file_get_contents($fileName1);
            $lines = explode("\n", $content1);
         foreach ($lines as $line) {
                if (empty($line) || $line == '-100') {
    				continue;
    			}
                $aryLine = explode("\t", $line);
                $uid = $aryLine[2];
                $step = $aryLine[3];
                if(!isset($data[$step][$uid])){
                	$data[$step][$uid] = 1;
                	$list[$step] += 1;
                }
            }
        }   catch (Exception $e) {
            return false;
        }
        $data = array('date'=>$dt,'list'=>json_encode($list));
        $dal = Hapyfish2_Stat_Dal_EventGift::getDefaultInstance();
        $dal->insert('day_newUser',$data);
    }
    
    public static function getNewUser($day)
    {
    	$info = array();
    	$dal = Hapyfish2_Stat_Dal_EventGift::getDefaultInstance();
    	$data = $dal->getNewUser($day);
    	$list = json_decode($data['list'],true);
    	for($i=1;$i<=28;$i++){
    		if(isset($list[$i])){
    			$info[$i] = $list[$i];
    		}else{
    			$info[$i] = 0;
    		}
    	}
    	$data['list'] = $info;
    	return $data;
    }
    
    public static function calcUserHelp($dt, $dir)
    {
    	$data = array();
    	$list = array();
        $fileName1 = $dir .'/userHelp/'. $dt . '/all-userHelp-' . $dt . '.log';
        try {
        	$content1 = file_get_contents($fileName1);
            $lines = explode("\n", $content1);
         foreach ($lines as $line) {
                if (empty($line) || $line == '-100') {
    				continue;
    			}
                $aryLine = explode("\t", $line);
                $uid = $aryLine[2];
                $step = $aryLine[3];
                if(!isset($data[$step][$uid])){
                	$data[$step][$uid] = 1;
                	$list[$step] += 1;
                }
            }
        }   catch (Exception $e) {
            return false;
        }
        $data = array('date'=>$dt,'list'=>json_encode($list));
        $dal = Hapyfish2_Stat_Dal_EventGift::getDefaultInstance();
        $dal->insert('day_help',$data);
    }
    
    public static function getUserHelp($day)
    {
    	$info = array();
    	$dal = Hapyfish2_Stat_Dal_EventGift::getDefaultInstance();
    	$data = $dal->getHelp($day);
    	$list = json_decode($data['list'],true);
    	for($i=1;$i<=17;$i++){
    		if(isset($list[$i])){
    			$info[$i] = $list[$i];
    		}else{
    			$info[$i] = 0;
    		}
    	}
    	$data['list'] = $info;
    	return $data;
    }
}