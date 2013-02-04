<?php

/**
 * Alchemy mercenary controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/03    Nick
 */
class MercenaryController extends Hapyfish2_Controller_Action_Api
{
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
    	$type = $this->_request->getParam('type');
    	$fast = $this->_request->getParam('fast');
    	if(PLATFORM == 'pengyou' || PLATFORM == 'qzone' ){
    		if($fast == 1){
    			$status = Hapyfish2_Alchemy_Bll_Mercenary::refreshQQHire($uid, $type, $fast);
    		}else{
    			$status = Hapyfish2_Alchemy_Bll_Mercenary::refreshHire($uid, $type, $fast);
    		}
    	}else{
    		$status = Hapyfish2_Alchemy_Bll_Mercenary::refreshHire($uid, $type, $fast);
    	}
        
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
    	$id = $this->_request->getParam('id');
        
        $status = Hapyfish2_Alchemy_Bll_Mercenary::hireMercenary($uid, $id);
        
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
    	if(PLATFORM == 'pengyou' || PLATFORM == 'qzone' ){
    		if($idx >= 4){
    			$status = Hapyfish2_Alchemy_Bll_Mercenary::unlockQqSkill($uid, $id, $idx);
    		}else{
    			$status = Hapyfish2_Alchemy_Bll_Mercenary::unlockSkill($uid, $id, $idx);
    		}
    	}else{
    		$status = Hapyfish2_Alchemy_Bll_Mercenary::unlockSkill($uid, $id, $idx);
    	}
        
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
    	if(PLATFORM == 'pengyou' || PLATFORM == 'qzone' ){
    		if($payType > 1){
    			$status = Hapyfish2_Alchemy_Bll_Mercenary::expandQqRoleLimit($uid, $payType);
    		}else{
    			$status = Hapyfish2_Alchemy_Bll_Mercenary::expandRoleLimit($uid, $payType);
    		}
    	}else{
    		$status = Hapyfish2_Alchemy_Bll_Mercenary::expandRoleLimit($uid, $payType);
    	}
        
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
     * 重置主角各属性资质
     */
    public function resetrolequalityAction()
    {
    	$uid = $this->uid;
    	$locks = $this->_request->getParam('array');
        $locks = json_decode($locks, true);
        if(PLATFORM == 'pengyou' || PLATFORM == 'qzone' ){
    		$status = Hapyfish2_Alchemy_Bll_Mercenary::resetRoleQqQuality($uid, $locks);
        }else{
        	$status = Hapyfish2_Alchemy_Bll_Mercenary::resetRoleQuality($uid, $locks);
        }
    	if ($status < 0) {
    		$this->echoError($status);
    	}
    	$this->flush();
    }

    /**
     * 获取主角各属性资质信息
     */
    public function getrolequalityAction()
    {
    	$uid = $this->uid;
    	 
    	$status = Hapyfish2_Alchemy_Bll_Mercenary::getRoleQuality($uid);
    	 
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
    	if(PLATFORM == 'pengyou' || PLATFORM == 'qzone' ){
    		if($type > 1){
    			$status = Hapyfish2_Alchemy_Bll_Mercenary::strengQQthenStart($uid, $roleId, $type);
    		}else{
    			$status = Hapyfish2_Alchemy_Bll_Mercenary::strengthenStart($uid, $roleId, $type);
    		}
    	}else{
    		$status = Hapyfish2_Alchemy_Bll_Mercenary::strengthenStart($uid, $roleId, $type);
    	}
        
    	
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
    
    /**
     * 传授佣兵技能图鉴
     */
    public function studyskillAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	
        $status = Hapyfish2_Alchemy_Bll_MercenaryExt::studySkill($uid, $id);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }

    /**
     * 佣兵技能升级
     */
    public function upgradeskillAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$curCid = $this->_request->getParam('curCid');
    	$nextCid = $this->_request->getParam('nextCid');
    	
    	$status = Hapyfish2_Alchemy_Bll_MercenaryExt::upgradeSkill($uid, $id, $curCid, $nextCid);
    	
    	if ($status < 0) {
    		$this->echoError($status);
    	}
    	$this->flush();
    }
    /* public function upgradeskillAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$idx = $this->_request->getParam('idx');
    	
    	$status = Hapyfish2_Alchemy_Bll_MercenaryExt::upgradeSkill($uid, $id, $idx);
    	
    	if ($status < 0) {
    		$this->echoError($status);
    	}
    	$this->flush();
    } */
    
    
}