package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.model.vo.ai.AIScriptVo;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class SkillAndItemVo extends BasicVo
	{
		public var cid:int;			//cid
		public var name:String;		//名字
		public var content:String;	//描述
		public var className:String; //图标名
		
		public var effectList:Vector.<EffectVo>;	//所能产生的效果
		
		public var target:int;						//目标 SELF/FRIEND/ENEMY/ALL
		public var range:int;						//范围 CLOSE/FAR
		public var area:int;						//区域 SINGLE/ROW/COL/CROSS/ALL
		
		public var resist:Number; 		//抵抗概率
		public var dodgeAccept:Number;	//闪躲使用率
		public var critAccept:Number;	//暴击使用率
		
		public var needMp:int;						//消耗MP
		
		public var displayScript:Array;				//动画表现脚本 Array<AnimationVo>
		
		public var aiScript:AIScriptVo;				//AI脚本
		
		public var needMats:Object = new Object;
		
		override public function setData(obj:Object):BasicVo 
		{
			var i:int;
			var arr:Array;
			for (var key:String in obj)
			{
				if (key == "effectList")
				{
					effectList = new Vector.<EffectVo>();
					arr = obj[key];
					for (i = 0; i < arr.length; i++)
					{
						var effectVo:EffectVo = new EffectVo().setData(arr[i]) as EffectVo;
						effectList.push(effectVo);
					}
				}
				else if (key == "displayScript")
				{
					displayScript = new Array;
					arr = obj[key];
					var tmpanimation:AnimationVo;
					for (i = 0; i < arr.length; i++)
					{
						var arr2:Array = arr[i][0] as Array;
						if (arr2 == null)
						{
							tmpanimation = new AnimationVo();
							tmpanimation.setData(arr[i]);
							displayScript.push(tmpanimation);
							getMat(tmpanimation);
						}
						else
						{
							arr2 = arr[i];
							var arr3:Array = new Array;
							for (var j:int = 0; j < arr2.length; j++)
							{
								tmpanimation = new AnimationVo();
								tmpanimation.setData(arr[i][j]);
								arr3.push(tmpanimation);
								getMat(tmpanimation);
							}
							displayScript.push(arr3);
						}
					}
				}
				else if (key == "aiScript" && obj[key]!=null)
				{
					aiScript = new AIScriptVo;
					aiScript.setData(obj[key]);
					if(aiScript.action) aiScript.action.skillAndItemVo = this;
				}
				else if (hasOwnProperty(key)) this[key] = obj[key];
			}
			return this;
		}
		
		private function getMat(animationVo:AnimationVo):void
		{
			switch(animationVo.type)
			{
				case AnimationVo.COVER:
				case AnimationVo.BOTTOM:
				case AnimationVo.FLYING:
					needMats[animationVo.className] = true;
				break;
			}
		}
		
		//常量-------------------------
		public static const SELF:int = 3; //自己
		public static const FRIEND:int = 2; //我方
		public static const ENEMY:int = 1; //敌方
		public static const DOWNFRIEND:int = 4; //我方死亡的
		
		public static const CLOSE:int = 1; //近战
		public static const FAR:int = 2; //远攻
		
		public static const SINGLE:int = 1; //单体
		public static const ROW:int = 2; //行
		public static const COL:int = 3; //列
		public static const CROSS:int = 4; //十字
		
		public static const ALL:int = 5; //全体
	}

}