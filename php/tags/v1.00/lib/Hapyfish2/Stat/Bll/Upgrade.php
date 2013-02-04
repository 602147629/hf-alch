<?php

class Hapyfish2_Stat_Bll_Upgrade
{
	/**
	 * 佣兵-雇佣
	 * @param varchar $dt,日期
	 * @param varchar $dir,路径
	 */
    public static function calUpgrade($dt, $dir='/home/admin/logs/alchemy/renren/')
    {
    	$data = null;
        $home = self::_getStat($dt, $dir, 351);
        $tavern1 = self::_getStat($dt, $dir, 353);
        $tavern2 = self::_getStat($dt, $dir, 354);
        $tavern3 = self::_getStat($dt, $dir, 355);
        $smithy = self::_getStat($dt, $dir, 352);
        $data['date'] = $dt;
        $data['home'] = json_encode($home);
        $data['tavern1'] = json_encode($tavern1);
        $data['tavern2'] = json_encode($tavern2);
        $data['tavern3'] = json_encode($tavern3);
        $data['smithy'] = json_encode($smithy);
        $dal = Hapyfish2_Stat_Dal_Upgrade::getDefaultInstance();
        $dal->insert('day_upgrade', $data);
        
      
    }

	private static function _getStat($dt, $dir, $file)
	{
		
    	$data = array();
        $fileName = $dir . $dt . '/all-'.$file.'-' . $dt . '.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_DaycLoadTm');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_DaycLoadTm');
                return false;
            }
            $lines = explode("\n", $content);
            $params351 = array();
            $params351[] = array('userLevel', 3, array('group'=>1,'sum'=>0,'avg'=>0,'diff'=>0));
            $params351[] = array('coin', 4, array('group'=>0,'sum'=>1,'avg'=>0,'diff'=>0));
            $params351[] = array('p', 2, array('group'=>0,'sum'=>0,'avg'=>0,'diff'=>1));
            $data351 = Hapyfish2_Stat_Bll_FormatData::formatData($lines, $params351);
            $data['list'] = $data351['userLevel']['group'];
            $data['sum'] = $data351['coin']['sum'];
            $data['total'] = $data351['allCnt'];
            $data['pnum'] = $data351['p']['diff'];
        }
        catch (Exception $e) {
            info_log('_EnterCopy:'.$e->getMessage(), 'stat_EnterCopy');
            return false;
        }
        return $data;
	}
	
	public static function getUpgrade($day)
	{
		$dal = Hapyfish2_Stat_Dal_Upgrade::getDefaultInstance();
    	$data = $dal->getUpgrade($day);
    	return $data;
	}
	
	
	
}