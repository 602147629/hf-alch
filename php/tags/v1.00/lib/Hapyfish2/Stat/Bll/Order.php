<?php

class Hapyfish2_Stat_Bll_Order
{
	/**
	 * 经营-订单
	 * @param varchar $dt,日期
	 * @param varchar $dir,路径
	 */
    public static function calOrder($dt, $dir)
    {
    	///home/admin/logs/alchemy/stat-data/311/
    	//开发服-/home/admin/website/alchemy/renren/logs/
        //$strDate = date('Ymd', $dt);
        $strDate = $dt;
        $fileName311 = $dir . $strDate . '/all-311-' . $strDate . '.log';
        $fileName312 = $dir . $strDate . '/all-312-' . $strDate . '.log';
        $fileName313 = $dir . $strDate . '/all-313-' . $strDate . '.log';
        $fileName314 = $dir . $strDate . '/all-314-' . $strDate . '.log';
        
        //fortest
        $fileName311 = $dir . '/311-' . $strDate . '.log';
        $fileName312 = $dir . '/312-' . $strDate . '.log';
        $fileName313 = $dir . '/313-' . $strDate . '.log';
        $fileName314 = $dir . '/314-' . $strDate . '.log';
        try {
            //file not exists - 311，主要信息
            if (!file_exists($fileName311)) {
                info_log($fileName311 . ' not exists!', 'stat_311');
                return false;
            }
            $content311 = file_get_contents($fileName311);
            if (!$content311) {
                info_log($fileName311 . ' has no content!', 'stat_311');
                return false;
            }
            $lines311 = explode("\n", $content311);
            
            $params311 = array();
            $params311[] = array('uid', 2, array('group'=>0,'sum'=>0,'avg'=>0,'diff'=>0));
            $data311 = Hapyfish2_Stat_Bll_FormatData::formatData($lines311, $params311);
            
            
            //file not exists - 312，刷新次数
            if (!file_exists($fileName312)) {
                info_log($fileName312 . ' not exists!', 'stat_311');
                return false;
            }
            $content312 = file_get_contents($fileName312);
            if (!$content312) {
                info_log($fileName312 . ' has no content!', 'stat_311');
                return false;
            }
            $lines312 = explode("\n", $content312);
            
            $params312 = array();
            $params312[] = array('addCoin', 2, array('group'=>0,'sum'=>1,'avg'=>0,'diff'=>0));
            $data312 = Hapyfish2_Stat_Bll_FormatData::formatData($lines312, $params312);
            
            
            //file not exists - 313，使用道具次数
            if (!file_exists($fileName313)) {
                info_log($fileName313 . ' not exists!', 'stat_311');
                return false;
            }
            $content313 = file_get_contents($fileName313);
            if (!$content313) {
                info_log($fileName313 . ' has no content!', 'stat_311');
                return false;
            }
            $lines313 = explode("\n", $content313);
            
            $params313 = array();
            $params313[] = array('uid', 2, array('group'=>0,'sum'=>0,'avg'=>0,'diff'=>0));
            $data313 = Hapyfish2_Stat_Bll_FormatData::formatData($lines313, $params313);
            
            
            //file not exists - 314，解雇次数
            if (!file_exists($fileName314)) {
                info_log($fileName314 . ' not exists!', 'stat_311');
                return false;
            }
            $content314 = file_get_contents($fileName314);
            if (!$content314) {
                info_log($fileName314 . ' has no content!', 'stat_311');
                return false;
            }
            $lines314 = explode("\n", $content314);
            
            $params314 = array();
            $params314[] = array('uid', 2, array('group'=>0,'sum'=>0,'avg'=>0,'diff'=>0));
            $data314 = Hapyfish2_Stat_Bll_FormatData::formatData($lines314, $params314);
            
            
            //记录数据
            $info = array('log_time' => (int)$strDate,
            			  'accept_count' => $data311['allCnt'],
            			  'complete_count' => $data312['allCnt'],
            			  'add_coin' => $data312['addCoin']['sum'],
            			  'fail_count' => $data313['allCnt'],
            			  'refresh_count' => $data314['allCnt']);

            $dal = Hapyfish2_Stat_Dal_Order::getDefaultInstance();
            $row = $dal->getRow($strDate);
            if (!empty($row)) {
                $dal->delete($strDate);
            }
            $dal->insert($info);
        }
        catch (Exception $e) {
            info_log($e->getMessage(), 'stat_311');
            return false;
        }
        return true;
    }

	public static function getOrderMain($day)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Stat_Dal_Order::getDefaultInstance();
			$data = $dal->getRow($day); 
		} catch (Exception $e) {

		}
		
		return $data;
	}
	
}