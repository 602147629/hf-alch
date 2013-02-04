<?php

class Hapyfish2_Stat_Bll_Item
{
	/**
	 * 道具使用
	 * @param varchar $dt,日期
	 * @param varchar $dir,路径
	 */
    public static function calItem($dt, $dir)
    {
    	///home/admin/logs/alchemy/stat-data/321/
    	//开发服-/home/admin/website/alchemy/renren/logs/
        //$strDate = date('Ymd', $dt);
        $strDate = $dt;
        $fileName321 = $dir . $strDate . '/all-321-' . $strDate . '.log';
        
        //fortest
        $fileName321 = $dir . '/321-' . $strDate . '.log';
        try {
            //file not exists - 321，主要信息
            if (!file_exists($fileName321)) {
                info_log($fileName321 . ' not exists!', 'stat_321');
                return false;
            }
            $content321 = file_get_contents($fileName321);
            if (!$content321) {
                info_log($fileName321 . ' has no content!', 'stat_321');
                return false;
            }
            $lines321 = explode("\n", $content321);
            $groupAry = array();
            $allCnt = 0;
    		foreach ( $lines321 as $i ) {
                if (empty($i) || $i == '-100') {
    				continue;
    			}
    			$v = explode("\t", $i);
    			
    			//分类
    			if ( isset($groupAry[$v[3]]) ) {
    				$groupAry[$v[3]]['count']++;
    			}
    			else {
    				$groupAry[$v[3]]['count'] = 1;
    				$groupAry[$v[3]]['name'] = $v[4];
    			}
    			
    			//文件数据总行数
    			$allCnt++;
    		} 
    		
            //记录数据
            $info = array('log_time' => (int)$strDate,
            			  'all_count' => $allCnt,
            			  'use_data' => json_encode($groupAry));

            $dal = Hapyfish2_Stat_Dal_Item::getDefaultInstance();
            $row = $dal->getRow($strDate);
            if (!empty($row)) {
                $dal->delete($strDate);
            }
            $dal->insert($info);
        }
        catch (Exception $e) {
            info_log($e->getMessage(), 'stat_321');
            return false;
        }
        return true;
    }

	public static function getItemMain($day)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Stat_Dal_Item::getDefaultInstance();
			$data = $dal->getRow($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
	
}