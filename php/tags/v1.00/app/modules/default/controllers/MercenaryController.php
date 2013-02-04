<?php

/**
 * Alchemy mercenary controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/03    Nick
 */
class MercenaryController extends Hapyfish2_Controller_Action_Api
{    
	public function getnewhireAction()
	{
        $uid = $this->uid;
        
		/*$hireList = Hapyfish2_Alchemy_HFC_Hire::getAll($uid);
		$hireInfo = $hireList[1];*/
		$hireInfo['level'] = 1;
		$newHire = Hapyfish2_Alchemy_Bll_Mercenary::getNewHire($uid, $id, $hireInfo);
        
        $this->echoResult($newHire);
	}
	
	public function getallhireAction()
	{
        $uid = $this->uid;
		$hire = Hapyfish2_Alchemy_HFC_Hire::getAll($uid);
        $this->echoResult($hire);
	}
	
	public function clearskillAction()
	{
        $uid = $this->uid;
    	$id = $this->_request->getParam('pos');
        
		$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		$userMercenary['weapon'] = array(0,0,0,0);
		$userMercenary['skill'] = array(0,-1,-1,-1);
		
		//更新技能信息
		$status = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $userMercenary);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
	}
	
	public function loadhireAction()
	{
        $uid = $this->uid;
		$hire = Hapyfish2_Alchemy_HFC_Hire::loadAll($uid);
        $this->echoResult($hire);
	}
	
	public function updateweaponAction()
	{
    	$uid = $this->_request->getParam('uid');
    	$wid = $this->_request->getParam('wid');
    	$status = $this->_request->getParam('status');
		//新装备信息
		$newWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
		$newWeapon['status'] = $status;
		Hapyfish2_Alchemy_HFC_Weapon::updateOne($uid, $newWeapon['wid'], $newWeapon);
		echo 'ok';
		exit;
	}
	
    /**
     * 初始化雇佣位
     */
    public function gethireAction()
    {
        $uid = $this->uid;
        
        $result = Hapyfish2_Alchemy_Bll_Mercenary::getHire($uid);
        
        $this->echoResult($result);
    }
    
    /**
     * 刷新佣兵（放弃）
     */
    public function refreshhireAction()
    {
        $uid = $this->uid;
    	$id = $this->_request->getParam('pos');
    	$npcId = $this->_request->getParam('npcId');
        
        $status = Hapyfish2_Alchemy_Bll_Mercenary::refreshHire($uid, $id, $npcId);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }

    /**
     * 立即完成雇佣时间
     */
    public function completehireAction()
    {
        $uid = $this->uid;
    	$id = $this->_request->getParam('pos');
    	$npcId = $this->_request->getParam('npcId');
    	$type = $this->_request->getParam('type');
        
        $status = Hapyfish2_Alchemy_Bll_Mercenary::completeHire($uid, $id, $npcId, $type);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 雇佣酒馆中佣兵
     */
    public function hireroleAction()
    {
        $uid = $this->uid;
    	$id = $this->_request->getParam('pos');
    	$npcId = $this->_request->getParam('npcId');
        
        $status = Hapyfish2_Alchemy_Bll_Mercenary::hireMercenary($uid, $id, $npcId);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 解雇佣兵
     */
    public function dismissroleAction()
    {
        $uid = $this->uid;
    	$id = $this->_request->getParam('id');
        
        $status = Hapyfish2_Alchemy_Bll_Mercenary::dismissMercenary($uid, $id);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /*
     * 更换装备
     */
    public function replaceequipAction()
    {
        $uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$wid = $this->_request->getParam('equipId');
        
        $status = Hapyfish2_Alchemy_Bll_Mercenary::replaceEquip($uid, $id, $wid);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 一键卸下所有装备
     */
    public function stripallequipAction()
    {
        $uid = $this->uid;
    	$id = $this->_request->getParam('id');
        
        $status = Hapyfish2_Alchemy_Bll_Mercenary::stripAllEquip($uid, $id);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 更换技能
     */
    public function replaceskillAction()
    {
        $uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$idx = $this->_request->getParam('idx');
    	$cid = $this->_request->getParam('cid');
        
        $status = Hapyfish2_Alchemy_Bll_Mercenary::replaceSkill($uid, $id, $idx, $cid);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 解锁技能位
     */
    public function unlockskillAction()
    {
        $uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$idx = $this->_request->getParam('idx');
        
        $status = Hapyfish2_Alchemy_Bll_Mercenary::unlockSkill($uid, $id, $idx);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 扩大佣兵上限数
     */
    public function expandrolelimitAction()
    {
        $uid = $this->uid;
    	$payType = $this->_request->getParam('type', 1);
        
        $status = Hapyfish2_Alchemy_Bll_Mercenary::expandRoleLimit($uid, $payType);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 更换阵型
     */
    public function updatematrixAction()
    {
        $uid = $this->uid;
    	$map = $this->_request->getParam('map');
    	$map = json_decode($map);
        $status = Hapyfish2_Alchemy_Bll_Mercenary::updateMatrix($uid, $map);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 回家补满佣兵血量
     */
    public function resumehpAction()
    {
    	$uid = $this->uid;
    	
        $status = Hapyfish2_Alchemy_Bll_Mercenary::resumeHp($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 主角晋级
     */
    public function roleupgradeAction()
    {
    	$uid = $this->uid;
    	
        $status = Hapyfish2_Alchemy_Bll_Mercenary::roleUpgrade($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 开始强化
     */
    public function strthenstartAction()
    {
    	$uid = $this->uid;
    	$roleId = $this->_request->getParam('roleId');
    	$type = $this->_request->getParam('type');
    	
        $status = Hapyfish2_Alchemy_Bll_Mercenary::strengthenStart($uid, $roleId, $type);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 保存强化
     */
    public function strthencompleteAction()
    {
    	$uid = $this->uid;
    	$roleId = $this->_request->getParam('roleId');
    	
        $status = Hapyfish2_Alchemy_Bll_Mercenary::strengthenComplete($uid, $roleId);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 佣兵派驻打工 静态信息
     */
    public function initmercenaryworkAction()
    {
		header("Cache-Control: max-age=2592000");
    	$result = Hapyfish2_Alchemy_Bll_BasicInfo::getInitMercenaryWork();

		$this->echoResult($result);
    }
    
    /**
     * 佣兵派驻打工 获取动态信息
     */
    public function getmercenaryworkAction()
    {
    	$uid = $this->uid;
    	
        $status = Hapyfish2_Alchemy_Bll_MercenaryWork::getWork($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 佣兵派驻打工 开始打工
     */
    public function startworkAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$roleIds = $this->_request->getParam('roleIds');
    	
        $status = Hapyfish2_Alchemy_Bll_MercenaryWork::startWork($uid, $id, $roleIds);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 佣兵派驻打工 立即完成打工
     */
    public function completeworkAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	
        $status = Hapyfish2_Alchemy_Bll_MercenaryWork::completeWork($uid, $id);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 佣兵派驻打工 领取奖励
     */
    public function getworkawardAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	
        $status = Hapyfish2_Alchemy_Bll_MercenaryWork::getAward($uid, $id);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 佣兵派驻打工 取消打工
     */
    public function cancelworkAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	
        $status = Hapyfish2_Alchemy_Bll_MercenaryWork::cancelWork($uid, $id);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    
}