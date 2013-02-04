package happymagic.hire.vo 
{
	import com.brokenfunction.json.decodeJson;
	import happyfish.model.vo.BasicVo;
	import happyfish.time.Time;
	import happymagic.model.vo.RoleVo;
	
	/**
	 * 雇用位
	 * @author lite3
	 */
	public class HireVo extends BasicVo 
	{
		private var totalTime:int;
		private var beginTime:int;
		
		public var name:String; 		//名字
		
		public var sex:int;				//性别
		
		public var label:String;		//可以打上某种标签 例如"主角"、"BOSS"、"任务NPC"
		
		//显示--------------
		public var className:String; 	//素材名
		public var faceClass:String;    //头像素材类名
		public var sFaceClass:String;	//战斗时的小头像素材类名
		
		public var pos:int = -1;		//所在的位置的索引 0-17
		public var sizeX:int = 1;		//横向尺寸
		public var sizeZ:int = 1;		//纵向尺寸
		
		//属性--------------
		public var profession:int;		//职业
		public var prop:int;			// 属性
		
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
		public var baseDodge:int; 		//基本闪躲
		public var baseCrit:int; 		//基本暴击
		
		public var skills:Array;					//装备的技能 最多三个
		
		public var dialog:String;
		public var price:int;
		
		public function get remainingTime():int { return Time.getRemainingTime(beginTime, totalTime); }
		public function set remainingTime(value:int):void
		{
			totalTime = value;
			beginTime = Time.getCurTime();
		}
		
		public function hasTime():Boolean { return remainingTime > 0; };
		
		override public function setData(obj:Object):BasicVo 
		{
			if (obj.skills)
			{
				skills = decodeJson(obj.skills);
				delete obj.skills;
			}
			return super.setData(obj);
		}
	}

}