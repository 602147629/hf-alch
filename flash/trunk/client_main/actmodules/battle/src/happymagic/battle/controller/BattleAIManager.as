package happymagic.battle.controller 
{
	import happymagic.battle.model.BattleDataManager;
	import happymagic.battle.model.Skills;
	import happymagic.battle.view.battlefield.BattleField;
	import happymagic.battle.view.battlefield.Role;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ai.AIActionVo;
	import happymagic.model.vo.ai.AIConditionVo;
	import happymagic.model.vo.ai.AIFormationVo;
	import happymagic.model.vo.ai.AIRoleVo;
	import happymagic.model.vo.ai.AIScriptVo;
	import happymagic.model.vo.EffectDictionary;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.SkillAndItemVo;
	import happymagic.model.vo.StatusVo;
	/**
	 * AI管理器
	 * @author XiaJunJie
	 */
	public class BattleAIManager 
	{
		private var battleField:BattleField;
		
		//辅助
		private var curRoleVo:RoleVo;
		private var conditionResult:Array; //保存特定角色或站位的中间结果 如果脚本的目标引用条件中的某一项的话就不必再找一遍了
		private var allAliveRoles:Vector.<RoleVo>; //所有活着的角色 其实是Battle.actionQueue 因此不能直接删改 只能复制后使用
		
		//行动结果
		public var actionType:int; //参考AIActionVo中的静态常量
		public var skillAndItemVo:SkillAndItemVo; //使用的技能或物品
		public var targets:Vector.<RoleVo>; //目标
		public var keyGrid:int; //关键位置
		public var targetGrid:int; //目标位置
		//TODO...说话脚本
		
		public function BattleAIManager(battleField:BattleField,allAliveRoles:Vector.<RoleVo>) 
		{
			this.battleField = battleField;
			this.allAliveRoles = allAliveRoles;
			
			targets = new Vector.<RoleVo>();
		}
		
		/**
		 * 处理脚本
		 * @return : true表示有行动 具体参数查看本对象中的 actionType、skillAndItemVo、targets等属性; false表示没有行动
		 */
		public function deal(curRoleVo:RoleVo):Boolean
		{
			this.curRoleVo = curRoleVo;
			
			if (curRoleVo.aiScript == null || curRoleVo.aiScript.length==0) initRoleAIScript(curRoleVo);
			
			var roleVo:RoleVo;
			var role:Role;
			var formationInfo:Array;
			
			var canUseSkill:Boolean = true; //是否被封印
			for each(var statusVo:StatusVo in curRoleVo.statusList)
			{
				if (statusVo.type == EffectDictionary.ADD_SEAL_STATUS)
				{
					canUseSkill = false;
					break;
				}
			}
			
			//遍历所有脚本 对于单个脚本 只有其中每个条件都成立 才能开始执行其指定的动作 否则的话此脚本中的动作不可执行 继续分析下一脚本
			for each(var script:AIScriptVo in curRoleVo.aiScript)
			{
				if (Math.random() > script.prob) continue; //几率
				if (script.action.skillAndItemVo && script.action.skillAndItemVo.needMp > curRoleVo.mp) continue; //没有足够的法力
				if (!canUseSkill) //如果无法使用技能
				{
					var skillId:int =  script.action.skillAndItemCid;
					if (skillId != Skills.CLOSEATTACK_CID && skillId != Skills.FARATTACK_CID) continue; //不是普通攻击就无法执行
				}
				
				conditionResult = new Array; //保存条件中特定角色或站位的中间结果 如果脚本的目标引用条件中的某一项的话就不必再找一遍了
				var conditionAllOK:Boolean = true; //记录所有条件是否都成立
				
				var closeAttack:Boolean = false; //是否近战攻击或近战技能
				if (script.action.type == AIActionVo.ATTACK && curRoleVo.profession != 2) closeAttack = true; //非弓箭手发动普通攻击
				else if (script.action.skillAndItemVo && script.action.skillAndItemVo.range == SkillAndItemVo.CLOSE) closeAttack = true; //近战技能
				if (closeAttack && battleField.isFrontOccupied(curRoleVo.pos)) continue; //前方有人挡着的话无法发动
				
				for (var j:int = 0; j < script.conditions.length; j++) //遍历单个脚本中的所有条件
				{
					var condition:AIConditionVo = script.conditions[j]; //获取该条件
					if (condition.type == AIConditionVo.ROLE) //当前条件为角色条件 即要求场景中有特定的角色
					{
						roleVo = getRoleByAIRoleVo(condition.role,closeAttack); //查找场景中是否有指定的角色
						
						if (roleVo) conditionResult.push(roleVo); //如果有符合条件的角色 保存结果
						else //否则的话 停止继续遍历剩下的条件
						{
							conditionAllOK = false;
							break;
						}
					}
					else if(condition.type == AIConditionVo.FORMATION) //当前条件为站位条件 即要求场景中某一阵营的角色站成特定站位(行、列、十字...)
					{
						formationInfo = getFormationByAIFormationVo(condition.formation, closeAttack); //查找场景中是否有这样的站位
						
						if (formationInfo) conditionResult.push(formationInfo); //如果有符合条件的站位 保存结果
						else //否则的话 停止继续遍历剩下的条件
						{
							conditionAllOK = false;
							break;
						}
					}
					else //无法辨识该条件 停止继续遍历剩下的条件
					{
						conditionAllOK = false;
						break;
					}
				}
				
				if (conditionAllOK) //所有条件都符合 开始行动
				{
					if(script.action.type == AIActionVo.ATTACK) //行动类型为 攻击
					{
						roleVo = getRoleByAIRoleVo(script.target.role,closeAttack); //获取目标
						if (roleVo) //设置动作
						{
							actionType = AIActionVo.SKILL_AND_ITEM;
							skillAndItemVo = curRoleVo.profession == 2 ? Skills.farAttackVo : Skills.closeAttackVo;
							targets.splice(0, targets.length);
							targets.push(roleVo);
							targetGrid = roleVo.pos;
							keyGrid = battleField.getFrontGridIndex(targetGrid);
							
							return true;
						}
					}
					else if (script.action.type == AIActionVo.SKILL_AND_ITEM) //行动类型为 使用物品或技能
					{
						skillAndItemVo = script.action.skillAndItemVo;
						if (skillAndItemVo.area == SkillAndItemVo.SINGLE) //目标是单体
						{
							roleVo = getRoleByAIRoleVo(script.target.role); //获取目标
							if (roleVo) //设置动作
							{
								actionType = AIActionVo.SKILL_AND_ITEM;
								targets.splice(0, targets.length);
								targets.push(roleVo);
								targetGrid = roleVo.pos;
								keyGrid = battleField.getFrontGridIndex(targetGrid);
								
								return true;
							}
						}
						else //目标是行、列、十字或全体
						{
							//检查AI脚本中的站位信息和所写的技能的目标范围是否相符 如果不符 属于策划填错 本动作不会被执行
							var aiFormationVo:AIFormationVo = script.target.formation;
							if (aiFormationVo.formationInCondition != -1) //如果目标的站位(AIFormationVo)是对条件的站位的引用 则获取条件中的站位
							{
								aiFormationVo = script.conditions[aiFormationVo.formationInCondition].formation;
							}
							if (aiFormationVo.side != skillAndItemVo.target || aiFormationVo.shape != skillAndItemVo.area) continue;
							
							formationInfo = getFormationByAIFormationVo(script.target.formation, closeAttack); //获取目标
							if (formationInfo) //设置动作
							{
								actionType = AIActionVo.SKILL_AND_ITEM;
								targetGrid = formationInfo.shift(); //行中心/列排头/十字中心/全体中心
								
								//如果是行或列 取行中心或列排头的前面一格
								if (aiFormationVo.shape == AIFormationVo.COL || aiFormationVo.shape == AIFormationVo.ROW)
								{
									keyGrid = battleField.getFrontGridIndex(targetGrid);
								}
								else keyGrid = battleField.getFrontGridIndex(battleField.getFrontGridIndex(targetGrid)); //否则 直接取 十字中心/全体中心 前的两格 作为关键点
								
								targets.splice(0, targets.length);
								for (var i:int = 0; i < formationInfo.length; i++)
								{
									role = formationInfo[i];
									targets.push(role.vo);
								}
								targets.sort(sortPos);
								
								return true;
							}
						}
					}
				}
			}
			return false;
		}
		
		private function sortPos(vo1:RoleVo,vo2:RoleVo):Number
		{
			if (vo1.pos < vo2.pos) return -1;
			else if (vo1.pos > vo2.pos) return 1;
			return 0;
		}
		
		//根据条件获得合适的角色
		//@param needCloseAttakable : 目标是否必须要可以被近战攻击到
		private function getRoleByAIRoleVo(aiRoleVo:AIRoleVo,needCloseAttakable:Boolean = false):RoleVo
		{
			if (aiRoleVo.roleInCondition != -1)
			{
				return conditionResult[aiRoleVo.roleInCondition] as RoleVo;
			}
			
			var roles:Vector.<RoleVo>;
			var i:int;
			
			if (aiRoleVo.type != -1) //对角色类型有指定
			{
				if (aiRoleVo.type == AIRoleVo.SELF) //角色是自己
				{
					roles = new Vector.<RoleVo>();
					roles.push(curRoleVo);
				}
				else 
				{
					roles = allAliveRoles.concat();
					if (aiRoleVo.type == AIRoleVo.ENEMY) //角色是敌人
					{
						for (i = 0; i < roles.length; i++) //过滤掉我方角色
						{
							if (isEnemy(curRoleVo) == isEnemy(roles[i]))
							{
								roles.splice(i, 1);
								i--;
							}
						}
					}
					else if (aiRoleVo.type == AIRoleVo.FRIEND) //角色是友方
					{
						for (i = 0; i < roles.length; i++) //过滤掉敌方角色
						{
							if (isEnemy(curRoleVo) != isEnemy(roles[i]))
							{
								roles.splice(i, 1);
								i--;
							}
						}
					}
				}
			}
			if (aiRoleVo.profession != -1) //对角色职业有指定
			{
				for (i = 0; i < roles.length; i++) //过滤掉所有非指定职业的角色
				{
					if (roles[i].profession != aiRoleVo.profession)
					{
						roles.splice(i, 1);
						i--;
					}
				}
			}
			if (aiRoleVo.hpLessThan >= 0) //角色的血必须少于等于指定值
			{
				for (i = 0; i < roles.length; i++) //过滤掉所有血多于指定值的角色
				{
					if (aiRoleVo.hpLessThan >= 1 && (roles[i].hp > aiRoleVo.hpLessThan)) //绝对值
					{
						roles.splice(i, 1);
						i--;
					}
					else if (aiRoleVo.hpLessThan < 1 && (roles[i].hp > aiRoleVo.hpLessThan * roles[i].maxHp)) //百分比
					{
						roles.splice(i, 1);
						i--;
					}
				}
			}
			if (aiRoleVo.hpMoreThan >= 0) //角色的血必须多于等于指定值
			{
				for (i = 0; i < roles.length; i++) //过滤掉所有血少于指定值的角色
				{
					if (aiRoleVo.hpMoreThan >= 1 && (roles[i].hp < aiRoleVo.hpMoreThan)) //绝对值
					{
						roles.splice(i, 1);
						i--;
					}
					else if (aiRoleVo.hpMoreThan < 1 && (roles[i].hp < aiRoleVo.hpMoreThan * roles[i].maxHp)) //百分比
					{
						roles.splice(i, 1);
						i--;
					}
				}
			}
			if (aiRoleVo.statusList != null) //角色必须有或没有指定的状态 其中元素为数组 格式为[有/无,效果类型,正面或负面]-[1/0,4/5/6...,1/2]
			{
				for (i = 0; i < roles.length; i++) //过滤掉所有不符合条件的角色
				{
					for each (var st:Array in aiRoleVo.statusList)
					{
						var hasStatus:Boolean = false;
						for (var j:int = 0; j < roles[i].statusList.length; j++)
						{
							var statusVo:StatusVo = roles[i].statusList[j];
							if (statusVo.type == st[1] && statusVo.prop == st[2])
							{
								hasStatus = true;
								break;
							}
						}
						if ((st[0] == 0 && hasStatus) || (st[0] != 0 && !hasStatus))
						{
							roles.splice(i, 1);
							i--;
						}
					}
				}
			}
			
			if (needCloseAttakable) //角色必须可以被近身攻击到
			{
				for (i = 0; i < roles.length; i++)
				{
					if (battleField.isFrontOccupied(roles[i].pos))
					{
						roles.splice(i, 1);
						i--;
					}
				}
			}
			
			if (roles.length > 0) //如果还有符合条件的角色
			{
				return roles[getRandomInt(roles.length - 1)]; //从中随机抽取一个返回
			}
			return null;
		}
		
		//根据条件获得合适的站位 返回数组的第一位是目标点 后几位是被包在这个范围内的角色(Role)
		//@param needCloseAttackable : 目标是否必须要可以被近战攻击到
		private function getFormationByAIFormationVo(aiFormationVo:AIFormationVo, needCloseAttackable:Boolean = false):Array
		{
			var formationInfo:Array;
			var gridIndex:int;
			
			if (aiFormationVo.formationInCondition != -1)
			{
				return conditionResult[aiFormationVo.formationInCondition] as Array;
			}
			
			var enemySide:Boolean = aiFormationVo.side == AIFormationVo.ENEMY; //当前角色角度的 己方/敌方
			if (isEnemy(curRoleVo)) enemySide = !enemySide; //整个战场角度的 己方/敌方
			
			if (aiFormationVo.shape == AIFormationVo.ROW) //行
			{
				return battleField.getRowFormation(enemySide, aiFormationVo.threshold, needCloseAttackable);
			}
			else if (aiFormationVo.shape == AIFormationVo.COL) //列
			{
				return battleField.getColFormation(enemySide, aiFormationVo.threshold);
			}
			else if (aiFormationVo.shape == AIFormationVo.CROSS)
			{
				return battleField.getCrossFormation(enemySide, aiFormationVo.threshold, needCloseAttackable);
			}
			else if (aiFormationVo.shape == AIFormationVo.ALL)
			{
				return battleField.getAllFormation(enemySide, aiFormationVo.threshold);
			}
			
			return null;
		}
		
		//获得一个 0-max 之间的随机整型数
		private function getRandomInt(max:int):int
		{
			return Math.floor(Math.random() * (max + 1));
		}
		
		//判断某角色是否敌方角色
		private function isEnemy(roleVo:RoleVo):Boolean
		{
			return roleVo.pos < 9;
		}
		
		//根据角色的技能拼凑出角色的AI脚本
		public static function initRoleAIScript(roleVo:RoleVo):void
		{
			roleVo.aiScript = new Vector.<AIScriptVo>();
			
			for (var i:int = 0 ; i < roleVo.skills.length; i++)
			{
				if (int(roleVo.skills[i]) != 0 && int(roleVo.skills[i]) != -1)
				{
					var skillAndItemVo:SkillAndItemVo = DataManager.getInstance().getSkillAndItemVo(int(roleVo.skills[i]));
					if(skillAndItemVo && skillAndItemVo.aiScript) roleVo.aiScript.push(skillAndItemVo.aiScript);
				}
			}
			//普通攻击
			var aiScriptVo:AIScriptVo = new AIScriptVo;
			aiScriptVo.setData( { "conditions":[ { "type":2, "role": { "type":3 }} ], "action": { "type":2 }, "target": { "role": { "roleInCondition":0 }}} );
			roleVo.aiScript.push(aiScriptVo);
		}
		
	}
}