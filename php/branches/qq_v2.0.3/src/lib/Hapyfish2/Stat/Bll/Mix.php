<?php

class Hapyfish2_Stat_Bll_Mix
{
	/**
	 * 合成物品信息
	 * @param varchar $dt,日期
	 * @param varchar $dir,路径
	 */
    public static function calMix($dt, $dir)
    {
    	///home/admin/logs/alchemy/stat-data/341/
    	//开发服-/home/admin/website/alchemy/renren/logs/
        //$strDate = date('Ymd', $dt);
        $strDate = $dt;
        $fileName341 = $dir . $strDate . '/all-341-' . $strDate . '.log';
        
        //fortest
        //$fileName341 = $dir . '/341-' . $strDate . '.log';
        try {
            //file not exists - 341，主要信息
            if (!file_exists($fileName341)) {
                info_log($fileName341 . ' not exists!', 'stat_341');
                return false;
            }
            $content341 = file_get_contents($fileName341);
            if (!$content341) {
                info_log($fileName341 . ' has no content!', 'stat_341');
                return false;
            }
            $lines341 = explode("\n", $content341);
            
            $groupAry = array();
            $allCnt = 0;
    		foreach ( $lines341 as $i ) {
                if (empty($i) || $i == '-100') {
    				continue;
    			}
    			$v = explode("\t", $i);
    			
    			$cid = $v[3];
    			$name = $v[4];
    			$num = $v[5];
    			$needCoin = $v[6];
    			$needGem = $v[7];
    			//分类
    			if ( isset($groupAry[$cid]) ) {
    				$groupAry[$cid]['count'] += $num;		 //合成物品个数
    				$groupAry[$cid]['needCoin'] += $needCoin;//花费金币
    				$groupAry[$cid]['needGem'] += $needGem;	 //花费宝石
    				$groupAry[$cid]['mixCount']++;			 //合成次数
    			}
    			else {
    				$groupAry[$cid]['count'] = $num;
    				$groupAry[$cid]['needCoin'] = $needCoin;
    				$groupAry[$cid]['needGem'] = $needGem;
    				$groupAry[$cid]['name'] = $name;
    				$groupAry[$cid]['mixCount'] = 1;
    			}
    			
    			//文件数据总行数
    			$allCnt++;
    		}
    		
            //记录数据
            $info = array('log_time' => (int)$strDate,
            			  'all_count' => $allCnt,
            			  'mix_data' => json_encode($groupAry));

            $dal = Hapyfish2_Stat_Dal_Mix::getDefaultInstance();
            $row = $dal->getRow($strDate);
            if (!empty($row)) {
                $dal->delete($strDate);
            }
            $dal->insert($info);
        }
        catch (Exception $e) {
            info_log($e->getMessage(), 'stat_341');
            return false;
        }
        return true;
    }

	public static function getMixMain($day)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Stat_Dal_Mix::getDefaultInstance();
			$data = $dal->getRow($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
	
}