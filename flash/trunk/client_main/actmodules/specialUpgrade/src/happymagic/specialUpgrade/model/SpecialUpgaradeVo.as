package happymagic.specialUpgrade.model 
{
	import com.brokenfunction.json.decodeJson;
	import happyfish.model.vo.BasicVo;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class SpecialUpgaradeVo extends BasicVo 
	{
		
		public var id:int;
		public var level:int;
		public var needLevel:int;
		public var npcClass:String;
		public var npcChat:String;
		public var content:String;
		public var conditions:Array;
		
		override public function setData(obj:Object):BasicVo 
		{
			conditions = [];
			if ("needItems" in obj)
			{
				var arr:Array = obj.needItems is String ? decodeJson(obj.needItems) : obj.needItems;
				for (var i:int = arr.length - 1; i >= 0; i--)
				{
					conditions.push(new ConditionVo().setData( { type:ConditionType.ITEM, id:arr[i][0], num:arr[i][1] } ));
				}
			}
			if ("needCoin" in obj && obj.needCoin)
			{
				conditions.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:obj.needCoin } ));
				delete obj.needCoin;
			}
			super.setData(obj);
			content &&= content.replace(/\\n/g, "\n");
			return this;
		}
		
	}

}