package happymagic.battle.controller 
{
	import com.greensock.easing.RoughEase;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.geom.Point;
	import happymagic.battle.Battle;
	import happymagic.battle.model.vo.BattleRecordTargetVo;
	import happymagic.battle.model.vo.FriendSkillVo;
	import happymagic.model.vo.EffectDictionary;
	import happymagic.battle.model.vo.BattleRecordVo;
	import happymagic.battle.view.battlefield.BattleField;
	import happymagic.battle.view.battlefield.Role;
	import happymagic.battle.view.ui.CustomNumber;
	import happymagic.model.vo.AnimationVo;
	import happymagic.model.vo.RolePropType;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.SkillAndItemVo;
	import happymagic.model.vo.StatusVo;
	/**
	 * 流程动画管理器
	 * @author XiaJunJie
	 */
	public class BattleAnimationManager extends EventDispatcher
	{
		//组合动画的数据
		private var battleField:BattleField; //战场
		private var dropItemLayer:Sprite; //物品掉落的层
		private var battle:Battle; //整个战场模块
		
		//播放的数据
		private var _isPlaying:Boolean = false; //是否正在播放
		private var currentIndex:int; //当前播放到的动画剪辑或动画剪辑组
		private var clipList:Array; //所有动画剪辑
		private var currentClip:AnimationClip; //当前正在播放的动画剪辑 如果当前正在播放的是动画剪辑组 此项为空
		private var currentClipGroup:Array; //当前正在播放的动画剪辑组 如果当前正在播放的是单个动画剪辑 此项为空
		
		private var curRecord:BattleRecordVo;
		
		public function BattleAnimationManager(battleField:BattleField,dropItemLayer:Sprite,battle:Battle):void
		{
			this.battleField = battleField;
			this.dropItemLayer = dropItemLayer;
			this.battle = battle;
		}
		
		/**
		 * 战斗开始 双方跑步进场
		 * @param	roleList
		 */
		public function enterBattleField(roleList:Vector.<RoleVo>):void
		{
			clipList = new Array;
			var group1:Array = new Array();
			var group2:Array = new Array();
			
			for each(var roleVo:RoleVo in roleList)
			{
				var role:Role = battleField.getRoleByIndex(roleVo.pos);
				var targetPoint:Point = battleField.getGridCoord(roleVo.pos);
				group1.push(new AnimationClip().setMove(role, targetPoint)); //移动角色
				group1.push(new AnimationClip().setRole(role, Role.MOVE, 0)); //播放移动动画
				
				group2.push(new AnimationClip().setRole(role, Role.WAIT, 0, Math.random()*200));
			}
			
			clipList.push(group1);
			clipList.push(group2);
			play();
		}
		
		/**
		 * 战斗结束
		 */
		public function endBattle(roleList:Vector.<RoleVo>, enemyWin:Boolean = false):void
		{
			clipList = new Array;
			var group:Array = new Array;
			for each(var roleVo:RoleVo in roleList)
			{
				if (roleVo.pos > 8 && enemyWin) continue;
				if (roleVo.pos < 9 && !enemyWin) continue;
				if (roleVo.hp <= 0) continue;
				
				var role:Role = battleField.getRoleByIndex(roleVo.pos);
				group.push(new AnimationClip().setRole(role, Role.WIN, 0));
			}
			if (group.length == 0) return;
			clipList.push(group);
			play();
		}
		
		/**
		 * 根据提供的BattleRecordVo拼凑出整个动画过程并播放
		 */
		public function playRecord(record:BattleRecordVo, friend:Role = null):void
		{
			curRecord = record;
			
			var initiator:Role = battleField.getRoleByIndex(curRecord.initiatorIndex); //发起者
			
			var targets:Vector.<Role> = new Vector.<Role>(); //目标
			for (var i:int = 0; i < curRecord.targetInfo.length; i++)
			{
				targets.push(battleField.getRoleByIndex(curRecord.targetInfo[i].index));
			}
			
			clipList = new Array; //重新构建clipList
			var clipGroup:Array; //声明一个辅助数据
			var clip:AnimationClip; //声明一个辅助数据
			
			if (curRecord.type == BattleRecordVo.SKILL_AND_ITEM) //攻击、技能、物品
			{
				var skillAndItemVo:SkillAndItemVo = curRecord.skillAndItemVo;
				var allUnrelatedRoles:Array; //记录所有不相关的角色 即非发起者也非目标的角色
				
				if (skillAndItemVo.cid >3) //普通攻击、防御不飘技能文字
				{
					allUnrelatedRoles = new Array;
					for (i = 0; i < 18; i++)
					{
						if (i == curRecord.initiatorIndex) continue;
						for (var j:int = 0; j < curRecord.targetInfo.length; j++)
							if (i == curRecord.targetInfo[j].index) break;
						if (j < curRecord.targetInfo.length) continue;
						var theRole:Role = battleField.getRoleByIndex(i);
						if (theRole) allUnrelatedRoles.push(theRole);
					}
					
					clipGroup = new Array;
					clipGroup.push(new AnimationClip().setSkillWord(skillAndItemVo.name, battleField));
					clipGroup.push(new AnimationClip().setSceneDark(battleField, allUnrelatedRoles));
					clipList.push(clipGroup);
				}
				
				//按照脚本进行编排
				//脚本是每个技能的静态数据 根据技能的不同有不同的定义 脚本的本质是一个Array 其中内容会被顺序播放
				//其中元素可以是一个AnimationVo 也可以是一个Array<AnimationVo>
				//如果是后者 则其中的AnimationVo会被同时播放 所有的AnimationVo都播放完毕后 该元素才算播放完毕
				
				//开始编排脚本
				parseScript(skillAndItemVo.displayScript, friend==null ? initiator : friend, targets);
				
				if (skillAndItemVo.cid > 3) clipList.push(new AnimationClip().setCancelSceneDark(battleField, allUnrelatedRoles));
				
				//检查目标方是否死亡并播放相应动画
				addDownAnimation(targets);
			}
			else if (curRecord.type == BattleRecordVo.STATUS) //状态
			{	
				//飘字
				if (curRecord.targetInfo[0].hpChange < 0) //受到伤害
				{
					clipGroup = new Array;
					
					//飘红字
					clip = new AnimationClip;
					clip.setPiao(CustomNumber.RED, Math.abs(curRecord.targetInfo[0].hpChange), false, targets[0], AnimationClip.PIAOTYPE_UP);
					
					clipGroup.push(clip);
					clipGroup.push(new AnimationClip().setRole(targets[0], Role.HITTED, 1)); //挨打
					
					clipList.push(clipGroup);
				}
				else if (curRecord.targetInfo[0].hpChange > 0) //受到治疗
				{
					//飘绿字
					clip = new AnimationClip;
					clip.setPiao(CustomNumber.GREEN, curRecord.targetInfo[0].hpChange, false, targets[0], AnimationClip.PIAOTYPE_UP);
					clipList.push(clip);
				}
				
				//检查目标方是否死亡并播放相应动画
				addDownAnimation(targets);
			}
			
			play(); //按照clipList播放动画
		}
		
		/**
		 * 当角色行动完毕后 播放状态消失动画
		 * @param	statusRecord: 当前角色开始行动时对状态进行处理的战斗记录
		 */
		public function playDisappearedStatus(statusRecord:BattleRecordVo):void
		{
			clipList = new Array;
			
			var initiator:Role = battleField.getRoleByIndex(statusRecord.initiatorIndex); //发起者
			
			var targets:Vector.<Role> = new Vector.<Role>(); //目标
			for (var i:int = 0; i < statusRecord.targetInfo.length; i++)
			{
				targets.push(battleField.getRoleByIndex(statusRecord.targetInfo[i].index));
			}
			
			//状态消失时的脚本 数据上状态的回合是减到-1才消失的 但在表现上 减到0就必须消失
			var leftStatusList:Vector.<StatusVo> = statusRecord.targetInfo[0].leftStatusList;
			for (i = 0; i < leftStatusList.length; i++)
			{
				if (leftStatusList[i].remain == 0) parseScript(leftStatusList[i].script, initiator, targets);
			}
			
			play(); //按照clipList播放动画
		}
		
		/**
		 * 播放逃跑动画
		 */
		public function playEscape(roleList:Vector.<RoleVo>, mainRole:Role, success:Boolean):void
		{
			clipList = new Array;
			
			if (success)
			{
				//主角身上暴东西
				var from:Point = new Point(mainRole.x, mainRole.y);
				clipList.push(new AnimationClip().setDropItem(mainRole.vo.items, dropItemLayer, from, null));
			}
			
			//跑
			currentClipGroup = new Array;
			for each(var roleVo:RoleVo in roleList)
			{
				var role:Role = battleField.getRoleByIndex(roleVo.pos);
				currentClipGroup.push(new AnimationClip().setRole(role, Role.ESCAPE, 1, 0, success));
			}
			clipList.push(currentClipGroup);
			
			play();
		}
		
		//主角身上暴东西
		public function playMainRoleDropItems(mainRole:Role):void
		{
			clipList = new Array;
			var from:Point = new Point(mainRole.x, mainRole.y);
			clipList.push(new AnimationClip().setDropItem(mainRole.vo.items, dropItemLayer, from, null));
			
			play();
		}
		
		//---------------------------------------------------------------------------------------------------------
		
		//将一个AnimationVo解析为一个或多个AnimationClip
		//返回结果要么是AnimationClip 要么是Array<AnimationClip>
		//一般情况下一个AnimationVo对应一个AnimationClip
		//如果该animationVo.pos为TARGET 则结果可能有多个AnimationClip 因为每个目标都要有一个AnimationClip
		private function parseAnimationVo(animationVo:AnimationVo,initiator:Role,targets:Vector.<Role>):*
		{
			var animationClip:AnimationClip;
			var result:Array;
			var role:Role;
			var i:int;
			var className:String;
			var targetPoint:Point;
			
			if (animationVo.type == AnimationVo.ROLE) //当前animationVo的类型为角色动画
			{
				if (animationVo.pos == AnimationVo.INITIATOR) //发起者动画
				{
					return new AnimationClip().setRole(initiator, animationVo.label, animationVo.times, animationVo.delay, animationVo.stopAtLabelEnd);
				}
				else if (animationVo.pos == AnimationVo.TARGET) //目标动画
				{
					result = new Array;
					for (i = 0; i < targets.length; i++)
					{
						if (!scanAnimationCondition(animationVo, i)) continue;
						
						role = targets[i];
						animationClip = new AnimationClip();
						animationClip.setRole(role, animationVo.label, animationVo.times, animationVo.delay, animationVo.stopAtLabelEnd);
						result.push(animationClip);
					}
					return result;
				}
			}
			else if (animationVo.type == AnimationVo.MOVE_TO_TARGET) //发起者移动到目标位置
			{
				if (animationVo.movTarget == AnimationVo.NONE) targetPoint = battleField.getGridCoord(curRecord.keyGrid);
				else if (animationVo.movTarget == AnimationVo.BATTLEFIELD_CENTER) targetPoint = battleField.getGridCoord(22);
				else if (animationVo.movTarget == AnimationVo.FRIEND_SKILL_POS) targetPoint = battleField.getGridCoord(BattleField.FRIEND_FIELD_INDEX);
				
				result = new Array;
				result.push(new AnimationClip().setMove(initiator, targetPoint, animationVo.jump, animationVo.duration, animationVo.delay)); //移动角色
				if (!animationVo.jump) result.push(new AnimationClip().setRole(initiator, Role.MOVE, 0)); //播放移动动画
				else result.push(new AnimationClip().setRole(initiator, Role.BACK, 1, 0, true)); //播放跳跃动画
				return result;
			}
			else if (animationVo.type == AnimationVo.JUMP_BACK) //发起者跳回
			{
				if (initiator.vo.pos == BattleField.FRIEND_INDEX) targetPoint = new Point(BattleField.FRIEND_POS_X, BattleField.FRIEND_POS_Y);
				else targetPoint = battleField.getGridCoord(initiator.vo.pos);
				result = new Array;
				result.push(new AnimationClip().setMove(initiator, targetPoint, true, animationVo.duration, animationVo.delay));
				result.push(new AnimationClip().setRole(initiator, Role.BACK, 1, animationVo.delay, true));
				return result;
			}
			else if (animationVo.type == AnimationVo.COVER) //叠加动画
			{
				className = getAnimClassName(initiator, animationVo.className);
				
				if (animationVo.pos == AnimationVo.INITIATOR) //叠在发起者身上
				{
					return new AnimationClip().setCoverAnim_Role(className, initiator, animationVo.times,animationVo.delay);
				}
				else if (animationVo.pos == AnimationVo.TARGET) //叠在每一个目标身上
				{
					result = new Array;
					for each(role in targets)
					{
						if (!scanAnimationCondition(animationVo, i)) continue;
						animationClip = new AnimationClip().setCoverAnim_Role(className, role, animationVo.times,animationVo.delay);
						result.push(animationClip);
					}
					return result;
				}
				else if (animationVo.pos == AnimationVo.KEYGRID) //叠在场景中的某个地块上
				{
					return new AnimationClip().setCoverAnim_BattleField(className, battleField, curRecord.keyGrid, animationVo.times,animationVo.delay);
				}
			}
			else if (animationVo.type == AnimationVo.BOTTOM) //底部动画
			{
				className = getAnimClassName(initiator, animationVo.className);
				
				if (animationVo.pos == AnimationVo.INITIATOR) //垫在发起者身上
				{
					return new AnimationClip().setBottomAnim_Role(className, initiator, animationVo.times,animationVo.delay);
				}
				else if (animationVo.pos == AnimationVo.TARGET) //垫在每一个目标身上
				{
					result = new Array;
					for each(role in targets)
					{
						if (!scanAnimationCondition(animationVo, i)) continue;
						animationClip = new AnimationClip().setBottomAnim_Role(className, role, animationVo.times,animationVo.delay);
						result.push(animationClip);
					}
					return result;
				}
				else if (animationVo.pos == AnimationVo.KEYGRID) //垫在场景中的某个地块上
				{
					return new AnimationClip().setBottomAnim_BattleField(className, battleField, curRecord.keyGrid, animationVo.times,animationVo.delay);
				}
			}
			else if (animationVo.type == AnimationVo.FLYING) //投掷物动画
			{
				result = new Array;
				for each(role in targets)
				{
					var target:Point = new Point(role.x, role.y - Role.ROLE_HEIGHT);
					animationClip = new AnimationClip().setFlying(animationVo.className, battleField, initiator, target, animationVo.jump, animationVo.delay);
					result.push(animationClip);
				}
				return result;
			}
			else if (animationVo.type == AnimationVo.PIAO) //飘字
			{
				//根据字体获得飘的方式
				var piaoType:int;
				switch(animationVo.font)
				{
					case CustomNumber.RED: //红
						piaoType = AnimationClip.PIAOTYPE_DROP; //抛物线淡出
					break;
					case CustomNumber.GREEN: //绿
						piaoType = AnimationClip.PIAOTYPE_UP; //直线淡出
					break;
				}
				
				var font:String = animationVo.font;
				var isCrit:Boolean;
				var value:int;
				
				result = new Array;
				
				for (i = 0; i < targets.length; i ++ )
				{
					if (!scanAnimationCondition(animationVo, i)) continue;
					
					value = animationVo.piaoMp? curRecord.targetInfo[i].mpChange : curRecord.targetInfo[i].hpChange;
					value = Math.round(Math.abs(value) * animationVo.percent);
					isCrit = curRecord.targetInfo[i].critOrDodgeOrResist == BattleRecordVo.CRIT;
					role = targets[i];
					animationClip = new AnimationClip().setPiao(font, value, isCrit, role, piaoType, animationVo.delay);
					result.push(animationClip);
				}
				return result;
			}
			else if (animationVo.type == AnimationVo.FILTER) //滤镜
			{
				if (animationVo.pos == AnimationVo.INITIATOR)
				{
					return new AnimationClip().setRoleFilter(initiator, animationVo.filterType, animationVo.delay);
				}
				else if (animationVo.pos == AnimationVo.TARGET)
				{
					result = new Array;
					for each(role in targets)
					{
						if (!scanAnimationCondition(animationVo, i)) continue;
						animationClip = new AnimationClip().setRoleFilter(role, animationVo.filterType, animationVo.delay);
						result.push(animationClip);
					}
					return result;
				}
			}
			else if (animationVo.type == AnimationVo.REMOVE_ANIM) //移除叠加动画或底部动画
			{
				if (animationVo.pos == AnimationVo.INITIATOR) //发起者身上
				{
					return new AnimationClip().setRemoveAnim_Role(initiator, animationVo.className, animationVo.delay);
				}
				else if (animationVo.pos == AnimationVo.TARGET) //每一个目标身上
				{
					result = new Array;
					for each(role in targets)
					{
						if (!scanAnimationCondition(animationVo, i)) continue;
						animationClip = new AnimationClip().setRemoveAnim_Role(role, animationVo.className, animationVo.delay);
						result.push(animationClip);
					}
					return result;
				}
			}
			else if (animationVo.type == AnimationVo.REMOVE_FILTER) //移除滤镜
			{
				if (animationVo.pos == AnimationVo.INITIATOR)
				{
					return new AnimationClip().setRemoveFilter_Role(initiator, animationVo.filterType, animationVo.delay);
				}
				else if (animationVo.pos == AnimationVo.TARGET)
				{
					result = new Array;
					for each(role in targets)
					{
						if (!scanAnimationCondition(animationVo, i)) continue;
						animationClip = new AnimationClip().setRemoveFilter_Role(role, animationVo.filterType, animationVo.delay);
						result.push(animationClip);
					}
					return result;
				}
			}
			else if (animationVo.type == AnimationVo.SHOCK)
			{
				if (animationVo.pos == AnimationVo.INITIATOR)
				{
					return new AnimationClip().setShock(initiator, animationVo.shockTime, animationVo.delay);
				}
				else if (animationVo.pos == AnimationVo.TARGET)
				{
					result = new Array;
					for each(role in targets)
					{
						animationClip = new AnimationClip().setShock(role, animationVo.shockTime, animationVo.delay);
						result.push(animationClip);
					}
					return result;
				}
				else if (animationVo.pos == AnimationVo.KEYGRID)
				{
					return new AnimationClip().setShock(battle, animationVo.shockTime, animationVo.delay);
				}
			}
			else if (animationVo.type == AnimationVo.SOUND)
			{
				return new AnimationClip().setSound(animationVo.className, animationVo.delay);
			}
			return null;
		}
		
		//根据施法者的站位拼凑出实际的叠加动画的类名
		private function getAnimClassName(initiator:Role, oriClassName:String):String
		{
			var result:String = oriClassName;
			if (initiator.vo.pos > 8) result += "_back";
			return result;
		}
		
		//检测当前动画是否符合条件 以此决定是否播放该动画
		private function scanAnimationCondition(animationVo:AnimationVo, targetIndex:int):Boolean
		{
			var initiator:Role = battleField.getRoleByIndex(curRecord.initiatorIndex);
			var targetVo:BattleRecordTargetVo = curRecord.targetInfo[targetIndex];
			
			var miss:Boolean = targetVo.critOrDodgeOrResist == BattleRecordVo.DODGE || targetVo.critOrDodgeOrResist == BattleRecordVo.RESIST;
			if (!miss && animationVo.condition == AnimationVo.MISS) return false;
			else if(miss && animationVo.condition == AnimationVo.IGNORE_WHEN_MISS) return false;
			
			var sex:int = initiator.vo.sex;
			if (animationVo.condition == AnimationVo.MALE && sex != 1) return false;
			else if (animationVo.condition == AnimationVo.FEMALE && sex != 2) return false;
			
			return true;
		}
		
		//编排脚本
		private function parseScript(script:Array, initiator:Role, targets:Vector.<Role>):void
		{
			if (!script) return;
			
			var parseResult:*;
			var animationVo:AnimationVo;
			
			var clipGroup:Array; //声明一个辅助数据
			var clip:AnimationClip; //声明一个辅助数据
			
			//开始编排脚本
			for (var i:int = 0; i < script.length; i++)
			{
				animationVo = script[i] as AnimationVo;
				if (animationVo) //单个动画VO
				{
					parseResult = parseAnimationVo(animationVo, initiator, targets);
					if (parseResult == null || (parseResult is Array && parseResult.length == 0)) continue;
					clipList.push(parseResult);
					if (animationVo.type == AnimationVo.JUMP_BACK) clipList.push(new AnimationClip().setRole(initiator, Role.WAIT, 1)); //小灶
				}
				else if(script[i] is Array) //动画VO组 即script[i] 是Array<AnimationVo>
				{
					clipGroup = new Array;
					for each(animationVo in script[i])
					{
						parseResult = parseAnimationVo(animationVo, initiator, targets); //获得一个或一组AnimationClip
						
						clip = parseResult as AnimationClip;
						if (clip) clipGroup.push(clip); //单个的AnimationClip
						else if(parseResult is Array && parseResult.length > 0) //一组AnimationClip 从中逐一取出并放入clipGroup中
						{
							for each(clip in parseResult) clipGroup.push(clip);
						}
					}
					if (clipGroup.length > 0) clipList.push(clipGroup);
				}
			}
		}
		
		//判断目标中是否有人死亡 死的话在clipList中插入倒下动画 怪的话还要暴物品并消失
		private function addDownAnimation(targets:Vector.<Role>):void
		{
			var clipGroup:Array = new Array;
			var clipGroup2:Array = new Array;
			var clipGroup3:Array = new Array;
			for each(var role:Role in targets)
			{
				if (role.vo.hp <= 0)
				{
					clipGroup.push(new AnimationClip().setRole(role, Role.DEAD, 1, 0, true));
					if (role.vo.pos < 9) //怪的话
					{
						var from:Point = new Point(role.x, role.y);
						clipGroup2.push(new AnimationClip().setDropItem(role.vo.items, dropItemLayer, from, battleField.mainRole));//掉出物品
						clipGroup3.push(new AnimationClip().setInvisible(role)); //消失
					}
				}
			}
			if (clipGroup.length > 0) clipList.push(clipGroup);
			if (clipGroup2.length > 0) clipList.push(clipGroup2);
			if (clipGroup3.length > 0) clipList.push(clipGroup3);
		}
		
		/**
		 * 播放一串动画剪辑 按this.clipList中的元素依次播放
		 * 元素可以是单个动画剪辑或一组动画剪辑(Array)
		 * 如果元素是个Array 其中内容会被同时播放 等这个Array中的AnimationClip全部播放完毕 此元素才算播放完毕
		 */
		private function play():void
		{
			if (!this.clipList) return;
			
			currentIndex = -1;
			_isPlaying = true;
			battleField.keepSorting(); //开启连续排序
			nextClip();
		}
		
		private function nextClip():void
		{
			currentIndex ++;
			
			if (currentIndex >= clipList.length) //全部播放完毕
			{
				_isPlaying = false;
				battleField.stopSorting();
				battleField.sort();
				dispatchEvent(new Event(Event.COMPLETE));
				return;
			}
			else //下一个
			{
				var clipOrClipGroup:* = clipList[currentIndex];
				if (clipOrClipGroup is AnimationClip) //如果是单个的AnimationClip
				{
					currentClip = clipOrClipGroup;
					currentClipGroup = null;
					currentClip.addEventListener(Event.COMPLETE, checkDone);
					currentClip.start();
				}
				else if(clipOrClipGroup is Array) //动画剪辑组
				{
					currentClip = null;
					currentClipGroup = clipOrClipGroup;
					if (currentClipGroup.length == 0) nextClip();
					else
					{
						for each(var clip:AnimationClip in currentClipGroup)
						{
							clip.addEventListener(Event.COMPLETE, checkDone);
							clip.start();
						}
					}
				}
				else nextClip();
			}
		}
		
		private function checkDone(event:Event):void
		{
			if (currentClip) //如果当前播放的是单个动画剪辑 直接播放下一个元素
			{
				currentClip.removeEventListener(Event.COMPLETE, checkDone);
				nextClip();
			}
			else if(currentClipGroup) //如果当前播放的是一组动画剪辑 检查是否全部完成
			{
				for each(var clip:AnimationClip in currentClipGroup)
				{
					if (clip && !clip.complete) return;
				}
				//经历以上循环而没有跳出 说明已经当前组内的动画剪辑已经全部播放完毕
				for each(clip in currentClipGroup)
				{
					clip.removeEventListener(Event.COMPLETE, checkDone);
				}
				nextClip();
			}
		}
		
		//是否正在播放动画
		public function get isPlaying():Boolean
		{
			return _isPlaying;
		}
		
	}
}