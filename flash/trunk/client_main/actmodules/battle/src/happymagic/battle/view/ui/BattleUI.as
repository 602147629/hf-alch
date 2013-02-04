package happymagic.battle.view.ui 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.utils.MouseActionCommand;
	import happymagic.battle.Battle;
	import happymagic.battle.events.BattleEvent;
	import happymagic.battle.model.BattleDataManager;
	import happymagic.battle.view.ui.BattleAutoOperateView;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.EffectDictionary;
	import happymagic.battle.model.Skills;
	import happymagic.battle.view.battlefield.Role;
	import happymagic.battle.view.FrameUI;
	import happymagic.model.vo.SkillAndItemVo;
	/**
	 * 战斗UI
	 * 战斗模块UI的容器 注册点在中心
	 * @author XiaJunJie
	 */
	public class BattleUI extends Sprite
	{
		public var operateBar:BattleOperateView; //操作条
		public var orderTimeView:OrderTimeView;
		public var friendSkillView:BattleFriendSkillView;
		public var userInfoView:BattleUserInfoView;
		public var skillList:BattleSkillListView;
		private var itemList:BattleItemListView;
		public var autoOperateBar:BattleAutoOperateView;
		
		public function BattleUI() 
		{
			//初始化操作条
			operateBar = new BattleOperateView(this);
			addChild(operateBar);
			
			autoOperateBar = new BattleAutoOperateView();
			addChild(autoOperateBar);
			
			//顺序条
			orderTimeView = new OrderTimeView();
			
			addChild(orderTimeView);
			
			//好友条
			friendSkillView = new BattleFriendSkillView();
			addChild(friendSkillView);
			
			//当前用户信息栏
			userInfoView = new BattleUserInfoView();
			addChild(userInfoView);
			
			//技能条
			skillList = new BattleSkillListView();
			addChild(skillList);
			skillList.visible = false;
			
			itemList = new BattleItemListView();
			addChild(itemList);
			itemList.visible = false;
			
			//生成外框
			var frame:Sprite = new FrameUI;
			addChild(frame);
			
			addEventListener(MouseEvent.CLICK, clickFun);
			
			EventManager.getInstance().addEventListener(BattleEvent.START_FIGHT, startFight);
			EventManager.getInstance().addEventListener(BattleEvent.CLOSE_BAG, itemList.hide);
		}
		
		private function startFight(e:BattleEvent):void 
		{
			orderTimeView.show();
		}
		
		public function hideOperateBar():void {
			operateBar.hide();
			itemList.hide(true);
			skillList.hide(true);
			friendSkillView.hide();
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			var index:uint;
			switch (e.target) 
			{
				case operateBar.atkBtn:
					EventManager.getInstance().dispatchEvent(new BattleEvent(BattleEvent.REQUEST_ATTACK));
				break;
				
				case operateBar.skillBtn:
					skillList.show();
					itemList.hide(true);
					new MouseActionCommand().outSideClickCommand(skillList, stage, closeList, [skillList]);
				break;
				
				case operateBar.itemBtn:
					itemList.show();
					skillList.hide(true);
					new MouseActionCommand().outSideClickCommand(itemList, stage, closeList, [itemList]);
				break;
				
				case operateBar.defenseBtn:
					EventManager.getInstance().dispatchEvent(new BattleEvent(BattleEvent.REQUEST_DEFENSE));
				break;
				
				case operateBar.escBtn:
					EventManager.getInstance().dispatchEvent(new BattleEvent(BattleEvent.REQUEST_ESC));
				break;
				
				case operateBar.autoBtn:
					EventManager.getInstance().dispatchEvent(new BattleEvent(BattleEvent.REQUEST_AUTO));
					autoOperateBar.show();
				break;
				
				case autoOperateBar:
					EventManager.getInstance().dispatchEvent(new BattleEvent(BattleEvent.REQUEST_CLOSE_AUTO));
					autoOperateBar.hide();
				break;
				
				case skillList.skill_0:
				case skillList.skill_1:
				case skillList.skill_2:
					skillList.hide(true);
					index = uint((e.target.name as String).split("_")[1]);
					var skill:SkillAndItemVo = skillList.skills[index];
					
					if (skill.needMp>userInfoView.curRole.mp) 
					{
						DisplayManager.showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, "mp不足");
						return;
					}
					
					var event:BattleEvent = new BattleEvent(BattleEvent.REQUEST_SKILL);
					event.skillAndItemVo = skill;
					EventManager.getInstance().dispatchEvent(event);
				break;
			}
		}
		
		private function closeList(targetList:*):void 
		{
			targetList["hide"](true);
		}
		
		/**
		 * 清除本对象
		 */
		public function clear():void
		{
			friendSkillView.clear();
			removeEventListener(MouseEvent.CLICK, clickFun);
		}
		
	}

}