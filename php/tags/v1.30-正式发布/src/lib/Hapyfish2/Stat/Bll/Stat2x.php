<?php

class Hapyfish2_Stat_Bll_Stat2x
{

    public static function calcFight($dt, $dir)
    {
        $strDate = $dt;
        $help = self::_getHelp($dt, $dir);
        $entCopy = self::_getEnterCopy($dt, $dir);
        $Monster = self::_getMonster($dt, $dir);
        $boss = self::_getBoss($dt, $dir);
        $mater = self::_getMater($dt, $dir);
        $totalStat = array();
        $totallevel = array();
        $totalStat['date'] = $dt;
        $totallevel['date'] = $dt;
        $dal = Hapyfish2_Stat_Dal_Stat2x::getDefaultInstance();
        foreach($entCopy as $map => $v){
        	$totalStat['map'] = $map;
        	$totalStat['copy'] = $v['enter']['num'];
        	$totalStat['copypnum'] = $v['enter']['pnum'];
        	$totalStat['monster'] = $Monster[$map]['totalnum'];
        	$totalStat['monspnum'] = $Monster[$map]['num'];
        	$totalStat['monwin'] = $Monster[$map]['win'];
        	$totalStat['boss'] = $boss[$map]['totalnum'];
        	$totalStat['bosspnum'] = $boss[$map]['num'];
        	$totalStat['bosswin'] = $boss[$map]['win'];
        	$totalStat['mm'] = $mater[$map][1]['num'];
        	$totalStat['mmp'] = $mater[$map][1]['pnum'];
        	$totalStat['cm'] = $mater[$map][2]['num'];
        	$totalStat['cmp'] = $mater[$map][2]['pnum'];
        	
        	$levelinfo = array();
        	$userLevelinfo = array();
        	foreach($v['level'] as $k1=>$v1){
        		$levelinfo['list'][$k1] = $v1['num'];
        		$levelinfo['totla'] += $v1['num'];
        	}
        	foreach($v['userlevel'] as $k1=>$v1){
        		$userLevelinfo['list'][$k1] = $v1['num'];
        		$userLevelinfo['total'] += $v1['num'];
        	}
        	$totalStat['level'] = json_encode($levelinfo);
        	$totalStat['userLevel'] = json_encode($userLevelinfo);
        	$dal->insert('day_fight', $totalStat);
        }
		
        $moninfo = array();
        $moninfo['date'] = $dt;
        foreach($Monster as $k=>$info){
        	$moninfo['map'] = $k;
        	foreach($info['monster'] as $k1=>$v1){
        		$moninfo['cid'] = $k1;
        		$moninfo['totalNum'] = $v1['totalnum'];
        		$moninfo['pNum'] = $v1['num'];
        		$moninfo['win'] = $v1['win'];
        		$moninfo['type'] = 1;
        		$dal->insert('day_monster',$moninfo);
        	}
        }
     	$bossinfo = array();
        $bossinfo['date'] = $dt;
        foreach($boss as $k=>$info){
        	$moninfo['map'] = $k;
        	foreach($info['boss'] as $k1=>$v1){
        		$moninfo['cid'] = $k1;
        		$moninfo['totalNum'] = $v1['totalnum'];
        		$moninfo['pNum'] = $v1['num'];
        		$moninfo['win'] = $v1['win'];
        		$moninfo['type'] = 2;
        		$dal->insert('day_monster',$moninfo);
        	}
        }
        $materInfo = array();
        $materInfo['date'] = $dt;
        foreach($mater as $map=>$info){
        	$materInfo['map'] = $map;
        	foreach($info as $k=>$v){
        		$materInfo['type'] = $k;
        		foreach($v['Mater'] as $k1=>$v1){
        			$materInfo['cid'] = $k1;
        			$materInfo['totalNum'] = $v1['total'];
        			$materInfo['pNum'] = $v1['num'];
        			$dal->insert('day_mater',$materInfo);
        		}
        	}
        }
        return true;
    }

