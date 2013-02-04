<?php

class Hapyfish2_Stat_Bll_Statmainhour
{
	/**
	 * 小时数据
	 * @param varchar $dt,日期,2012071100-2012071123
	 * @param varchar $dir,路径
	 */
    public static function calStatMainhour($dt, $dir, $statTime)
    {
    	///home/admin/logs/alchemy/stat-data/100/
    	//开发服-/home/admin/website/alchemy/renren/logs/
        //$strDate = date('Ymd', $dt);
        $strDate = $dt;
        $fileName100 = $dir . '361/' . $strDate . '/all-361-' . $strDate . '.log';

        //$statTime = strtotime($statTime);
        $timeNextHour = $statTime + 60*60;
        
        try {
            //file not exists - 361，主要信息
            if (!file_exists($fileName100)) {
                info_log($fileName100 . ' not exists!', 'stat_361');
                return false;
            }
            $content100 = file_get_contents($fileName100);
            if (!$content100) {
                info_log($fileName100 . ' has no content!', 'stat_361');
                return false;
            }
            $lines100 = explode("\n", $content100);
            
            $instalCnt = 0;
    		foreach ( $lines100 as $i ) {
                if (empty($i) || $i == '-100') {
    				continue;
    			}
    			$v = explode("\t", $i);
    			
    			if ( $v[0] < $timeNextHour && $v[0] >= $statTime ) {
	    			//一小时内数据行数
	    			$instalCnt++;
    			}
    		}
    		
    		$logTime = (int)date('YmdH', $statTime);
            //记录数据
            $info = array('log_time' => $logTime,
            			  'install_user' => $instalCnt);

            $dal = Hapyfish2_Stat_Dal_MainHour::getDefaultInstance();
            $row = $dal->getRow($logTime);
            if (!empty($row)) {
                $dal->deleteRow($logTime);
            }
            $dal->insertStat($info);
        }
        catch (Exception $e) {
            info_log($e->getMessage(), 'stat_361');
            return false;
        }
        return true;
    }

	public static function getStatMainHour($time)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Stat_Dal_MainHour::getDefaultInstance();
			$data = $dal->getRow($time); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
	
}