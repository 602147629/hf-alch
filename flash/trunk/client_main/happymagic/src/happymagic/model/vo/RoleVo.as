package happymagic.model.vo 
{
	import com.brokenfunction.json.decodeJson;
	import happyfish.model.vo.BasicVo;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ai.AIScriptVo;
	/**
	 * 参战角色VO
	 * @author XiaJunJie
	 */
	public class RoleVo extends BasicVo
	{
		//基本--------------
		public var id:int; 				//ID
		public var name:String; 		//名字
		//public var content:String; 		//介绍
		
		public var sex:int;				//性别
		
		public var label:String;		//可以打上某种标签 例如"主角"、"BOSS"、"任务NPC"
		
		//显示--------------
		public var className:String; 	//素材类名
		public var faceClass:String; //头像素材类名
		public var sFaceClass:String;	//战斗时的小头像素材类名
		
		public var pos:int = -1;				//所在的位置的索引 0-17
		public var sizeX:int = 1;		//横向尺寸
		public var sizeZ:int = 1;		//纵向尺寸
		
		//属性--------------
		public var profession:int;		//职业
		public var prop:int;			// 属性  1:水 2:火 3:风
		
		public var hp:int; 				//生命
		public var maxHp:int; 			//最大生命
		public var mp:int; 				//法力
		public var maxMp:int; 			//最大法力
		
		public var exp:int;
		public var maxExp:int;
		
		public var level:int;			//角色等级
		public var quality:int;			//角色质量
		
		public var speed:int; 			//速度
		public var phyAtk:int;			//基础物理攻击力
		public var phyDef:int;			//基础物理防御力
		public var magAtk:int;			//基础魔法攻击力
		public var magDef:int;			//基础魔法防御力
		public var physicAttack:int; 	//物理攻击力
		public var physicDefense:int; 	//物理防御力
		public var magicAttack:int; 	//魔法攻击力
		public var magicDefense:int; 	//魔法防御力
		public var dodge:int; 			//闪躲
		public var baseDodge:int; 		//基本闪躲
		public var crit:int; 			//暴击
		public var baseCrit:int; 		//基本暴击
		
		public var occCdTime:int;		// 可出战PVP的时间,时间戳
		
		public var statusList:Vector.<StatusVo>;	//状态列表(BUFF和DEBUFF)
		public var skills:Array;					//装备的技能 最多三个
		public var equipments:Array;				//装备列表[[id,cid],[id,cid]]
		
		//其他---------------
		public var items:Vector.<ConditionVo>;			//掉落的物品
		
		public var aiScriptId:Array;					//AI脚本的编号数组
		public var aiScript:Vector.<AIScriptVo>;		//AI脚本 根据aiScriptId获得
		
		//强化属性-----------
		public var sPhyAtk:uint;//攻击
		public var sPhyDef:uint;//防御
		public var sMagAtk:uint;//魔攻
		public var sMagDef:uint;//魔防
		public var sSpeed:uint;//速度
		
		//是否在打工
		public var work:Boolean;
		
		/**
		 * 设置数据
		 */
		override public function setData(obj:Object):BasicVo 
		{
			var arr:Array;
			var i:int;
			
			if (("skills" in obj) && obj.skills is String)
			{
				skills = decodeJson(obj.skills);
				delete obj.skills;
			}
			
			for (var key:String in obj)
			{	
				if (key == "statusList")
				{
					statusList = new Vector.<StatusVo>();
					
					arr = obj[key];
					for (i = 0; i < arr.length; i++)
					{
						var statusVo:StatusVo = new StatusVo;
						statusVo.setData(arr[i]);
						statusList.push(statusVo);
					}
					delete obj.statusList;
				}
				else if (key == "aiScript")
				{
					if(!aiScript) aiScript = new Vector.<AIScriptVo>();
					arr = obj[key];
					for (i = 0; i < arr.length; i++)
					{
						var aiScriptVo:AIScriptVo = new AIScriptVo;
						aiScriptVo.setData(arr[i]);
						aiScript.push(aiScriptVo);
					}
					delete obj.aiScript;
				}
				else if (key == "aiScriptId")
				{
					aiScriptId = obj[key];
					
					if(!aiScript) aiScript = new Vector.<AIScriptVo>();
					arr = obj[key];
					for (i = 0; i < arr.length; i++)
					{
						aiScript.push(DataManager.getInstance().getAiScript(arr[i]));
					}
					delete obj.aiScriptId;
				}
				else if (key == "items")
				{
					items = new Vector.<ConditionVo>();
					arr = obj[key];
					if (arr == null) continue;
					for (i = 0; i < arr.length; i++)
					{
						var conditionVo:ConditionVo = new ConditionVo;
						conditionVo.setData(arr[i]);
						items.push(conditionVo);
					}
					delete obj.items;
				}
			}
			
			super.setData(obj);
			
			physicAttack = phyAtk;
			physicDefense = phyDef;
			magicAttack = magAtk;
			magicDefense = magDef;
			
			if (!items) items = new Vector.<ConditionVo>();
			
			return this;
		}
		
		public static const MAIN_ROLE:String = "MR";
		public static const BOSS:String = "BOSS";
		public static const TEMP:String = "TEMP";
	}

}