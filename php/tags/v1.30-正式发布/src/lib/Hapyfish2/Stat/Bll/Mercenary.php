<?php

class Hapyfish2_Stat_Bll_Mercenary
{
	/**
	 * 佣兵-雇佣
	 * @param varchar $dt,日期
	 * @param varchar $dir,路径
	 */
    public static function calHireMercenary($dt, $dir)
    {
    	///home/admin/logs/alchemy/stat-data/301/
    	//开发服-/home/admin/website/alchemy/renren/logs/
        //$strDate = date('Ymd', $dt);
        $strDate = $dt;
        
    	$dir = '/home/admin/stat/data/alchemy/kaixin/'.$prefix1.'/';
    
        $fileName301 = $dir.'301/' . $strDate . '/all-301-' . $strDate . '.log';
        $fileName302 = $dir.'302/' . $strDate . '/all-302-' . $strDate . '.log';
        $fileName303 = $dir.'303/' . $strDate . '/all-303-' . $strDate . '.log';
        $fileName304 = $dir.'304/' . $strDate . '/all-304-' . $strDate . '.log';
        $fileName305 = $dir.'305/' . $strDate . '/all-305-' . $strDate . '.log';
        try {
            //file not exists - 301，主要信息
            if (!file_exists($fileName301)) {
                info_log($fileName301 . ' not exists!', 'stat_301');
                return false;
            }
            $content301 = file_get_contents($fileName301);
            if (!$content301) {
                info_log($fileName301 . ' has no content!', 'stat_301');
                return false;
            }
            $lines301 = explode("\n", $content301);
            
            $params301 = array();
            $params301[] = array('userLev', 3, array('group'=>1,'sum'=>0,'avg'=>0,'diff'=>0));
            $params301[] = array('roleLev', 4, array('group'=>1,'sum'=>0,'avg'=>0,'diff'=>0));
            $params301[] = array('rp', 5, array('group'=>1,'sum'=>0,'avg'=>0,'diff'=>0));
            $params301[] = array('coin', 8, array('group'=>0,'sum'=>1,'avg'=>0,'diff'=>0));
            $data301 = Hapyfish2_Stat_Bll_FormatData::formatData($lines301, $params301);
            
            
            //file not exists - 302，刷新次数
            if (!file_exists($fileName302)) {
                info_log($fileName302 . ' not exists!', 'stat_301');
                return false;
            }
            $content302 = file_get_contents($fileName302);
            if (!$content302) {
                info_log($fileName302 . ' has no content!', 'stat_301');
                return false;
            }
            $lines302 = explode("\n", $content302);
            
            $params302 = array();
            $params302[] = array('uid', 2, array('group'=>0,'sum'=>0,'avg'=>0,'diff'=>0));
            $data302 = Hapyfish2_Stat_Bll_FormatData::formatData($lines302, $params302);
            
            
            //file not exists - 303，使用道具次数
            if (!file_exists($fileName303)) {
                info_log($fileName303 . ' not exists!', 'stat_301');
                return false;
            }
            $content303 = file_get_contents($fileName303);
            if (!$content303) {
                info_log($fileName303 . ' has no content!', 'stat_301');
                return false;
            }
            $lines303 = explode("\n", $content303);
            
            $params303 = array();
            $params303[] = array('uid', 2, array('group'=>0,'sum'=>0,'avg'=>0,'diff'=>0));
            $data303 = Hapyfish2_Stat_Bll_FormatData::formatData($lines303, $params303);
            
            
            //file not exists - 304，解雇次数
            if (!file_exists($fileName304)) {
                info_log($fileName304 . ' not exists!', 'stat_301');
                return false;
            }
            $content304 = file_get_contents($fileName304);
            if (!$content304) {
                info_log($fileName304 . ' has no content!', 'stat_301');
                return false;
            }
            $lines304 = explode("\n", $content304);
            
            $params304 = array();
            $params304[] = array('uid', 2, array('group'=>0,'sum'=>0,'avg'=>0,'diff'=>0));
            $data304 = Hapyfish2_Stat_Bll_FormatData::formatData($lines304, $params304);
            
            
            //file not exists - 305，佣兵培养
            if (!file_exists($fileName305)) {
                info_log($fileName305 . ' not exists!', 'stat_301');
                return false;
            }
            $content305 = file_get_contents($fileName305);
            if (!$content305) {
                info_log($fileName305 . ' has no content!', 'stat_301');
                return false;
            }
            $lines305 = explode("\n", $content305);
            
            $params305 = array();
            $params305[] = array('strthenCoin', 3, array('group'=>0,'sum'=>1,'avg'=>0,'diff'=>0));
            $params305[] = array('strthenGem', 4, array('group'=>0,'sum'=>1,'avg'=>0,'diff'=>0));
            $params305[] = array('strthenRoleLev', 5, array('group'=>1,'sum'=>0,'avg'=>0,'diff'=>0));
            $data305 = Hapyfish2_Stat_Bll_FormatData::formatData($lines305, $params305);
            
            
            //记录数据
            $info = array('log_time' => (int)$strDate,
            			  'all_count' => $data301['allCnt'],
            			  'rp_list' => json_encode($data301['rp']['group']),
            			  'user_level' => json_encode($data301['userLev']['group']),
            			  'role_level' => json_encode($data301['roleLev']['group']),
            			  'need_coin' => $data301['coin']['sum'],
            			  'refresh_count' => $data302['allCnt'],
            			  'useitem_count' => $data303['allCnt'],
            			  'dismiss_count' => $data304['allCnt'],
            			  'strthen_count' => $data305['allCnt'],
            			  'strthen_coin' => $data305['strthenCoin']['sum'],
            			  'strthen_gem' => $data305['strthenGem']['sum'],
            			  'strthen_role_level' => json_encode($data305['strthenRoleLev']['group']));

            $dal = Hapyfish2_Stat_Dal_Mercenary::getDefaultInstance();
            $row = $dal->getRow($strDate);
            if (!empty($row)) {
                $dal->delete($strDate);
            }
            $dal->insert($info);
        }
        catch (Exception $e) {
            info_log($e->getMessage(), 'stat_301');
            return false;
        }
        return true;
    }

	public static function getMercenaryMain($day)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Stat_Dal_Mercenary::getDefaultInstance();
			$data = $dal->getRow($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
	
}