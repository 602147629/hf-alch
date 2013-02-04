package happymagic.battle.model 
{
	import happymagic.battle.controller.BattleRecordTool;
	import happymagic.battle.model.vo.BattleRecordTargetVo;
	import happymagic.battle.model.vo.BattleVo;
	import happymagic.battle.model.vo.BattleRecordVo;
	import happymagic.battle.view.battlefield.Role;
	import happymagic.battle.view.ui.CustomNumber;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.EffectDictionary;
	import happymagic.model.vo.EffectVo;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.SkillAndItemVo;
	import happymagic.model.vo.StatusVo;
	/**
	 * 战斗记录管理器
	 * 分析攻击、技能、逃跑等操作的成功或失败、以及所造成的伤害等数值 并记录之
	 * @author XiaJunJie
	 */
	public class BattleDataManager 
	{
		private var randomList:Array; //随机数列 0.001-1
		private var curRandomIndex:int; //当前使用到的随机数在数列中的索引
		
		private var battleRecordList:Vector.<BattleRecordVo>; //战斗记录列表
		private var _curRecord:BattleRecordVo; //当前正在处理的操作
		private var curTargetVo:BattleRecordTargetVo; //当前正在处理的BattleRecordTargetVo
		
		private var actionQueue:Vector.<RoleVo>; //行动队列
		
		private var battleRecordTool:BattleRecordTool; //将战斗记录转换成操作记录或文字描述的工具
		
		private var curRecordId:int; //当前的操作记录编号
		
		/**
		 * 构造
		 * @param	randomList
		 */
		public function BattleDataManager(randomList:Array, battleVo:BattleVo, actionQueue:Vector.<RoleVo>)
		{
			this.randomList = randomList;
			curRandomIndex = -1;
			
			battleRecordList = new Vector.<BattleRecordVo>();
			battleRecordTool = new BattleRecordTool(battleVo);
			
			this.actionQueue = actionQueue;
			actionQueue.sort(actionQueueSort);
			
			curRecordId = 1;
		}
		
		//掷骰子
		//@param probability: 成功几率 0-1的随机数
		//@return: true:成功 false:失败
		private function dice(probability:Number):Boolean
		{
			curRandomIndex ++;
			if (curRandomIndex >= randomList.length) curRandomIndex = 0;
			
			var randomNum:Number = randomList[curRandomIndex];
			return randomNum <= probability;
		}
		
		/**
		 * 获得最近的操作信息
		 */
		public function get curRecord():BattleRecordVo
		{
			return _curRecord;
		}
		
		/**
		 * 处理攻击、使用技能、使用物品、防御
		 */
		public function dealSkillAndItem(skillAndItemVo:SkillAndItemVo, initiator:RoleVo, targetGrid:int, targets:Vector.<RoleVo>, keyGrid:int, itemId:int = 0, isFriendSkill:Boolean = false):void
		{
			_curRecord = new BattleRecordVo;
			_curRecord.type = BattleRecordVo.SKILL_AND_ITEM;
			
			_curRecord.id = curRecordId;
			curRecordId++;
			
			//记录技能VO
			_curRecord.skillAndItemVo = skillAndItemVo;
			
			//记录物品ID并消耗物品
			_curRecord.itemId = itemId;
			if (itemId != 0) DataManager.getInstance().itemData.removeItemByCid(itemId);
			
			//记录发起者
			_curRecord.initiatorIndex = initiator.pos;
			
			//记录目标地块
			_curRecord.targetGrid = targetGrid;
			
			//消耗法力
			initiator.mp -= skillAndItemVo.needMp;
			if (initiator.mp < 0) initiator.mp = 0;
			
			//初始化目标信息容器
			_curRecord.targetInfo = new Vector.<BattleRecordTargetVo>();
			
			//逐个目标进行处理
			for each(var singleTarget:RoleVo in targets)
			{
				curTargetVo = new BattleRecordTargetVo;
				curTargetVo.index = singleTarget.pos;
				curRecord.targetInfo.push(curTargetVo);
				
				scanDodgeAndCritAndResist(skillAndItemVo, initiator, singleTarget); //闪避暴击检测
				
				if (curTargetVo.critOrDodgeOrResist != BattleRecordVo.DODGE && curTargetVo.critOrDodgeOrResist != BattleRecordVo.RESIST)
				{
					for each(var effectVo:EffectVo in skillAndItemVo.effectList) //逐个效果进行处理
					{
						var typeStr:String = EffectDictionary.strMap[effectVo.type];
						var dealFunc:Function = this["deal" + typeStr + "Effect"]; //处理函数
						
						dealFunc(effectVo, initiator, singleTarget);
					}
					
					//稳定血量
					if (singleTarget.hp < 0) singleTarget.hp = 0;
					else if (singleTarget.hp > singleTarget.maxHp) singleTarget.hp = singleTarget.maxHp;
					
					//稳定法力
					if (singleTarget.mp < 0) singleTarget.mp = 0;
					else if (singleTarget.mp > singleTarget.maxMp) singleTarget.mp = singleTarget.maxMp;
					
					//记录是否死亡
					curTargetVo.down = singleTarget.hp <= 0;
				}
			}
			
			//记录关键位置
			_curRecord.keyGrid = keyGrid;
			
			//记录是否好友技能
			_curRecord.isFriendSkill = isFriendSkill
			
			//记录到战斗记录列表中
			battleRecordList.push(_curRecord);
		}
		
		/**
		 * 处理逃跑
		 * @param	initiator
		 */
		public function dealEscape(initiator:RoleVo,targets:Vector.<RoleVo>):void
		{
			_curRecord = new BattleRecordVo;
			_curRecord.type = BattleRecordVo.ESCAPE;
			
			_curRecord.id = curRecordId;
			curRecordId++;
			
			//记录发起者
			_curRecord.initiatorIndex = initiator.pos;
			
			//记录目标 顺带记录敌方数量和总等级
			_curRecord.targetInfo = new Vector.<BattleRecordTargetVo>();
			var targetNum:Number = 0;
			var totalTargetLevel:Number = 0;
			for each(var roleVo:RoleVo in targets)
			{
				var targetVo:BattleRecordTargetVo = new BattleRecordTargetVo;
				targetVo.index = roleVo.pos;
				_curRecord.targetInfo.push(targetVo);
				
				targetNum ++;
				totalTargetLevel += roleVo.level;
			}
			
			//计算平均等级
			var avTargetLevel:Number = totalTargetLevel / targetNum;
			
			//计算成功率、是否成功 并记录之
			_curRecord.escSuccess = dice(getEscProb(initiator.level, targetNum, avTargetLevel));
			
			//记录到战斗记录列表中
			battleRecordList.push(_curRecord);
		}
		
		//辨识不同的valueType 从而计算出value的值
		private function getValueByType(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):Number
		{
			var value:Number = effectVo.value;
			
			if (effectVo.isAbs) return value; //是绝对值 没有操作
			
			switch(effectVo.type)
			{
				case EffectDictionary.CHANGE_HP:
				case EffectDictionary.CHANGE_MP:
				case EffectDictionary.ADD_CHANGE_HP_STATUS:
				case EffectDictionary.ADD_CHANGE_MP_STATUS:
					if (effectVo.isPhysic) value *= initiator.physicAttack;
					else value *= initiator.magicAttack;
				break;
				case EffectDictionary.ADD_CHANGE_PHYSICATTACK_STATUS:
					value *= target.phyAtk;
				break;
				case EffectDictionary.ADD_CHANGE_PHYSICDEFENCE_STATUS:
					value *= target.phyDef;
				break;
				case EffectDictionary.ADD_CHANGE_MAGICATTACK_STATUS:
					value *= target.magAtk;
				break;
				case EffectDictionary.ADD_CHANGE_MAGICDEFENCE_STATUS:
					value *= target.magDef;
				break;
				case EffectDictionary.ADD_CHANGE_DODGE_STATUS:
					value *= target.baseDodge;
				break;
				case EffectDictionary.ADD_CHANGE_CRIT_STATUS:
					value *= target.baseCrit;
				break;
				case EffectDictionary.REVIVE:
					value *= target.maxHp;
				break;
			}
			
			return value;
		}
		
		//给某目标角色增加一个状态
		private function addStatus(effectVo:EffectVo, initiator:RoleVo, target:RoleVo, crit:Boolean):void
		{
			//如果有同类状态的话先移除
			removeStatus(effectVo, initiator, target);
			
			//新建状态
			var statusVo:StatusVo = new StatusVo();
			statusVo.type = effectVo.type;
			
			var value:Number = getValueByType(effectVo, initiator, target);
			if (crit) value *= critCoe;
			statusVo.value = Math.ceil(value);
			
			statusVo.remain = effectVo.duration;
			statusVo.prop = effectVo.statusProp;
			statusVo.script = effectVo.statusScript;
			
			target.statusList.push(statusVo);
			
			//增加到战斗记录
			curTargetVo.addStatusList.push(statusVo);
			
			//数值处理
			handleStatusData(statusVo, target, 1);
		}
		
		//移除一个目标角色的状态
		private function removeStatus(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			var statusVo:StatusVo;
			for (var i:int = 0; i < target.statusList.length; i++)
			{
				statusVo = target.statusList[i];
				if (statusVo.type == effectVo.type && statusVo.prop == effectVo.statusProp)
				{
					target.statusList.splice(i, 1); //移除
					curTargetVo.removeStatusList.push(statusVo); //增加到战斗记录
					handleStatusData(statusVo, target, -1); //数值处理
					break;
				}
			}
		}
		
		private function removeAllStatus(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			var statusVo:StatusVo;
			for (var i:int = 0; i < target.statusList.length; i++)
			{
				statusVo = target.statusList[i];
				if (statusVo.prop == effectVo.statusProp || effectVo.statusProp == 0)
				{
					target.statusList.splice(i, 1); //移除
					i--;
					curTargetVo.removeStatusList.push(statusVo); //增加到战斗记录
					handleStatusData(statusVo, target, -1); //数值处理
				}
			}
		}
		
		//执行某个角色的某个状态
		//返回 该状态是否还存在
		private function runStatus(statusVo:StatusVo, target:RoleVo):Boolean
		{
			statusVo.remain--;
			if (statusVo.remain < 0) return false; //注意必须是小于0 而非小于等于0
			handleStatusData(statusVo, target);
			return true;
		}
		
		//处理状态的添加或移除或发挥作用时给目标带来的数值变化
		//@param type: 1:添加 -1:移除 0:回合开始时执行
		private function handleStatusData(statusVo:StatusVo, target:RoleVo, type:int = 0):void
		{
			if (statusVo.type == EffectDictionary.ADD_CHANGE_HP_STATUS && type == 0) //持续掉血或加血
			{
				target.hp += statusVo.value;
				curTargetVo.hpChange += statusVo.value;
			}
			else if (statusVo.type == EffectDictionary.ADD_CHANGE_MP_STATUS && type == 0) //持续加魔或减魔
			{
				target.mp += statusVo.value;
				curTargetVo.mpChange += statusVo.value;
			}
			else if (statusVo.type == EffectDictionary.ADD_CHANGE_PHYSICATTACK_STATUS) //改变物攻
			{
				if (type == -1) target.physicAttack -= statusVo.value;
				else target.physicAttack += statusVo.value;
			}
			else if (statusVo.type == EffectDictionary.ADD_CHANGE_PHYSICDEFENCE_STATUS) //改变物防
			{
				if(type == -1) target.physicDefense -= statusVo.value;
				else target.physicDefense += statusVo.value;
			}
			else if (statusVo.type == EffectDictionary.ADD_CHANGE_MAGICATTACK_STATUS) //改变魔攻
			{
				if(type == -1) target.magicAttack -= statusVo.value;
				else target.magicAttack += statusVo.value;
			}
			else if (statusVo.type == EffectDictionary.ADD_CHANGE_MAGICDEFENCE_STATUS) //改变魔防
			{
				if (type == -1) target.magicDefense -= statusVo.value;
				else target.magicDefense += statusVo.value;
			}
			else if (statusVo.type == EffectDictionary.ADD_CHANGE_DODGE_STATUS) //改变闪躲
			{
				if (type == -1) target.dodge -= statusVo.value;
				else target.dodge += statusVo.value;
			}
			else if (statusVo.type == EffectDictionary.ADD_CHANGE_CRIT_STATUS) //改变暴击
			{
				if (type == -1) target.crit -= statusVo.value;
				else target.crit += statusVo.value;
			}
			else if (statusVo.type == EffectDictionary.ADD_PETRIFACTION_STATUS && type == 0) //石化
			{
				curTargetVo.canAction = false;
			}
			else if (statusVo.type == EffectDictionary.ADD_SEAL_STATUS && type == 0) //封印
			{
				curTargetVo.canUseSkill = false;
			}
			//...不断扩展
		}
		
		/**
		 * 处理一个角色身上所有的状态
		 * @param	target
		 */
		public function dealStatus(target:RoleVo):BattleRecordTargetVo
		{
			//新建战斗记录
			curTargetVo = new BattleRecordTargetVo;
			curTargetVo.index = target.pos;
			
			_curRecord = new BattleRecordVo;
			_curRecord.type = BattleRecordVo.STATUS;
			_curRecord.initiatorIndex = target.pos;
			_curRecord.targetInfo = new Vector.<BattleRecordTargetVo>();
			_curRecord.targetInfo.push(curTargetVo);
			
			battleRecordList.push(_curRecord);
			
			//重置角色属性
			target.physicAttack = target.phyAtk;
			target.physicDefense = target.phyDef;
			target.magicAttack = target.magAtk;
			target.magicDefense = target.magDef;
			target.dodge = target.baseDodge;
			target.crit = target.baseCrit;
			
			//处理每个属性
			for (var i:int = 0; i < target.statusList.length; i++)
			{
				if (runStatus(target.statusList[i], target)) //如果该状态仍然存在
				{
					curTargetVo.leftStatusList.push(target.statusList[i]); //在战斗记录中记录之
				}
				else //如果该状态持续时间已到
				{
					var statusVo:StatusVo = target.statusList.splice(i, 1)[0]; //移除之
					curTargetVo.removeStatusList.push(statusVo); //在战斗记录中记录之
					i--;
				}
			}
			
			//稳定血量
			if (target.hp < 0) target.hp = 0;
			else if (target.hp > target.maxHp) target.hp = target.maxHp;
			
			//稳定法力
			if (target.mp < 0) target.mp = 0;
			else if (target.mp > target.maxMp) target.mp = target.maxMp;
			
			//记录是否死亡
			curTargetVo.down = target.hp <= 0;
			
			return curTargetVo;
		}
		
		//闪避暴击检查
		private function scanDodgeAndCritAndResist(skillAndItemVo:SkillAndItemVo, initiator:RoleVo, target:RoleVo):void
		{
			var resistResult:Boolean = false;
			var dodgeResult:Boolean = false;
			
			if (skillAndItemVo.resist > 0) //如果需要计算抵抗
			{
				curTargetVo.resistProb = skillAndItemVo.resist / 100;
				resistResult = dice(curTargetVo.resistProb); //掷骰子 得到是否暴击的抵抗
				curTargetVo.resistRandom = randomList[curRandomIndex];
				curTargetVo.resistRandomIndex = curRandomIndex;
			}
			else if (skillAndItemVo.dodgeAccept > 0) //如果需要计算闪避
			{
				curTargetVo.dodgeProb = getDodgeProb(initiator.dodge, target.dodge * skillAndItemVo.dodgeAccept / 100); //计算闪避几率
				dodgeResult = dice(curTargetVo.dodgeProb); //掷骰子 得到是否闪避的结果
				curTargetVo.dodgeRandomIndex = curRandomIndex;
				curTargetVo.dodgeRandom = randomList[curRandomIndex];
			}
			
			if (resistResult) curTargetVo.critOrDodgeOrResist = BattleRecordVo.RESIST; //抵抗成功记录之
			else if (dodgeResult) curTargetVo.critOrDodgeOrResist = BattleRecordVo.DODGE; //闪避成功 记录之
			else if (skillAndItemVo.critAccept > 0) //否则进行暴击检查
			{
				curTargetVo.critProb = getCritProb(initiator.crit * skillAndItemVo.critAccept / 100, target.crit); //计算暴击几率
				var critResult:Boolean = dice(curTargetVo.critProb); //掷骰子 得到是否暴击的结果
				curTargetVo.critRandomIndex = curRandomIndex;
				curTargetVo.critRandom = randomList[curRandomIndex];
				
				if (critResult) curTargetVo.critOrDodgeOrResist = BattleRecordVo.CRIT; //暴击成功 记录之
				else curTargetVo.critOrDodgeOrResist = BattleRecordVo.NONE; //无闪避无暴击 记录之
			}
			else curTargetVo.critOrDodgeOrResist = BattleRecordVo.NONE; //无闪避无暴击 记录之
		}
		
		//获取整场战斗的操作数据
		public function getOperateInfo():Object
		{
			return battleRecordTool.getOperateInfo(battleRecordList);
		}
		
		//获取整场战斗的描述
		public function getDescription():String
		{
			return battleRecordTool.getDescription(battleRecordList);
		}
		
		//获取整场战斗的日志
		public function getLog():String
		{
			return battleRecordTool.getLog(battleRecordList);
		}
		
		//行动队列排序算法
		private function actionQueueSort(r1:RoleVo, r2:RoleVo):Number
		{
			if (r1.speed > r2.speed) return -1;
			else if (r1.speed < r2.speed) return 1;
			else //如果双方速度相同
			{
				if (r1.pos < r2.pos) return 1; //索引大的先行动
				else return -1;
			}
			return 0;
		}
		
		//////////////////////////////////////////////////////
		//			效果处理函数 可在此扩展新的效果逻辑
		//////////////////////////////////////////////////////
		//减血加血
		public function dealChangeHpEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			//计算并记录伤害
			var value:Number = getValueByType(effectVo, initiator, target);
			if (curTargetVo.critOrDodgeOrResist == BattleRecordVo.CRIT) value *= critAddCoe; //如果暴击 计算绝对值
			
			if (value < 0) //如果是伤害 需综合对方防御力进行计算
			{
				var defense:int = effectVo.isPhysic?target.physicDefense:target.magicDefense;
				
				var jobAdjKey:String = initiator.profession + "-" + target.profession;
				var propAdjKey:String = initiator.prop + "-" + target.prop;
				
				if (DataManager.getInstance().jobAdj[jobAdjKey] == null) throw new Error("未找到职业相克数据: "+ jobAdjKey);
				if (DataManager.getInstance().propAdj[propAdjKey] == null) throw new Error("未找到属性相克数据: " + propAdjKey);
				
				var adj:Number = DataManager.getInstance().jobAdj[jobAdjKey] / 100;
				adj += DataManager.getInstance().propAdj[propAdjKey] / 100;
				
				curTargetVo.hpChange -= getAttackDamage(Math.abs(value), defense, adj);
			}
			else curTargetVo.hpChange += Math.ceil(value);
			
			target.hp += curTargetVo.hpChange; //掉血或加血
		}
		
		//减蓝加蓝
		public function dealChangeMpEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			//计算并记录伤害
			var value:Number = getValueByType(effectVo, initiator, target);
			if (curTargetVo.critOrDodgeOrResist == BattleRecordVo.CRIT) value *= critAddCoe; //如果暴击 计算绝对值
			
			if (value < 0) //如果是伤害 需综合对方防御力进行计算
			{
				curTargetVo.mpChange -= Math.ceil(Math.abs(value));
			}
			else curTargetVo.mpChange += Math.ceil(value);
			
			target.mp += curTargetVo.mpChange; //掉蓝或加蓝
		}
		
		//持续性改变血
		public function dealAddChangeHpStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			addStatus(effectVo, initiator, target, curTargetVo.critOrDodgeOrResist == BattleRecordVo.CRIT);
		}
		
		//持续性改变蓝
		public function dealAddChangeMpStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			addStatus(effectVo, initiator, target, curTargetVo.critOrDodgeOrResist == BattleRecordVo.CRIT);
		}
		
		//改变物攻
		public function dealAddChangePhysicAttackStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			addStatus(effectVo, initiator, target, curTargetVo.critOrDodgeOrResist == BattleRecordVo.CRIT);
		}
		
		//改变物防
		public function dealAddChangePhysicDefenceStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			addStatus(effectVo, initiator, target, curTargetVo.critOrDodgeOrResist == BattleRecordVo.CRIT);
		}
		
		//改变魔攻
		public function dealAddChangeMagicAttackStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			addStatus(effectVo, initiator, target, curTargetVo.critOrDodgeOrResist == BattleRecordVo.CRIT);
		}
		
		//改变魔防
		public function dealAddChangeMagicDefenceStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			addStatus(effectVo, initiator, target, curTargetVo.critOrDodgeOrResist == BattleRecordVo.CRIT);
		}
		
		//改变闪避
		public function dealAddChangeDodgeStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			addStatus(effectVo, initiator, target, curTargetVo.critOrDodgeOrResist == BattleRecordVo.CRIT);
		}
		
		//改变暴击
		public function dealAddChangeCritStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			addStatus(effectVo, initiator, target, curTargetVo.critOrDodgeOrResist == BattleRecordVo.CRIT);
		}
		
		//石化
		public function dealAddPetrifactionStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			addStatus(effectVo, initiator, target, curTargetVo.critOrDodgeOrResist == BattleRecordVo.CRIT);
		}
		
		//复活
		public function dealReviveEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			//计算并记录复活后的血量
			var value:Number = getValueByType(effectVo, initiator, target);
			if (curTargetVo.critOrDodgeOrResist == BattleRecordVo.CRIT) value *= critAddCoe; //如果暴击 计算绝对值
			
			curTargetVo.hpChange += Math.ceil(value);
			target.hp += Math.ceil(value);
			
			//将此目标加入到行动列表中去
			var list:Vector.<RoleVo> = actionQueue.concat();
			list.push(target);
			list.sort(actionQueueSort);
			for (var i:int = 0; i < list.length; i++)
			{
				var curRolePos:int = actionQueue[actionQueue.length - 1].pos; //此时curRole已经在队列尾部
				if (list[0].pos != curRolePos) list.push(list.shift());
				else break;
			}
			list.push(list.shift()); //将curRole放到队列尾部
			var index:int = list.indexOf(target);
			actionQueue.splice(index, 0, target);
		}
		
		//解除持续性改变血
		public function dealRemoveChangeHpStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			var effectVo2:EffectVo = new EffectVo;
			effectVo2.type = EffectDictionary.ADD_CHANGE_HP_STATUS;
			effectVo2.statusProp = effectVo.statusProp;
			removeStatus(effectVo2, initiator, target);
		}
		
		//解除改变物防效果
		public function dealRemoveChangePhysicDefenceStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			var effectVo2:EffectVo = new EffectVo;
			effectVo2.type = EffectDictionary.ADD_CHANGE_PHYSICDEFENCE_STATUS;
			effectVo2.statusProp = effectVo.statusProp;
			removeStatus(effectVo2, initiator, target);
		}
		
		//解除石化
		public function dealRemovePetrifactionStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			var effectVo2:EffectVo = new EffectVo;
			effectVo2.type = EffectDictionary.ADD_PETRIFACTION_STATUS;
			effectVo2.statusProp = effectVo.statusProp;
			removeStatus(effectVo2, initiator, target);
		}
		
		//解除所有效果
		public function dealRemoveAllStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			removeAllStatus(effectVo, initiator, target);
		}
		
		//封印
		public function dealAddSealStatusEffect(effectVo:EffectVo, initiator:RoleVo, target:RoleVo):void
		{
			addStatus(effectVo, initiator, target, curTargetVo.critOrDodgeOrResist == BattleRecordVo.CRIT);
		}
		
		//静态-----------------------------------------
		
		//伤害公式
		//伤害量=物理攻击力*[物理攻击力/（物理攻击+物理防御）*系数]
		private static const attackCoe:Number = 1; //普通攻击系数
		private static function getAttackDamage(attack:Number, defense:Number, adj:Number):int
		{
			return Math.ceil(attack * (attack / (attack + defense) * attackCoe) * (1 + adj));
		}
		
		//闪避公式
		//闪避率=对方闪避*[对方闪避/（对方闪避+我方闪避）*系数]
		private static const dodgeCoe:Number = 1; //闪避系数
		private static function getDodgeProb(attackerDodge:Number, targetDodge:Number):Number
		{
			return targetDodge / 1000 * (targetDodge / (targetDodge + attackerDodge) * dodgeCoe);
		}
		
		//暴击公式
		//暴击率=我方暴击*[我方暴击/（我方暴击+对方暴击）*系数]
		private static const critCoe:Number = 1; //暴击系数
		private static function getCritProb(attackerCrit:Number, targetCirt:Number):Number
		{
			return attackerCrit / 1000 * (attackerCrit / (attackerCrit + targetCirt) * critCoe);
		}
		
		//暴击加成系数
		private static const critAddCoe:Number = 1.5;
		
		//逃跑公式 逃跑率 = 0.9 / ( 对方个数 + Math.max(怪物的平均等级-我方等级*0.5,0) )
		private static function getEscProb(initiatorLevel:Number, targetsNum:Number, avTargetsLevel:Number):Number
		{
			return 0.9 / (targetsNum + Math.max(avTargetsLevel - initiatorLevel / 2, 0));
		}
		
	}
}