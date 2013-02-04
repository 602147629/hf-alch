package happymagic.battle.controller 
{
	import happymagic.battle.model.vo.BattleRecordTargetVo;
	import happymagic.battle.model.vo.BattleRecordVo;
	import happymagic.battle.model.vo.BattleVo;
	import happymagic.model.vo.EffectDictionary;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.StatusVo;
	/**
	 * 可以将战斗记录转换成通讯协议数据 或者文字描述
	 * @author XiaJunJie
	 */
	public class BattleRecordTool 
	{
		private var battleVo:BattleVo;
		
		public function BattleRecordTool(battleVo:BattleVo)
		{
			this.battleVo = battleVo;
		}
		
		//获得整场战斗的操作信息
		public function getOperateInfo(recordList:Vector.<BattleRecordVo>):Object
		{
			var result:Object = new Object;
			result["v"] = 1.0;
			result["id"] = battleVo.id;
			result["type"] = battleVo.result;
			
			var arr:Array = new Array;
			for (var i:int = 0; i < recordList.length; i++)
			{
				var infoArr:Array = turnRecordToOperateData(recordList[i]);
				if(infoArr) arr.push(infoArr);
			}
			result["data"] = arr;
			
			return result;
		}
		
		//将单条战斗记录转换成操作信息
		private function turnRecordToOperateData(record:BattleRecordVo):Array
		{
			var result:Array = new Array;
			
			if (record.type == BattleRecordVo.SKILL_AND_ITEM) 
			{
				if (record.skillAndItemVo.cid == 3) //防御
				{
					result.push("x");
					result.push(record.initiatorIndex);
				}
				else //攻击、技能、物品
				{
					var type:int; //操作类型
					if (record.skillAndItemVo.cid == 1 || record.skillAndItemVo.cid == 2) type = 1; //普通攻击
					else if (record.itemId == 0) type = 2; //技能
					else type = 3; //物品
					
					result.push(record.isFriendSkill ? "#" : "-");
					result.push(record.initiatorIndex); //发起方索引
					result.push(record.targetGrid); //目标位置索引
					result.push(type); //操作类型
					result.push(record.skillAndItemVo.cid); //使用的物品或技能的CID
					result.push(0);
				}
			}
			else if (record.type == BattleRecordVo.ESCAPE) //逃跑
			{
				result.push("!");
				result.push(record.initiatorIndex);
			}
			
			if (result.length > 0) return result;
			return null;
		}
		
		//######################################################################################################
		
		//获得整场战斗的文字描述
		public function getDescription(recordList:Vector.<BattleRecordVo>):String
		{
			var result:String = "";
			for (var i:int = 0; i < recordList.length; i++)
			{
				result += turnRecordToDescription(recordList[i]) + "\n";
			}
			
			result += "战斗结束:";
			if (battleVo.result == BattleVo.WIN) result += "胜利";
			else if (battleVo.result == BattleVo.LOST) result += "失败";
			else if (battleVo.result == BattleVo.ESC) result += "逃跑";
			result += ";";
			
			return result;
		}
		
		//将单条战斗记录转换成文字描述
		private function turnRecordToDescription(record:BattleRecordVo):String
		{
			var result:String = "";
			
			var statusVo:StatusVo;
			var targetVo:BattleRecordTargetVo;
			var targetName:String;
			
			if (record.type == BattleRecordVo.SKILL_AND_ITEM)
			{
				result += getRoleName(record.initiatorIndex) + " 使用 ";
				result += record.skillAndItemVo.name + ", ";
				
				for each(targetVo in record.targetInfo)
				{
					targetName = getRoleName(targetVo.index);
					
					if (targetVo.critOrDodgeOrResist == BattleRecordVo.DODGE) result += "被 " + targetName + " 躲闪了; ";
					else if (targetVo.critOrDodgeOrResist == BattleRecordVo.RESIST) result += "被 " + targetName + " 抵抗了; ";
					else
					{
						if (targetVo.critOrDodgeOrResist == BattleRecordVo.CRIT) result += "发生暴击 ";
						
						if (targetVo.hpChange > 0) result += "治疗了 " + targetName + " " + targetVo.hpChange + "点生命; ";
						else if (targetVo.hpChange < 0) result += "对 " + targetName + " 造成了" + Math.abs(targetVo.hpChange) + "点伤害; ";
						
						if (targetVo.mpChange > 0) result += "恢复了 " + targetName + " " + targetVo.mpChange + "点法力; ";
						else if (targetVo.mpChange < 0) result += "消耗了 " + targetName + " " + Math.abs(targetVo.mpChange) + "点法力; ";
						
						if (targetVo.addStatusList.length > 0)
						{
							result += "给" + targetName + "施加了";
							for each(statusVo in targetVo.addStatusList)
							{
								result += "状态" + getStatusDescription(statusVo) + ",";
							}
							result.substr(0, result.length - 1);
							result += "; ";
						}
						if (targetVo.removeStatusList.length > 0)
						{
							result += "消除了" + targetName;
							for each(statusVo in targetVo.removeStatusList)
							{
								result += "状态" + getStatusDescription(statusVo) + ",";
							}
							result.substr(0, result.length - 1);
							result += "; ";
						}
						
						if (targetVo.down) result += targetName + "死亡了; ";
					}
					
					result += "[";
					if (targetVo.resistRandomIndex != -1)
					{
						result += "抵抗(几率:" + Math.round(targetVo.resistProb * 100);
						result += ",随机数:" + Math.round(targetVo.resistRandom * 100) + "[" + targetVo.resistRandomIndex + "])";
					}
					if (targetVo.dodgeRandomIndex != -1)
					{
						result += "闪避(几率:" + Math.round(targetVo.dodgeProb * 100);
						result += ",随机数:" + Math.round(targetVo.dodgeRandom * 100) + "[" + targetVo.dodgeRandomIndex + "])";
					}
					if (targetVo.critRandomIndex != -1)
					{
						result += " | 暴击(几率:" + Math.round(targetVo.critProb * 100);
						result += ",随机数:" + Math.round(targetVo.critRandom * 100) + "[" + targetVo.critRandomIndex + "])";
					}
					result += "]";
				}
			}
			else if (record.type == BattleRecordVo.ESCAPE)
			{
				targetName = getRoleName(record.initiatorIndex);
				result += targetName + " 尝试逃跑,";
				if (record.escSuccess) result += "成功; ";
				else result += "失败;";
			}
			else if (record.type == BattleRecordVo.STATUS)
			{
				var subResult:String = "";
				targetVo = record.targetInfo[0];
				targetName = getRoleName(targetVo.index);
				
				if (targetVo.hpChange > 0) subResult += "治疗了 " + targetName + " " + targetVo.hpChange + "点生命; ";
				else if (targetVo.hpChange < 0) subResult += "对 " + targetName + " 造成了" + Math.abs(targetVo.hpChange) + "点伤害; ";
				
				if (targetVo.mpChange > 0) subResult += "恢复了 " + targetName + " " + targetVo.mpChange + "点法力; ";
				else if (targetVo.mpChange < 0) subResult += "消耗了 " + targetName + " " + Math.abs(targetVo.mpChange) + "点法力; ";
				
				if (targetVo.removeStatusList.length > 0)
				{
					for each(statusVo in targetVo.removeStatusList)
					{
						subResult += "状态" + getStatusDescription(statusVo) + "消失了,";
					}
					subResult.substr(0, subResult.length - 1);
					subResult += "; ";
				}
				if (targetVo.leftStatusList.length > 0)
				{
					subResult += targetName + " 身上还剩余";
					for each(statusVo in targetVo.leftStatusList)
					{
						subResult += "状态" + getStatusDescription(statusVo) + ",";
					}
					subResult.substr(0, subResult.length - 1);
					subResult += "; ";
				}
				
				if (targetVo.down) subResult += targetName + "死亡了; ";
				if(subResult!="") result = targetName + " 身上的状态发生作用, " + subResult;
			}
			
			return result;
		}
		
		/**
		 * 获得角色的名字
		 * @param	index
		 * @return
		 */
		private function getRoleName(index:int):String
		{
			for each(var roleVo:RoleVo in battleVo.roleList)
			{
				if (roleVo.pos == index) return roleVo.name + "(" + roleVo.pos + ")";
			}
			return "未知("+index+")";
		}
		
		/**
		 * 获得状态的描述
		 * @param	statusVo
		 * @return
		 */
		private function getStatusDescription(statusVo:StatusVo):String
		{
			var result:String = "(";
			
			if (statusVo.type == EffectDictionary.ADD_CHANGE_HP_STATUS)
			{
				if (statusVo.value > 0) result += "每回合恢复" + statusVo.value + "点生命";
				else result += "每回合造成" + statusVo.value + "点伤害 ";	
			}
			else if (statusVo.type == EffectDictionary.ADD_CHANGE_MP_STATUS)
			{
				if (statusVo.value > 0) result += "每回合恢复" + statusVo.value + "点法力";
				else result += "每回合减少" + statusVo.value + "点法力 ";
			}
			else if (statusVo.type == EffectDictionary.ADD_PETRIFACTION_STATUS)
			{
				result += "石化";
			}
			else if (statusVo.type == EffectDictionary.ADD_CONFUSION_STATUS)
			{
				result += "混乱";
			}
			else if (statusVo.type == EffectDictionary.ADD_SLEEP_STATUS)
			{
				result += "睡眠";
			}
			else 
			{
				if (statusVo.value > 0) result += "增加";
				else result += "减少";
				
				if (statusVo.type == EffectDictionary.ADD_CHANGE_PHYSICATTACK_STATUS)
				{
					result += statusVo.value + "点物攻";
				}
				else if (statusVo.type == EffectDictionary.ADD_CHANGE_PHYSICDEFENCE_STATUS)
				{
					result += statusVo.value + "点物防";
				}
				else if (statusVo.type == EffectDictionary.ADD_CHANGE_MAGICATTACK_STATUS)
				{
					result += statusVo.value + "点魔攻";
				}
				else if (statusVo.type == EffectDictionary.ADD_CHANGE_MAGICDEFENCE_STATUS)
				{
					result += statusVo.value + "点魔防";
				}
				else if (statusVo.type == EffectDictionary.ADD_CHANGE_CRIT_STATUS)
				{
					result += statusVo.value + "点暴击";
				}
				else if (statusVo.type == EffectDictionary.ADD_CHANGE_DODGE_STATUS)
				{
					result += statusVo.value + "点闪躲";
				}
			}
			
			if (statusVo.remain >= 0) result += " 剩余" + statusVo.remain + "回合";
			result += ")";
			
			return result;
		}
		
		//############################################################################################################
		
		//获得整场战斗的战斗日志
		public function getLog(recordList:Vector.<BattleRecordVo>):String
		{
			var result:String = "";
			for (var i:int = 0; i < recordList.length; i++)
			{
				var recordLog:String = turnRecordToLog(recordList[i]);
				if (recordLog) result += "Log" + recordList[i].id + " " + recordLog + "\n";
			}
			
			result += "BattleEnd:";
			if (battleVo.result == BattleVo.WIN) result += "win";
			else if (battleVo.result == BattleVo.LOST) result += "lost";
			else if (battleVo.result == BattleVo.ESC) result += "esc";
			
			return result;
		}
		
		//将单条战斗记录转换成日志
		private function turnRecordToLog(record:BattleRecordVo):String
		{
			var result:String = "";
			
			var statusVo:StatusVo;
			var targetVo:BattleRecordTargetVo;
			var targetName:String;
			
			if (record.type == BattleRecordVo.SKILL_AND_ITEM)
			{
				result += "(" + record.initiatorIndex + ")" + "Use_";
				result += record.skillAndItemVo.cid + ", ";
				
				for each(targetVo in record.targetInfo)
				{
					targetName = "(" + targetVo.index + ")";
					result += "->" + targetName;
					
					if (targetVo.critOrDodgeOrResist == BattleRecordVo.RESIST) result += "Resist";
					else if (targetVo.critOrDodgeOrResist == BattleRecordVo.DODGE) result += "Miss";
					else
					{
						if (targetVo.critOrDodgeOrResist == BattleRecordVo.CRIT) result += "Crit";
						
						if (targetVo.hpChange > 0) result += "+" + targetVo.hpChange + "Hp;";
						else if (targetVo.hpChange < 0) result += "-" + Math.abs(targetVo.hpChange) + "Hp;";
						
						if (targetVo.mpChange > 0) result += "+" + targetVo.mpChange + "Mp;";
						else if (targetVo.mpChange < 0) result += "-" + Math.abs(targetVo.mpChange) + "Mp;";
						
						if (targetVo.addStatusList.length > 0)
						{
							for each(statusVo in targetVo.addStatusList) result += "+" + getStatusInLog(statusVo)+";";
						}
						if (targetVo.removeStatusList.length > 0)
						{
							for each(statusVo in targetVo.removeStatusList) result += "-" + getStatusInLog(statusVo)+";";
						}
						
						if (targetVo.down) result += "->Down";
					}
					
					if (targetVo.resistRandomIndex != -1)
					{
						result += "|R:" + Math.round(targetVo.resistProb * 100);
						result += ":" + Math.round(targetVo.resistRandom * 100) + "[" + targetVo.resistRandomIndex + "]";
					}
					if (targetVo.dodgeRandomIndex != -1)
					{
						result += "|M:" + Math.round(targetVo.dodgeProb * 100);
						result += ":" + Math.round(targetVo.dodgeRandom * 100) + "[" + targetVo.dodgeRandomIndex + "]";
					}
					if (targetVo.critRandomIndex != -1)
					{
						result += "|C:" + Math.round(targetVo.critProb * 100);
						result += ":" + Math.round(targetVo.critRandom * 100) + "[" + targetVo.critRandomIndex + "]";
					}
					result += "| ";
				}
				return result;
			}
			else if (record.type == BattleRecordVo.ESCAPE)
			{
				result += "("+ record.initiatorIndex + ")Esc:";
				if (record.escSuccess) result += "succ";
				else result += "fail";
				
				return result;
			}
			else if (record.type == BattleRecordVo.STATUS)
			{
				var subResult:String = "";
				targetVo = record.targetInfo[0];
				
				if (targetVo.hpChange > 0) subResult += "+" + targetVo.hpChange + "Hp;";
				else if (targetVo.hpChange < 0) subResult += "-" + Math.abs(targetVo.hpChange) + "Hp;";
				
				if (targetVo.mpChange > 0) subResult += "+" + targetVo.mpChange + "Mp;";
				else if (targetVo.mpChange < 0) subResult += "-" + Math.abs(targetVo.mpChange) + "Mp;";
				
				if (targetVo.removeStatusList.length > 0)
				{
					for each(statusVo in targetVo.removeStatusList) subResult += "-" + getStatusInLog(statusVo) + ";";
				}
				if (targetVo.leftStatusList.length > 0)
				{
					for each(statusVo in targetVo.leftStatusList) subResult += "~" + getStatusInLog(statusVo) + ";";
				}
				
				if (targetVo.down) subResult += "->Down";
				if (subResult != "")
				{
					result = "(" + record.targetInfo[0].index + ")" + "Status:" + subResult;
					return result;
				}
			}
			return null;
		}
		
		/**
		 * 获得状态的描述
		 * @param	statusVo
		 * @return
		 */
		private function getStatusInLog(statusVo:StatusVo):String
		{
			var result:String = "<";
			result += statusVo.type + ":" + statusVo.value + ":" + statusVo.remain;
			result += ">";
			
			return result;
		}
		
	}
}