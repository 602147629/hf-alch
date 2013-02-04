<?php

class Hapyfish2_Stat_Bll_Shop
{
	/**
	 * 商店物品购买
	 * @param varchar $dt,日期
	 * @param varchar $dir,路径
	 */
    public static function calShop($dt, $dir)
    {
    	///home/admin/logs/alchemy/stat-data/331/
    	//开发服-/home/admin/website/alchemy/renren/logs/
        //$strDate = date('Ymd', $dt);
        $strDate = $dt;
        $fileName331 = $dir . $strDate . '/all-331-' . $strDate . '.log';
        
        //fortest
        $fileName331 = $dir . '/331-' . $strDate . '.log';
        try {
            //file not exists - 331，主要信息
            if (!file_exists($fileName331)) {
                info_log($fileName331 . ' not exists!', 'stat_331');
                return false;
            }
            $content331 = file_get_contents($fileName331);
            if (!$content331) {
                info_log($fileName331 . ' has no content!', 'stat_331');
                return false;
            }
            $lines331 = explode("\n", $content331);
            
            $groupAry = array();
            $allCnt = 0;
    		foreach ( $lines331 as $i ) {
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
    				$groupAry[$cid]['count'] += $num;
    				$groupAry[$cid]['needCoin'] += $needCoin;
    				$groupAry[$cid]['needGem'] += $needGem;
    			}
    			else {
    				$groupAry[$cid]['count'] = $num;
    				$groupAry[$cid]['needCoin'] = $needCoin;
    				$groupAry[$cid]['needGem'] = $needGem;
    				$groupAry[$cid]['name'] = $name;
    			}
    			
    			//文件数据总行数
    			$allCnt++;
    		} 
    		
            //记录数据
            $info = array('log_time' => (int)$strDate,
            			  'all_count' => $allCnt,
            			  'buy_data' => json_encode($groupAry));

            $dal = Hapyfish2_Stat_Dal_Shop::getDefaultInstance();
            $row = $dal->getRow($strDate);
            if (!empty($row)) {
                $dal->delete($strDate);
            }
            $dal->insert($info);
        }
        catch (Exception $e) {
            info_log($e->getMessage(), 'stat_331');
            return false;
        }
        return true;
    }

	public static function getShopMain($day)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Stat_Dal_Shop::getDefaultInstance();
			$data = $dal->getRow($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
	
}