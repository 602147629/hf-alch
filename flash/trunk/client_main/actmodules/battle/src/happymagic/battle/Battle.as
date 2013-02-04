package happymagic.battle 
{
	import com.brokenfunction.json.encodeJson;
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.ModuleEvent;
	import happyfish.events.SwfClassCacheEvent;
	import happyfish.manager.BgMusicManager;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.mouse.MouseManager;
	import happymagic.battle.controller.AnimationClip;
	import happymagic.battle.controller.BattleAIManager;
	import happymagic.battle.controller.BattleAnimationManager;
	import happymagic.battle.events.BattleEvent;
	import happymagic.battle.model.BattleDataManager;
	import happymagic.battle.model.command.BattleEndCommand;
	import happymagic.battle.model.Skills;
	import happymagic.battle.model.vo.BattleRecordTargetVo;
	import happymagic.battle.model.vo.BattleVo;
	import happymagic.battle.model.vo.BattleRecordVo;
	import happymagic.battle.model.vo.FriendSkillVo;
	import happymagic.battle.view.battlefield.BattleField;
	import happymagic.battle.view.battlefield.Role;
	import happymagic.battle.view.battlefield.TalkView;
	import happymagic.battle.view.ui.BattleUI;
	import happymagic.battle.view.ui.BattleWinView;
	import happymagic.battle.view.ui.OperateBtnDict;
	import happymagic.manager.DataManager;
	import happymagic.battle.view.battlefield.Grid;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.mouse.MagicMouseIconType;
	import happymagic.model.vo.ai.AIActionVo;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.SkillAndItemVo;
	
	/**
	 * 战斗主类
	 * @author XiaJunJie
	 */
	public class Battle extends MovieClip
	{	
		//数据
		private var vo:BattleVo; //战斗数据
		
		private var actionQueue:Vector.<RoleVo>; //行动队列
		
		private var auto:Boolean; //是否自动战斗
		private var isBossBattle:Boolean; //是否BOSS战
		
		//辅助数据
		private var leftRoleNum:int; //剩余的尚未加载完毕的角色数量
		private var curRoleVo:RoleVo; //当前行动的角色的VO
		private var operateType:String; //目前玩家正在进行何种操作
		private var curSkillAndItemVo:SkillAndItemVo; //目前玩家正在操作的技能或物品
		private var curItemId:int; //当前正在使用的物品的ID
		private var curSelectedGrids:Array; //当前变成红色的地块 主要是为了方便鼠标移出后快速还原
		private var targetGrid:int; //当前的目标地块
		private var targets:Vector.<RoleVo>; //当前选择到的目标
		private var keyGrid:int; //当前技能的关键点
		private var canAction:Boolean; //当前角色经过状态的洗礼后是否还能继续行动(有没被石化)
		private var canUseSkill:Boolean; //当前角色经过状态的洗礼后是否还能使用技能(有没被沉默)
		private var curStatusRecord:BattleRecordVo; //当前角色在行动前进行的状态处理的战斗记录 之所以要保存此记录是为了角色结束行动时消除持续时间为零的状态
		private var curFriend:Role; //当前使用的技能如果是好友援助技能 此属性记录要施放此技能的好友
		
		private var exp:Array;
		private var awards:Object;
		
		private var needMats:Array; //战斗开始前要加载的素材
		private var hightLightRolePos:int = -1; //当前高亮的角色的位置
		
		//显示对象
		private var battleField:BattleField; //战场
		private var dropItemLayer:Sprite; //掉落物品层 由于必须有鼠标事件 因此独立于战场外
		private var ui:BattleUI; //UI
		
		//组件
		private var battleDataManager:BattleDataManager; //数据管理器
		private var battleAnimationManager:BattleAnimationManager; //动画管理器
		private var battleAIManager:BattleAIManager; //AI管理器
		
		//纯辅助
		private var isTest:Boolean; //是否是测试用的战斗 影响到最后战斗结束调用的后端接口 测试用战斗有专门的战斗结束接口
		private var testUid:int; //测试战斗可以不是当前玩家的战斗 因此需要记录战斗所属的用户的UID
		
		//静态
		public static var autoTimes:int = 0;
		
		/**
		 * 构造
		 */
		public function Battle() 
		{
			//初始化战场
			battleField = new BattleField;
			addChild(battleField);
			battleField.x = -BG_WIDTH / 2;
			battleField.y = -BG_HEIGHT / 2;
			battleField.addEventListener(MouseEvent.CLICK, onBattleFieldClick);
			battleField.addEventListener(MouseEvent.ROLL_OVER, onBattleFieldRollOver);
			battleField.addEventListener(MouseEvent.ROLL_OUT, onBattleFieldRollOut);
			
			//初始化物品掉落层
			dropItemLayer = new Sprite;
			addChild(dropItemLayer);
			dropItemLayer.x = battleField.x;
			dropItemLayer.y = battleField.y;
			var dropItemLayerMask:Shape = new Shape;
			dropItemLayerMask.graphics.beginFill(0);
			dropItemLayerMask.graphics.drawRect(0, 0, BG_WIDTH, BG_HEIGHT);
			dropItemLayerMask.graphics.endFill();
			dropItemLayer.addChild(dropItemLayerMask);
			dropItemLayer.mask = dropItemLayerMask;
			
			//初始化UI
			ui = new BattleUI;
			addChild(ui);
			EventManager.getInstance().addEventListener(BattleEvent.REQUEST_ATTACK, startAttackOperation);
			EventManager.getInstance().addEventListener(BattleEvent.REQUEST_SKILL, startSkillOrItemOperation);
			EventManager.getInstance().addEventListener(BattleEvent.REQUEST_DEFENSE, startDefenseOperation);
			EventManager.getInstance().addEventListener(BattleEvent.REQUEST_ESC, startEscapeOperation);
			EventManager.getInstance().addEventListener(BattleEvent.REQUEST_AUTO, startAutoOperation);
			EventManager.getInstance().addEventListener(BattleEvent.LIGHT_ROLE, lightRole);
			EventManager.getInstance().addEventListener(BattleEvent.CLOSE_LIGHT_ROLE, closeLightRole);
			EventManager.getInstance().addEventListener(BattleEvent.REQUEST_CLOSE_AUTO, cancelAuto);
			
			//侦听输赢面板关闭事件
			ModuleManager.getInstance().addEventListener(ModuleEvent.MODULE_CLOSE, onModuleClose);
			
			init();
		}
		
		//初始化战斗
		private function init():void
		{
			//获取数据
			var data:Object = DataManager.getInstance().getVar("battleInitData");
			vo = new BattleVo().setData(data["BattleVo"]) as BattleVo;
			
			actionQueue = vo.roleList.concat();
			ui.orderTimeView.nextRoleCallBack = onNextRoleUiComplete;
			
			var randomList:Array = new Array;
			var arr:Array = data["RndNums"];
			for (var i:int = 0; i < arr.length; i++) randomList.push(arr[i] / 1000);
			
			isTest = data.isTest;
			testUid = data.testUid;
			
			//初始化组件
			battleDataManager = new BattleDataManager(randomList, vo, actionQueue);
			battleAnimationManager = new BattleAnimationManager(battleField, dropItemLayer, this);
			battleAIManager = new BattleAIManager(battleField, actionQueue);
			
			//加载背景
			battleField.loadBackground(vo.bgClassName); 
			
			needMats = new Array;
			
			//加载角色
			leftRoleNum = vo.roleList.length;
			for each(var roleVo:RoleVo in vo.roleList)
			{
				if (roleVo.label == RoleVo.BOSS) isBossBattle = true;
				
				var role:Role = new Role(roleVo);
				battleField.addRole(role, true);
				if (role.view) onRoleLoadComplete();
				else role.addEventListener(Event.COMPLETE, onRoleLoadComplete);
				
				for each(var skillId:int in roleVo.skills) //收集需要加载的技能素材
				{
					if (skillId == 0 || skillId == -1) continue;
					var skillAndItemAndVo:SkillAndItemVo = DataManager.getInstance().getSkillAndItemVo(skillId);
					for(var mat:String in skillAndItemAndVo.needMats)
					{
						if (needMats.indexOf(mat) == -1) needMats.push(mat);
					}
				}
			}
			
			if (vo.friendSkill)
			{
				ui.friendSkillView.setData(vo.friendSkill, vo.assCnt, vo.extCnt);
				
				//加载好友
				for (i = 0; i < vo.friendSkill.length; i++)
				{
					role = vo.friendSkill[i].role;
					if (role)
					{
						battleField.addRole(role, true);
						role.x = BattleField.FRIEND_POS_X;
						role.y = BattleField.FRIEND_POS_Y;
					}
					for(mat in vo.friendSkill[i].skill.needMats) //收集需要加载的技能素材
					{
						if (needMats.indexOf(mat) == -1) needMats.push(mat);
					}
				}
			}
			else ui.friendSkillView.visible = false;
			
			//加载技能素材
			for (i = 0; i < needMats.length; i++ )
			{
				var className:String = needMats[i];
				if (SwfClassCache.getInstance().hasClass(className))
				{
					needMats.splice(i, 1);
					i--;
				}
				else
				{
					SwfClassCache.getInstance().addEventListener(SwfClassCacheEvent.COMPLETE, onMatLoadComplete);
					SwfClassCache.getInstance().loadClass(className);
				}
			}
			if (needMats.length == 0) onMatLoadComplete();
			
			//播放音乐
			var staticHost:String = InterfaceURLManager.getInstance().staticHost;
			if (isBossBattle) BgMusicManager.getInstance().setSound(staticHost + "bossBattle.mp3");
			else BgMusicManager.getInstance().setSound(staticHost + "normalBattle.mp3");
			
			operateType = CANTOPERATE;
			
			//设置自动战斗
			autoTimes --;
			ui.autoOperateBar.update(autoTimes);
			auto = autoTimes > 0;
		}
		
		private function onMatLoadComplete(event:SwfClassCacheEvent = null):void
		{
			if (event)
			{
				var index:int = needMats.indexOf(event.className);
				if (index != -1) needMats.splice(index, 1);
			}
			if (needMats.length == 0 && leftRoleNum <= 0)
			{
				SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE, onMatLoadComplete);
				begin();
			}
		}
		
		private function onRoleLoadComplete(event:Event = null):void
		{
			if (event) event.target.removeEventListener(Event.COMPLETE, onRoleLoadComplete);
			leftRoleNum --;
			if (needMats.length == 0 && leftRoleNum <= 0)
			{
				SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE, onMatLoadComplete);
				begin();
			}
		}
		
		//战斗开始阶段----------------------------------------
		private function begin():void
		{
			//双方跑步进场
			battleAnimationManager.addEventListener(Event.COMPLETE, onEnterBattleFieldComplete);
			battleAnimationManager.enterBattleField(vo.roleList);
		}
		
		private function onEnterBattleFieldComplete(event:Event):void
		{
			battleAnimationManager.removeEventListener(Event.COMPLETE, onEnterBattleFieldComplete);
			
			nextTalk(); //开场说话
		}
		
		private function nextTalk():void
		{
			if (vo.talk.length > 0)
			{
				var arr:Array = vo.talk.shift();
				var target:Role = battleField.getRoleByIndex(int(arr[0]));
				var talkView:TalkView = new TalkView(target,arr[1],nextTalk,battleField.stage);
				battleField.coverAnimLayer.addChild(talkView);
			}
			else
			{
				EventManager.getInstance().dispatchEvent(new BattleEvent(BattleEvent.START_FIGHT));
				ui.orderTimeView.setQueue(actionQueue, curRoleActionDone);
			}
		}
		
		//战斗进行阶段----------------------------------------
		//角色行动完毕
		private function curRoleActionDone():void
		{
			if (curRoleVo && curRoleVo.hp>0 && curStatusRecord) //如果有状态可能需要表现消失
			{
				battleAnimationManager.addEventListener(Event.COMPLETE, gotoNextRole);
				battleAnimationManager.playDisappearedStatus(curStatusRecord);
			}
			else gotoNextRole();
		}
		
		//处理下一个角色
		private function gotoNextRole(event:Event = null):void
		{
			if (event) event.target.removeEventListener(Event.COMPLETE, gotoNextRole);
			
			ui.userInfoView.hide();
			ui.orderTimeView.nextRole(); //行动条运动到下一个角色
		}
		
		//行动条动画播放完毕 角色信息也已经显示
		private function onNextRoleUiComplete():void
		{
			//获取下一个行动角色
			curRoleVo = actionQueue.shift();
			actionQueue.push(curRoleVo);
			DataManager.getInstance().setVar("curRoleVoInBattle", curRoleVo); //放到DataManager中以供UI使用
			
			//显示角色信息面板
			ui.userInfoView.setRole(curRoleVo);
			
			//处理状态
			var targetRecordVo:BattleRecordTargetVo = battleDataManager.dealStatus(curRoleVo);
			canAction = targetRecordVo.canAction;
			canUseSkill = targetRecordVo.canUseSkill;
			battleAnimationManager.addEventListener(Event.COMPLETE, allStatusDone);
			curStatusRecord = battleDataManager.curRecord;
			battleAnimationManager.playRecord(curStatusRecord);
		}
		
		//所有状态都处理完毕
		private function allStatusDone(event:Event):void
		{
			battleAnimationManager.removeEventListener(Event.COMPLETE, allStatusDone);
			
			if (curRoleVo.hp <= 0) //如果当前角色死亡
			{
				//从行动列表中移除该单位
				var index:int = actionQueue.indexOf(curRoleVo);
				if (index != -1) actionQueue.splice(index, 1);
				ui.orderTimeView.removeRole(curRoleVo.pos);
				
				if (isEnemy(curRoleVo)) //如果是怪
				{
					battleField.removeRole(battleField.getRoleByIndex(curRoleVo.pos)); //从场景中移除
					for each(var conditionVo:ConditionVo in curRoleVo.items) pushItemIntoBag(conditionVo); //将其物品移动到主角的包内
				}
				
				if (scanAllDown(isEnemy(curRoleVo))) //如果当前角色方全军覆没
				{
					if (isEnemy(curRoleVo)) endBattle(BattleVo.WIN); //如果目标方是怪 战斗胜利
					else endBattle(BattleVo.LOST); //否则战斗失败
				}
				else curRoleActionDone();
			}
			else if (!canAction) //角色经过状态的作用后无法继续行动
			{
				curRoleActionDone();
			}
			else //可以继续行动
			{	
				if (isEnemy(curRoleVo) || auto) runCurRoleAI(); //如果当前角色是敌方角色 或者自动战斗 则执行AI
				else
				{
					waitingForPlayer(); //否则 等待用户操作
					if(!ui.friendSkillView.skillUsed && ui.friendSkillView.visible) ui.friendSkillView.unfreezeSkill();
				}
			}
		}
		
		//等待用户操作
		private function waitingForPlayer():void
		{
			operateType = NOOPERATE;
			ui.operateBar.show(); //显示操作条
			ui.friendSkillView.show(); //显示好友援助条
			battleField.selectRole(battleField.getRoleByIndex(curRoleVo.pos)); //箭头指向该角色
			
			var btns:Array = new Array;
			if (isBossBattle) btns.push(4);
			if (!canUseSkill)
			{
				btns.push(1);
				btns.push(2);
			}
			//根据角色是否可攻击 冻结或解冻 操作条上的攻击按钮
			if (curRoleVo.profession != 2 && battleField.isFrontOccupied(curRoleVo.pos))
			{
				btns.push(0);
				ui.operateBar.frozeBtn(btns);
			}
			else
			{
				ui.operateBar.frozeBtn(btns);
				startAttackOperation();
			}
			EventManager.getInstance().dispatchEvent(new Event("battle_playerAction"));
		}
		
		//用户操作--------------------------------------------------------
		
		//战场鼠标事件分派
		//以下函数响应战场中任意地形块、背景的鼠标滑入、滑出和点击事件
		//并根据当前的操作类型 分派给不同的响应函数
		private function onBattleFieldClick(event:MouseEvent):void
		{
			//if (operateType == CANTOPERATE) return;
			var funcName:String = "onClick_" + operateType;
			if (hasOwnProperty(funcName)) this[funcName](event.target);
		}
		private function onBattleFieldRollOver(event:MouseEvent):void
		{
			//if (operateType == CANTOPERATE) return;
			var funcName:String = "onRollOver_" + operateType;
			if (hasOwnProperty(funcName)) this[funcName](event.target);
		}
		private function onBattleFieldRollOut(event:MouseEvent):void
		{
			//if (operateType == CANTOPERATE) return;
			var funcName:String = "onRollOut_" + operateType;
			if (hasOwnProperty(funcName)) this[funcName](event.target);
		}
		
		//响应攻击按钮 开始攻击操作
		private function startAttackOperation(event:BattleEvent = null):void
		{
			if (operateType == CANTOPERATE) return;
			if (operateType == SELECT_TARGET) endSelectTargetOperation();
			
			curSkillAndItemVo = curRoleVo.profession == 2 ? Skills.farAttackVo:Skills.closeAttackVo;
			curItemId = 0;
			startSelectTargetOperation();
			
			//设置手型
			MouseManager.getInstance().setTmpIcon(MouseManager.getInstance().getMouseIcon(MagicMouseIconType.SWORD_HAND));
		}
		
		//响应使用技能或物品操作
		private function startSkillOrItemOperation(event:BattleEvent):void
		{
			if (operateType == CANTOPERATE) return;
			if (operateType == SELECT_TARGET) endSelectTargetOperation();
			
			curSkillAndItemVo = event.skillAndItemVo;
			curItemId = event.itemId;
			
			for (var i:int = 0; i < vo.friendSkill.length; i++)
			{
				if (vo.friendSkill[i].skill.cid == curSkillAndItemVo.cid) //好友援助技能
				{
					curFriend = vo.friendSkill[i].role;
					break;
				}
			}
			
			//判断是否需要选择目标
			if (curSkillAndItemVo.target != SkillAndItemVo.SELF && curSkillAndItemVo.area != SkillAndItemVo.ALL)
			{
				startSelectTargetOperation(); //选择目标
				
				//设置手型
				if(curItemId) MouseManager.getInstance().setTmpIcon(MouseManager.getInstance().getMouseIcon(MagicMouseIconType.PICK_HAND));
				else MouseManager.getInstance().setTmpIcon(MouseManager.getInstance().getMouseIcon(MagicMouseIconType.STUDENT_HAND));
			}
			else //不需要选择目标的情况
			{
				var roleVo:RoleVo;
				var role:Role;
				targets = new Vector.<RoleVo>();
				
				if (curSkillAndItemVo.target == SkillAndItemVo.SELF) //目标是自己
				{
					if (curSkillAndItemVo.area == SkillAndItemVo.SINGLE) //以自己为中心的单体
					{
						targets.push(curRoleVo);
						targetGrid = curRoleVo.pos; //原地
						keyGrid = battleField.getFrontGridIndex(targetGrid);
					}
					else
					{
						if (curSkillAndItemVo.area == SkillAndItemVo.ROW) //以自己为中心的行
						{
							curSelectedGrids = battleField.getGridsInRow(curRoleVo.pos);
							targetGrid = curSelectedGrids[1]; //行中心
							keyGrid = battleField.getFrontGridIndex(targetGrid);
						}
						else if (curSkillAndItemVo.area == SkillAndItemVo.COL) //以自己为中心的列
						{
							curSelectedGrids = battleField.getGridsInCol(curRoleVo.pos);
							targetGrid = curSelectedGrids[0]; //列中心
							keyGrid = battleField.getFrontGridIndex(targetGrid);
						}
						else if (curSkillAndItemVo.area == SkillAndItemVo.CROSS) //以自己为中心的十字
						{
							curSelectedGrids = battleField.getGridsInCross(curRoleVo.pos);
							targetGrid = curSelectedGrids[0]; //十字中心
							keyGrid = battleField.getFrontGridIndex(battleField.getFrontGridIndex(targetGrid));
						}
						
						curSelectedGrids.sort(Array.NUMERIC); //从小到大排序
						for (i = 0; i < curSelectedGrids.length; i++) //设置目标
						{
							role = battleField.getRoleByIndex(curSelectedGrids[i]);
							if (role) targets.push(role.vo);
						}
					}
				}
				else if (curSkillAndItemVo.target == SkillAndItemVo.FRIEND) //目标是友方全体
				{
					for (i = 9; i < 18; i++) //从小到大依次选择
					{
						role = battleField.getRoleByIndex(i);
						if (role && role.vo.hp>0) targets.push(role.vo);
					}
					targetGrid = 13;
					keyGrid = 25;
				}
				else if (curSkillAndItemVo.target == SkillAndItemVo.DOWNFRIEND) //目标是友方全体
				{
					for (i = 9; i < 18; i++) //从小到大依次选择
					{
						role = battleField.getRoleByIndex(i);
						if (role && role.vo.hp<=0) targets.push(role.vo);
					}
					targetGrid = 13;
					keyGrid = 25;
				}
				else if (curSkillAndItemVo.target == SkillAndItemVo.ENEMY) //目标是敌方全体
				{
					for (i = 0; i < 9; i++) //从小到大依次选择
					{
						role = battleField.getRoleByIndex(i);
						if (role) targets.push(role.vo);
					}
					targetGrid = 4;
					keyGrid = 19;
				}
				else //目标是敌我全体
				{
					for (i = 0; i < 18; i++) //从小到大依次选择
					{
						role = battleField.getRoleByIndex(i);
						if (role) targets.push(role.vo);
					}
					keyGrid = targetGrid = 22;
				}
				
				dealSkillAndItem(); //开始处理
			}
		}
		
		//响应防御操作
		private function startDefenseOperation(event:BattleEvent):void
		{
			if (operateType == CANTOPERATE) return;
			if (operateType == SELECT_TARGET) endSelectTargetOperation();
			
			curSkillAndItemVo = Skills.defenseVo;
			curItemId = 0;
			targets = new Vector.<RoleVo>();
			targets.push(curRoleVo);
			targetGrid = curRoleVo.pos;
			keyGrid = battleField.getFrontGridIndex(targetGrid);
			
			dealSkillAndItem(); //开始处理
		}
		
		//响应逃跑操作
		private function startEscapeOperation(event:BattleEvent):void
		{
			if (operateType == CANTOPERATE) return;
			if (operateType == SELECT_TARGET) endSelectTargetOperation();
			
			ui.hideOperateBar(); //隐藏UI
			operateType = CANTOPERATE; //设为不可操作
			
			var targets:Vector.<RoleVo> = new Vector.<RoleVo>();
			var friends:Vector.<RoleVo> = new Vector.<RoleVo>();
			for each(var roleVo:RoleVo in actionQueue)
			{
				if (isEnemy(roleVo)) targets.push(roleVo);
				else friends.push(roleVo);
			}
			
			battleDataManager.dealEscape(curRoleVo, targets);
			battleAnimationManager.addEventListener(Event.COMPLETE, onEscAnimationComplete);
			battleAnimationManager.playEscape(friends, battleField.mainRole, battleDataManager.curRecord.escSuccess);
		}
		
		//响应自动战斗操作
		private function startAutoOperation(event:BattleEvent):void
		{
			if (operateType == CANTOPERATE) return;
			if (operateType == SELECT_TARGET) endSelectTargetOperation();
			else battleField.hideSelectSign();
			
			ui.hideOperateBar(); //隐藏UI
			operateType = CANTOPERATE; //设为不可操作
			
			auto = true;
			autoTimes = 10;
			runCurRoleAI();
		}
		
		
		//根据curSkillAndItemVo显示可选范围 开始选择目标
		private function startSelectTargetOperation():void
		{
			battleField.showGrid(); //显示地块
			battleField.setRoleDarkness(curRoleVo); //将非当前行动的我方角色变暗
			battleField.setHpBarVisible(true); //显示血条
			
			var roleVo:RoleVo;
			var i:int;
			
			if (curSkillAndItemVo.target == SkillAndItemVo.FRIEND) //如果是选择友方单位
			{
				switch(curSkillAndItemVo.area)
				{
					case SkillAndItemVo.SINGLE: //如果是选择单体
						for each(roleVo in actionQueue) //遍历所有还活着的角色
						{
							if (!isEnemy(roleVo)) battleField.setGridSelectable(roleVo.pos, true); //将所有友方单位脚下的格子设置为蓝色
						}
					break;
					case SkillAndItemVo.ROW: //如果是选择行
					case SkillAndItemVo.COL: //如果是选择列
					case SkillAndItemVo.CROSS: //如果是十字
						for (i = 9; i < 18; i++) battleField.setAllGridSelectable(false, true); //将所有友方地块设置为蓝色
					break;
				}
			}
			else if (curSkillAndItemVo.target == SkillAndItemVo.DOWNFRIEND) //如果是选择倒下的友方单位
			{
				switch(curSkillAndItemVo.area)
				{
					case SkillAndItemVo.SINGLE: //如果是选择单体
						var list:Vector.<RoleVo> = new Vector.<RoleVo>();
						for each(roleVo in vo.roleList) //遍历所有角色
						{
							if (!isEnemy(roleVo)) //如果是友方角色
							{
								var index:int = actionQueue.indexOf(roleVo);
								if (index == -1) list.push(roleVo);//如果不在行动队列中 说明死了
							}
						}
						for each(roleVo in list) battleField.setGridSelectable(roleVo.pos, true); //将这些角色的脚下设为绿色
					break;
					case SkillAndItemVo.ROW: //如果是选择行
					case SkillAndItemVo.COL: //如果是选择列
					case SkillAndItemVo.CROSS: //如果是十字
						for (i = 9; i < 18; i++) battleField.setAllGridSelectable(false, true); //将所有友方地块设置为蓝色
					break;
				}
			}
			else //如果是选择敌方单位
			{
				if (curSkillAndItemVo.range == SkillAndItemVo.CLOSE) //如果是近身攻击
				{
					for each(roleVo in actionQueue) //遍历所有还活着的角色
					{
						if (isEnemy(roleVo)) battleField.setGridSelectable(roleVo.pos, true); //将所有敌方单位脚下的格子设置为蓝色
					}
					var unattackableTarget:Vector.<RoleVo> = getCloseUnattackableTarget();
					for each(roleVo in unattackableTarget) //将所有不可被近身攻击的目标
					{
						//battleField.getRoleByIndex(roleVo.pos).selectable = false; //角色设为灰色
						battleField.setGridSelectable(roleVo.pos, false); //脚下的地块从蓝色变回无色
					}
				}
				else //远程攻击
				{
					switch(curSkillAndItemVo.area)
					{
						case SkillAndItemVo.SINGLE: //如果是选择单体
							for each(roleVo in actionQueue) //遍历所有还活着的角色
							{
								if (isEnemy(roleVo)) battleField.setGridSelectable(roleVo.pos, true); //将所有敌方单位脚下的格子设置为蓝色
							}
						break;
						case SkillAndItemVo.ROW: //如果是选择行
						case SkillAndItemVo.COL: //如果是选择列
						case SkillAndItemVo.CROSS: //如果是十字
							for (i = 0; i < 9; i++) battleField.setAllGridSelectable(true, true); //将所有敌方地块设置为蓝色
						break;
					}
				}
			}
			operateType = SELECT_TARGET;
			battleField.onMouseMove();
		}
		
		//在选择目标操作下点击某物
		public function onClick_SelectTarget(obj:DisplayObject):void
		{
			var grid:Grid = obj as Grid;
			if (!grid) return;
			
			if (grid.selectable)
			{
				//确定关键点(角色施放技能时所站的点)和目标点
				if (curSkillAndItemVo.area == SkillAndItemVo.SINGLE) //单体
				{
					targetGrid = curSelectedGrids[0];
					keyGrid = battleField.getFrontGridIndex(targetGrid);
				}
				else if (curSkillAndItemVo.area == SkillAndItemVo.ROW) //行
				{
					targetGrid = curSelectedGrids[1]; //行中心
					keyGrid = battleField.getFrontGridIndex(targetGrid);
				}
				else if (curSkillAndItemVo.area == SkillAndItemVo.COL) //列
				{
					targetGrid = curSelectedGrids[0]; //列中心
					keyGrid = battleField.getFrontGridIndex(targetGrid);
				}
				else if (curSkillAndItemVo.area == SkillAndItemVo.CROSS) //十字
				{
					targetGrid = curSelectedGrids[0]; //十字中心
					keyGrid = battleField.getFrontGridIndex(battleField.getFrontGridIndex(targetGrid));
				}
				
				//确定目标
				targets = new Vector.<RoleVo>();
				curSelectedGrids.sort(Array.NUMERIC); //从小到大排序
				for (var i:int = 0; i < curSelectedGrids.length; i++)
				{
					var role:Role = battleField.getRoleByIndex(curSelectedGrids[i]);
					if (role)
					{
						if (curSkillAndItemVo.target == SkillAndItemVo.DOWNFRIEND)
						{
							if(role.vo.hp<=0) targets.push(role.vo);
						}
						else if (role.vo.hp > 0) targets.push(role.vo);
					}
				}
				if (targets.length == 0) return; //如果一个目标也没有选到 则认为是误操作 不予处理
				
				endSelectTargetOperation(); //结束选目标操作 UI变回
				
				dealSkillAndItem(); //开始处理
			}
		}
		
		//在选择目标操作下滑过某物
		public function onRollOver_SelectTarget(obj:DisplayObject):void
		{
			var grid:Grid = obj as Grid;
			if (!grid) return;
			dispatchLightRoleEvent(grid.index);
			if (!grid.selectable) return;
			
			switch(curSkillAndItemVo.area)
			{
				case SkillAndItemVo.SINGLE: //如果是选择单体
					curSelectedGrids = [grid.index];
				break;
				case SkillAndItemVo.ROW: //如果是选择行
					curSelectedGrids = battleField.getGridsInRow(grid);
				break;
				case SkillAndItemVo.COL: //如果是选择列
					curSelectedGrids = battleField.getGridsInCol(grid);
				break;
				case SkillAndItemVo.CROSS: //如果是选择十字
					curSelectedGrids = battleField.getGridsInCross(grid);
				break;
			}
			battleField.select(grid);
			for (var i:int = 0; i < curSelectedGrids.length; i++) battleField.setGridSelected(curSelectedGrids[i], true);
		}
		
		//在选择目标操作下滑出某物
		public function onRollOut_SelectTarget(obj:DisplayObject):void
		{
			var grid:Grid = obj as Grid;
			if (!grid) return;
			dispatchLightRoleEvent(grid.index, true);
			
			if (curSelectedGrids != null) //如果之前还选着一组格子 将它们的颜色变回来
			{
				for (var i:int = 0; i < curSelectedGrids.length; i++) battleField.setGridSelected(curSelectedGrids[i], false);
				curSelectedGrids = null;
			}
		}
		
		private function dispatchLightRoleEvent(pos:int,closeLight:Boolean = false):void
		{
			var role:Role = battleField.getRoleByIndex(pos);
			if (role)
			{
				hightLightRolePos = closeLight ? -1 : pos;
				var event:BattleEvent = new BattleEvent(closeLight ? BattleEvent.CLOSE_LIGHT_ROLE : BattleEvent.LIGHT_ROLE);
				event.pos = pos;
				EventManager.getInstance().dispatchEvent(event);
			}
		}
		
		//结束目标选择的操作
		private function endSelectTargetOperation():void
		{
			battleField.setHpBarVisible(false); //隐藏所有角色的血条
			battleField.setRoleDarkness(curRoleVo, false); //取消我方所有角色的变暗效果
			battleField.setAllGridSelectable(true, false); //所有敌方地块变回无色
			battleField.setAllGridSelectable(false, false); //所有我方地块变回无色
			battleField.hideGrid(); //隐藏地块
			battleField.hideSelectSign(); //隐藏选择标记(箭头和会动的地块)
			//battleField.resetAllRoleSelectable(); //所有角色从灰色变回彩色
			
			if (operateType != CANTOPERATE) operateType = NOOPERATE; //重设操作标记
			
			MouseManager.getInstance().clearTmpIcon();
		}
		
		//处理技能 计算数值 并播放动画
		private function dealSkillAndItem():void
		{
			ui.hideOperateBar(); //隐藏UI
			battleField.hideSelectSign(); //隐藏箭头
			operateType = CANTOPERATE; //设为不可操作
			
			if (hightLightRolePos != -1) dispatchLightRoleEvent(hightLightRolePos, true); //如果有高亮角色 取消高亮
			
			if (curFriend) //如果是援助技能
			{
				ui.friendSkillView.freezeSkill(); //冻结援助技能面板
				ui.friendSkillView.skillUsed = true; //标记为此次战斗已经使用过援助技能
			}
			
			
			battleDataManager.dealSkillAndItem(curSkillAndItemVo, curRoleVo, targetGrid, targets, keyGrid, curItemId, curFriend != null); //进行各种数据处理
			battleAnimationManager.addEventListener(Event.COMPLETE, onAnimationComplete);
			battleAnimationManager.playRecord(battleDataManager.curRecord, curFriend); //根据处理结果播放动画
			ui.userInfoView.refresh(); //刷新一下血蓝
			curItemId = 0; //重置数据
		}
		
		//播放完动画回调
		private function onAnimationComplete(event:Event):void
		{
			battleAnimationManager.removeEventListener(Event.COMPLETE, onAnimationComplete);
			
			if (scanRecordId()) return;
			
			//检查死亡
			var battleRecordVo:BattleRecordVo = battleDataManager.curRecord;
			for (var i:int = 0; i < battleRecordVo.targetInfo.length; i++)
			{
				var role:Role = battleField.getRoleByIndex(battleRecordVo.targetInfo[i].index);
				if (role.vo.hp <= 0)
				{
					//从行动列表中移除该单位
					var index:int = actionQueue.indexOf(role.vo);
					if (index != -1) actionQueue.splice(index, 1);
					ui.orderTimeView.removeRole(role.vo.pos);
					
					if (isEnemy(role.vo)) //如果是怪
					{
						battleField.removeRole(role); //从场景中移除
						for each(var conditionVo:ConditionVo in role.vo.items) pushItemIntoBag(conditionVo); //将其物品移动到主角的包内
					}
				}
			}
			
			if (scanAllDown(isEnemy(role.vo))) //如果目标方全军覆没
			{
				if (isEnemy(role.vo)) endBattle(BattleVo.WIN); //如果目标方是怪 战斗胜利
				else endBattle(BattleVo.LOST); //否则战斗失败
			}
			else if (curFriend) //如果刚才使用的是好友援助技能 则不会使当前角色结束行动
			{
				//重置好友角色
				curFriend.x = BattleField.FRIEND_POS_X;
				curFriend.y = BattleField.FRIEND_POS_Y;
				curFriend.alpha = 1;
				curFriend = null;
				
				ui.friendSkillView.reduceTimes();
				ui.friendSkillView.freezeSkill(); //一次战斗只能使用一次
				waitingForPlayer(); //等待用户操作
			}
			else curRoleActionDone(); //当前角色结束行动
		}
		
		//播放完逃跑动画
		private function onEscAnimationComplete(event:Event):void
		{
			battleAnimationManager.removeEventListener(Event.COMPLETE, onEscAnimationComplete);
			
			if (scanRecordId()) return;
			
			var battleRecordVo:BattleRecordVo = battleDataManager.curRecord;
			if (battleRecordVo.escSuccess) //逃跑成功
			{
				endBattle(BattleVo.ESC);
			}
			else curRoleActionDone();
		}
		
		//无操作状态下鼠标滑过角色
		public function onRollOver_NoOperate(obj:DisplayObject):void
		{
			var grid:Grid = obj as Grid;
			if (!grid) return;
			var role:Role = battleField.getRoleByIndex(grid.index);
			if (!role) return;
			
			var event:BattleEvent = new BattleEvent(BattleEvent.LIGHT_ROLE);
			event.pos = grid.index;
			EventManager.getInstance().dispatchEvent(event);
		}
		
		//无操作状态下鼠标滑出角色
		public function onRollOut_NoOperate(obj:DisplayObject):void
		{
			var grid:Grid = obj as Grid;
			if (!grid) return;
			var role:Role = battleField.getRoleByIndex(grid.index);
			if (!role) return;
			
			var event:BattleEvent = new BattleEvent(BattleEvent.CLOSE_LIGHT_ROLE);
			event.pos = grid.index;
			EventManager.getInstance().dispatchEvent(event);
		}
		
		//不可操作状态下鼠标滑过角色
		public function onRollOver_CantOperate(obj:DisplayObject):void
		{
			//var grid:Grid = obj as Grid;
			//if (!grid) return;
			//var role:Role = battleField.getRoleByIndex(grid.index);
			//if (!role) return;
		}
		
		//不可操作状态下鼠标滑过角色
		public function onRollOut_CantOperate(obj:DisplayObject):void
		{
			//var grid:Grid = obj as Grid;
			//if (!grid) return;
			//var role:Role = battleField.getRoleByIndex(grid.index);
			//if (!role) return;
		}
		
		
		//战斗结束阶段----------------------------------------
		//战斗结束
		private function endBattle(result:int):void
		{
			vo.result = result;
			
			if (vo.result == BattleVo.LOST) //如果战斗失败 主角先暴物品
			{
				battleAnimationManager.addEventListener(Event.COMPLETE, continueEndBattle);
				battleAnimationManager.playMainRoleDropItems(battleField.mainRole);
			}
			else continueEndBattle();
		}
		
		private function continueEndBattle(event:Event = null):void
		{
			//trace(battleDataManager.getDescription());
			//trace("---------------------------------------------------------------------");
			//trace(encodeJson(battleDataManager.getOperateInfo()));
			
			if(event) battleAnimationManager.removeEventListener(Event.COMPLETE, continueEndBattle);
			
			//发包通知后端
			var command:BattleEndCommand = new BattleEndCommand(battleDataManager.getOperateInfo(), battleDataManager.getLog(), isTest?testUid:0);
			command.addEventListener(Event.COMPLETE, onBattleEndComplete);
			
			ui.autoOperateBar.visible = 
			ui.operateBar.visible = 
			ui.orderTimeView.visible = 
			ui.friendSkillView.visible = 
			ui.userInfoView.visible = false;
			
			//ui.operateBar.parent.removeChild(ui.operateBar);
		}
		
		//从后端收到回包 弹出面板
		private function onBattleEndComplete(event:Event):void
		{
			event.target.removeEventListener(Event.COMPLETE, onBattleEndComplete);
			
			var command:BattleEndCommand = event.target as BattleEndCommand;
			
			DataManager.getInstance().setVar("battleResult", vo.result);
			DataManager.getInstance().setVar("isBossBattle", isBossBattle);
			DataManager.getInstance().setVar("battleEndScene", command.nextScene);
			
			if (command.confirm)
			{
				if (vo.result == BattleVo.WIN)
				{
					exp = new Array;
					for each (var subArr:Array in command.exp)
					{
						var theRoleVo:RoleVo;
						for each(var roleVo:RoleVo in vo.roleList)
						{
							if (roleVo.pos == Number(subArr[0]))
							{
								theRoleVo = roleVo;
								break;
							}
						}
						if (theRoleVo)
						{
							var obj:Object = { name:theRoleVo.name, exp:subArr[1] };
							var levelUpTo:int = subArr[2];
							if (levelUpTo != 0)
							{
								obj["level"] = levelUpTo;
								theRoleVo.level = levelUpTo;
								theRoleVo.exp = int(subArr[3]);
								theRoleVo.maxExp = int(subArr[4]);
							}
							else theRoleVo.exp += int(subArr[1]);
							
							exp.push(obj);
						}
					}
					battleAnimationManager.endBattle(actionQueue);
					BgMusicManager.getInstance().setSound(InterfaceURLManager.getInstance().staticHost + "battleWin.mp3", 1);
					awards = command.objdata;
					showPanelCount();
				}
				else if (vo.result == BattleVo.LOST)
				{
					battleAnimationManager.endBattle(actionQueue, true);
					BgMusicManager.getInstance().setSound(InterfaceURLManager.getInstance().staticHost + "battleLost.mp3",1);
					showPanelCount();
				}
				else if (vo.result == BattleVo.ESC) close();
				
				//将我方人员的数据更新到DataManager内
				//for each(roleVo in vo.roleList)
				//{
					//if (isEnemy(roleVo)) continue;
					//var changeData:Object = new Object;
					//changeData["id"] = roleVo.id;
					//changeData["hp"] = roleVo.hp <= 0 ? 1 : roleVo.hp;
					//changeData["mp"] = roleVo.mp;
					//changeData["exp"] = roleVo.exp;
					//changeData["maxExp"] = roleVo.maxExp;
					//changeData["level"] = roleVo.level;
					//
					//DataManager.getInstance().roleData.changeRole(changeData);
				//}
			}
		}
		
		private function showPanelCount():void
		{
			var timer:Timer = new Timer(3000, 1);
			timer.addEventListener(TimerEvent.TIMER_COMPLETE, showPanel);
			timer.start();
		}
		
		private function showPanel(event:TimerEvent):void
		{
			event.target.removeEventListener(TimerEvent.TIMER_COMPLETE, showPanel);
			
			if (vo.result == BattleVo.WIN)
			{
				var awardConditions:Vector.<ConditionVo> = ConditionVo.turnResultVoToConditions(awards["result"], awards["addItems"]);
				var battleWinView:BattleWinView = DisplayManager.uiSprite.addModule("battleWin", "happymagic.battle.view.ui.BattleWinView", false, AlginType.CENTER, 0, 10) as BattleWinView;
				battleWinView.setData(exp, awardConditions);
			}
			else if (vo.result == BattleVo.LOST)
			{
				DisplayManager.uiSprite.addModule("battleFault", "happymagic.battle.view.ui.BattleFaultView", false, AlginType.CENTER, 0, 10);
			}
		}
		
		private function close():void
		{
			clear();
			ModuleManager.getInstance().closeModule("battle", true);
			EventManager.getInstance().dispatchEvent(new Event(BATTLE_END));
		}
		
		//其他------------------------------------------------
		
		//判断某角色是否敌方角色
		private function isEnemy(roleVo:RoleVo):Boolean
		{
			return roleVo.pos < 9;
		}
		
		//获得某阵营中所有无法被近战攻击攻击到的角色
		private function getCloseUnattackableTarget(targetIsEnemy:Boolean = true):Vector.<RoleVo>
		{
			var result:Vector.<RoleVo> = new Vector.<RoleVo>();
			for each(var roleVo:RoleVo in actionQueue)
			{
				if (isEnemy(roleVo))
				{
					if (targetIsEnemy)
					{
						if (battleField.isFrontOccupied(roleVo.pos)) result.push(roleVo);
					}
				}
				else
				{
					if (!targetIsEnemy)
					{
						if (battleField.isFrontOccupied(roleVo.pos)) result.push(roleVo);
					}
				}
			}
			return result;
		}
		
		//检查战场中某一阵营是否已经全军覆没
		private function scanAllDown(enemySide:Boolean):Boolean
		{
			for each(var roleVo:RoleVo in vo.roleList)
			{
				if (enemySide && isEnemy(roleVo) && roleVo.hp > 0) return false;
				if (!enemySide && !isEnemy(roleVo) && roleVo.hp > 0) return false;
			}
			return true;
		}
		
		//执行当前角色的AI脚本
		private function runCurRoleAI():void
		{
			if (battleAIManager.deal(curRoleVo)) //处理AI
			{
				if (battleAIManager.actionType == AIActionVo.SKILL_AND_ITEM) //攻击或使用技能
				{
					curSkillAndItemVo = battleAIManager.skillAndItemVo;
					targets = battleAIManager.targets;
					targetGrid = battleAIManager.targetGrid;
					keyGrid = battleAIManager.keyGrid;
					
					dealSkillAndItem(); //开始处理
				}
			}
			else //没有行动 防御一下
			{
				curSkillAndItemVo = Skills.defenseVo;
				targets = new Vector.<RoleVo>();
				targets.push(curRoleVo);
				targetGrid = curRoleVo.pos;
				keyGrid = battleField.getFrontGridIndex(curRoleVo.pos);
				
				dealSkillAndItem();
			}
		}
		
		//检查当前战斗记录的序号
		private function scanRecordId():Boolean
		{
			var error:Boolean = battleDataManager.curRecord.id > 1000;
			if (error) //如果超过1000 则认为是数据异常
			{
				//立即结束战斗 通知用户刷新页面
				close();
				DataManager.getInstance().setVar("battleResult", BattleVo.ERROR);
				DataManager.getInstance().setVar("battleEndScene", 0);
				dispatchEvent(new Event(BATTLE_END));
			}
			return error;
		}
		
		//侦听输赢面板的关闭
		private function onModuleClose(event:ModuleEvent):void
		{
			if (event.moduleName == "battleWin" || event.moduleName == "battleFault")
			{
				event.target.removeEventListener(ModuleEvent.MODULE_CLOSE, onModuleClose);
				close();
			}
		}
		
		//将一件东西装入主角的背包
		private function pushItemIntoBag(item:ConditionVo):void
		{
			for each(var conditionVo:ConditionVo in battleField.mainRole.vo.items)
			{
				if (conditionVo.type == item.type && conditionVo.id == item.id) //如果已有此物品 合并数量
				{
					conditionVo.num += item.num;
					return;
				}
			}
			battleField.mainRole.vo.items.push(item);
		}
		
		private function lightRole(event:BattleEvent):void
		{
			var role:Role = battleField.getRoleByIndex(event.pos);
			if (role) role.addFilter(AnimationClip.highLightFilter, AnimationClip.HIGHTLIGHT_FILTER);
		}
		
		private function closeLightRole(event:BattleEvent):void
		{
			var role:Role = battleField.getRoleByIndex(event.pos);
			if (role) role.removeFilter(AnimationClip.HIGHTLIGHT_FILTER);
		}
		
		private function cancelAuto(event:BattleEvent):void
		{
			auto = false;
			autoTimes = 0;
		}
		
		//清除本对象
		public function clear():void
		{
			battleField.clear();
			ui.clear();
			
			battleField.removeEventListener(MouseEvent.CLICK, onBattleFieldClick);
			battleField.removeEventListener(MouseEvent.ROLL_OVER, onBattleFieldRollOver);
			battleField.removeEventListener(MouseEvent.ROLL_OUT, onBattleFieldRollOut);
			EventManager.getInstance().removeEventListener(BattleEvent.REQUEST_ATTACK, startAttackOperation);
			EventManager.getInstance().removeEventListener(BattleEvent.REQUEST_SKILL, startSkillOrItemOperation);
			EventManager.getInstance().removeEventListener(BattleEvent.REQUEST_DEFENSE, startDefenseOperation);
			EventManager.getInstance().removeEventListener(BattleEvent.REQUEST_ESC, startEscapeOperation);
			EventManager.getInstance().removeEventListener(BattleEvent.REQUEST_AUTO, startAutoOperation);
			EventManager.getInstance().removeEventListener(BattleEvent.LIGHT_ROLE, lightRole);
			EventManager.getInstance().removeEventListener(BattleEvent.CLOSE_LIGHT_ROLE, closeLightRole);
			EventManager.getInstance().removeEventListener(BattleEvent.REQUEST_CLOSE_AUTO, cancelAuto);
			ModuleManager.getInstance().removeEventListener(ModuleEvent.MODULE_CLOSE, onModuleClose);
		}
		
		//常量--------------------------------------------------
		public static const BG_WIDTH:int = 640; //战场背景宽
		public static const BG_HEIGHT:int = 445; //战场背景高
		
		public static const CANTOPERATE:String = "CantOperate"; //无法操作
		public static const NOOPERATE:String = "NoOperate"; //没有任何操作
		public static const SELECT_TARGET:String = "SelectTarget"; //选择目标
		
		public static const BATTLE_END:String = "battleEnd"; //战斗结束事件
		
	}
}