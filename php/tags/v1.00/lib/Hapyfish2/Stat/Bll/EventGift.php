<?php

class Hapyfish2_Stat_Bll_EventGift
{

    public static function calcGift($dt, $dir)
    {
    	$data = array();
        $fileName1 = $dir .'/'. $dt . '/all-401-' . $dt . '.log';
        $fileName2 = $dir .'/'. $dt . '/all-402-' . $dt . '.log';
        $fileName3 = $dir .'/'. $dt . '/all-403-' . $dt . '.log';
        try {
        	$content1 = file_get_contents($fileName1);
        	$content2 = file_get_contents($fileName2);
        	$content3 = file_get_contents($fileName3);
            $lines1 = explode("\n", $content1);
            $lines2 = explode("\n", $content2);
            $lines3 = explode("\n", $content3);
            $params1[] = array('giftId', 3, array('group'=>1,'sum'=>0,'avg'=>0,'diff'=>0));
            $timeGift = Hapyfish2_Stat_Bll_FormatData::formatData($lines1, $params1);
            $params2[] = array('giftId', 3, array('group'=>1,'sum'=>0,'avg'=>0,'diff'=>0));
            $sevenGift = Hapyfish2_Stat_Bll_FormatData::formatData($lines2, $params2);
            $params3[] = array('giftId', 3, array('group'=>1,'sum'=>0,'avg'=>0,'diff'=>0));
            $levelGift = Hapyfish2_Stat_Bll_FormatData::formatData($lines3, $params3);
        }   catch (Exception $e) {
            return false;
        }
        $data = array('date'=>$dt,'timeGift'=>json_encode($timeGift['giftId']['group']), 'sevenGift'=>json_encode($sevenGift['giftId']['group']), 'levelGift'=>json_encode($levelGift['giftId']['group']));
        $dal = Hapyfish2_Stat_Dal_EventGift::getDefaultInstance();
        $dal->insert('day_eventGift',$data);
    }
}