    private static function _getEnterCopy($dt, $dir)
    {
    	$data = array();
    	$num = 0;
    	$pnum = 0;
        $fileName = $dir .'/220/'. $dt . '/all-220-' . $dt . '.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_2x');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_2x');
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
                $userlevel = $aryLine[4];
                $map = $aryLine[5];
                 //去重 各副本进入人数
                if(!isset($data[$map]['enter']['list'][$uid])){
                	$data[$map]['enter']['list'][$uid] = 1;
                	$data[$map]['enter']['pnum'] += 1;
                }
                //经营等级分布
             	if(!isset($data[$map]['level'][$level]['list'][$uid])){
                	$data[$map]['level'][$level]['list'][$uid] = 1;
                	$data[$map]['level'][$level]['num'] += 1;
                }
                //主角等级分布
            	if(!isset($data[$map]['userlevel'][$level]['list'][$uid])){
                	$data[$map]['userlevel'][$level]['list'][$uid] = 1;
                	$data[$map]['userlevel'][$level]['num'] += 1;
                }
                $data[$map]['enter']['num'] += 1;
            }
        }
        catch (Exception $e) {
            info_log('_EnterCopy:'.$e->getMessage(), 'stat_EnterCopy');
            return false;
        }
        return $data;
    }

    private static function _getMonster($dt, $dir)
    {
      $data = array();
    	$num = 0;
    	$pnum = 0;
        $fileName = $dir .'/221/'.$dt . '/all-221-' . $dt . '.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_2x');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_2x');
                return false;
            }
            $lines = explode("\n", $content);
            foreach ($lines as $line) {
                if (empty($line) || $line == '-100') {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $uid = $aryLine[2];
                $map = $aryLine[3];
                $cid = $aryLine[4];
                $win = $aryLine[5];
                if(!isset($data[$map]['monster'][$cid]['list'][$uid])){
                	$data[$map]['monster'][$cid]['list'][$uid] = 1;
                	$data[$map]['monster'][$cid]['num']+= 1;
                }
                if($win == 1){
                	$data[$map]['monster'][$cid]['win']+= 1;
                	$data[$map]['win'] += 1;
                }
                $data[$map]['monster'][$cid]['totalnum']+= 1;
                if(!isset($data[$map]['total'][$uid])){
                	$data[$map]['total'][$uid] = 1;
                	$data[$map]['num'] += 1;
                }
                $data[$map]['totalnum'] += 1;
                //经营等级分布
            }
        }
        catch (Exception $e) {
            return false;
        }
        return $data;
    }
    
 	private static function _getBoss($dt, $dir)
    {
      $data = array();
    	$num = 0;
    	$pnum = 0;
        $fileName = $dir .'/222/'. $dt . '/all-222-' . $dt . '.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_2x');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_2x');
                return false;
            }
            $lines = explode("\n", $content);
            foreach ($lines as $line) {
                if (empty($line) || $line == '-100') {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $uid = $aryLine[2];
                $map = $aryLine[3];
                $cid = $aryLine[4];
                $win = $aryLine[5];
                if(!isset($data[$map]['boss'][$cid]['list'][$uid])){
                	$data[$map]['boss'][$cid]['list'][$uid] = 1;
                	$data[$map]['boss'][$cid]['num']+= 1;
                }
                
            	if($win == 1){
                	$data[$map]['boss'][$cid]['win']+= 1;
                	$data[$map]['win'] += 1;
                }
                $data[$map]['boss'][$cid]['totalnum']+= 1;
                if(!isset($data[$map]['total'][$uid])){
                	$data[$map]['total'][$uid] = 1;
                	$data[$map]['num'] += 1;
                }
                $data[$map]['totalnum'] += 1;
                //经营等级分布
            }
        }
        catch (Exception $e) {
            return false;
        }
        return $data;
    }
    
	private static function _getMater($dt, $dir)
    {
      $data = array();
    	$num = 0;
    	$pnum = 0;
        $fileName = $dir .'/223/'. $dt . '/all-223-' . $dt . '.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_2x');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_2x');
                return false;
            }
            $lines = explode("\n", $content);
            foreach ($lines as $line) {
                if (empty($line) || $line == '-100') {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $uid = $aryLine[2];
                $map = $aryLine[3];
                $cid = $aryLine[4];
                $num = $aryLine[5];
                $type = $aryLine[6];
                if(!isset($data[$map][$type]['Mater'][$cid]['list'][$uid])){
                	$data[$map][$type]['Mater'][$cid]['list'][$uid] = 1;
                	$data[$map][$type]['Mater'][$cid]['num']+= 1;
                }
                $data[$map][$type]['Mater'][$cid]['total'] += $num;
            	if(!isset($data[$map][$type]['list'][$uid])){
                	$data[$map][$type]['list'][$uid] = 1;
                	$data[$map][$type]['pnum']+= 1;
                }
                $data[$map][$type]['num']+= $num;
            }
        }
        catch (Exception $e) {
            return false;
        }
        return $data;
    }
    
    public static function getMain($day)
    {
    	$dal = Hapyfish2_Stat_Dal_Stat2x::getDefaultInstance();
    	$data = $dal->getMain($day);
    	return $data;
    }
    
    public static function getMonster($day)
    {
    	$dal = Hapyfish2_Stat_Dal_Stat2x::getDefaultInstance();
    	$data = $dal->getMonster($day);
    	return $data;
    }
    
    public static function getMater($day)
    {
    	$dal = Hapyfish2_Stat_Dal_Stat2x::getDefaultInstance();
    	$data = $dal->getMater($day);
    	return $data;
    	
    }
    
    public static function calcMutual($dt,$dir)
    {
    	$help = self::_getHelp($dt, $dir);
        $seize = self::_getSeize($dt, $dir);
        $resist = self::_getResist($dt, $dir);
        $gift = self::_getGift($dt, $dir);
        $data['date'] = $dt;
        $data['help'] = json_encode($help);
        $data['resist'] = json_encode($resist);
        $data['seize'] = json_encode($seize);
        $data['gift'] = json_encode($gift);
        $dal = Hapyfish2_Stat_Dal_Stat2x::getDefaultInstance();
        $dal->insert('day_mutual', $data);
    }
    
	private static function _getHelp($dt, $dir)
    {
      	$data = array();
    	$num = 0;
    	$pnum = 0;
        $fileName = $dir .'/225/'. $dt . '/all-225-' . $dt . '.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_2x');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_2x');
                return false;
            }
            $lines = explode("\n", $content);
            $params1[] = array('num', 2, array('group'=>0,'sum'=>0,'avg'=>0,'diff'=>1));
            $num = Hapyfish2_Stat_Bll_FormatData::formatData($lines, $params1);
            
            
        }   catch (Exception $e) {
            return false;
        }
        return array('total'=>$num['allCnt'], 'num'=>$num['num']['diff']);
    }
    
	private static function _getSeize($dt, $dir)
    {
      	$data = array();
    	$num = 0;
    	$pnum = 0;
        $fileName = $dir .'/226/'. $dt . '/all-226-' . $dt . '.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_2x');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_2x');
                return false;
            }
            $lines = explode("\n", $content);
            $params1[] = array('num', 2, array('group'=>0,'sum'=>0,'avg'=>0,'diff'=>1));
            $num = Hapyfish2_Stat_Bll_FormatData::formatData($lines, $params1);
            
            
        }   catch (Exception $e) {
            return false;
        }
        return array('total'=>$num['allCnt'], 'num'=>$num['num']['diff']);
    }
    
	private static function _getResist($dt, $dir)
    {
      	$data = array();
    	$num = 0;
    	$pnum = 0;
        $fileName = $dir .'/227/'. $dt . '/all-227-' . $dt . '.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_2x');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_2x');
                return false;
            }
            $lines = explode("\n", $content);
            $params1[] = array('num', 2, array('group'=>0,'sum'=>0,'avg'=>0,'diff'=>1));
            $num = Hapyfish2_Stat_Bll_FormatData::formatData($lines, $params1);
            
            
        }   catch (Exception $e) {
            return false;
        }
        return array('total'=>$num['allCnt'], 'num'=>$num['num']['diff']);
    }
    
	private static function _getGift($dt, $dir)
    {
      	$data = array();
    	$num = 0;
    	$pnum = 0;
        $fileName = $dir .'/228/'. $dt . '/all-228-' . $dt . '.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_2x');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_2x');
                return false;
            }
            $lines = explode("\n", $content);
            $params1[] = array('num', 2, array('group'=>0,'sum'=>0,'avg'=>0,'diff'=>1));
            $num = Hapyfish2_Stat_Bll_FormatData::formatData($lines, $params1);
            
            
        }   catch (Exception $e) {
            return false;
        }
        return array('total'=>$num['allCnt'], 'num'=>$num['num']['diff']);
    }
    
    public static function getMutual($day)
    {
    	$dal = Hapyfish2_Stat_Dal_Stat2x::getDefaultInstance();
    	$data = $dal->getMitial($day);
    	return $data;
    }
    
    public static function calcRepair($dt,$dir)
    {
		$data = array();
    	$num = 0;
    	$pnum = 0;
        $fileName = $dir .'/229/'. $dt . '/all-229-' . $dt . '.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_2x');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_2x');
                return false;
            }
            $lines = explode("\n", $content);
            $params1[] = array('num', 2, array('group'=>0,'sum'=>0,'avg'=>0,'diff'=>1));
            $params1[] = array('total', 2, array('group'=>0,'sum'=>1,'avg'=>0,'diff'=>0));
            $num = Hapyfish2_Stat_Bll_FormatData::formatData($lines, $params1);
        }   catch (Exception $e) {
            return false;
        }
        $data = array('date'=>$dt,'total'=>$num['allCnt'], 'num'=>$num['num']['diff'], 'cost'=>$num['total']['sum']);
        $dal = Hapyfish2_Stat_Dal_Stat2x::getDefaultInstance();
        $dal->insert('day_repair',$data);
    }
    	
    public static function getRepair($day)
    {
    	$dal = Hapyfish2_Stat_Dal_Stat2x::getDefaultInstance();
    	$data = $dal->getRepair($day);
    	return $data;
    }
}