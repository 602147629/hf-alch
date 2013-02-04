<?php

/**
 * Alchemy helltower controller
 *
 * @copyright  Copyright (c) 2012 HapyFish
 * @create      2012/12    mouse
 */
class HelltowerController extends Hapyfish2_Controller_Action_Api
{
	
    /**
     * 初始化水牢
     * 
     */
    public function initwaterAction()
    {
    	$uid = $this->uid;
        $status = Hapyfish2_Alchemy_Bll_Helltower::initWaterDungeon($uid);
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    /**
     * 初始化深渊
     * 
     */
    public function initabyssAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::initAbyssDungeon($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    /**
     * 重置水牢
     * 
     */
    public function resetwaterAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::resetWaterDungeon($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 重置深渊
     * 
     */
    public function resetabyssAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::resetAbyssDungeon($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    /**
     * 水牢闯关
     * 
     */
    public function rushwaterAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::rushWaterDungeon($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    /**
     * 深渊闯关
     * 
     */
    public function rushabyssAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::rushAbyssDungeon($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    /**
     * 深渊战斗
     * 
     */
    public function fightabyssAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::fightAbyssDungeon($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    /**
     * 水牢战斗
     * 
     */
    public function fightwaterAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::fightWaterDungeon($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    /**
     * 商店初始化
     * 
     */
    public function initshopAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::initShop($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    /**
     * 购买物品
     * 
     */
    public function buyitemAction()
    {
    	$uid = $this->uid;
		$cid = $this->_request->getParam('cid');
		$num = $this->_request->getParam('num');
        $status = Hapyfish2_Alchemy_Bll_Helltower::buyItemFromHellTowerShop($uid,$cid,$num);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    /**
     * 初始化水牢排行榜
     * 
     */
    public function initwaterrankAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::initWaterDungeonTop($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 初始化深渊排行榜
     * 
     */
    public function initabyssrankAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::initAbyssDungeonTop($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 扫荡水牢
     * 
     */
    public function sweepwaterAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::sweepWaterDungeon($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 扫荡深渊
     * 
     */
    public function sweepabyssAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::sweepAbyssDungeon($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    /**
     * 加速扫荡水牢
     * 
     */
    public function finishwatersweepAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::finishQQWaterSweep($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    /**
     * 加速扫荡深渊
     * 
     */
    public function finishabysssweepAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Helltower::finishAbyssSweep($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    //水牢复活
    public function revivalwaterAction()
    {
    	$uid = $this->uid;
        $status = Hapyfish2_Alchemy_Bll_Helltower::revivalQQWaterDungeon($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    //深渊复活
    public function revivalabyssAction()
    {
    	$uid = $this->uid;
        $status = Hapyfish2_Alchemy_Bll_Helltower::revivalQQAbyssDungeon($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    //深渊解锁
    public function unlockabyssAction()
    {
    	$uid = $this->uid;
        $status = Hapyfish2_Alchemy_Bll_Helltower::openAbyss($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
}