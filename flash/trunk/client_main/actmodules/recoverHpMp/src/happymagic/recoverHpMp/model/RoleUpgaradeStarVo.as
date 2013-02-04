package happymagic.recoverHpMp.model 
{
	import com.brokenfunction.json.decodeJson;
	import happyfish.manager.local.LocaleWords;
	import happyfish.model.vo.BasicVo;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class RoleUpgaradeStarVo extends BasicVo 
	{
		
		public var quality:int;
		public var npcChat:String;
		public var npcClass:String;
		public var conditions:Array;
		
		override public function setData(obj:Object):BasicVo 
		{
			conditions = [];
			if ("needItems" in obj)
			{
				var arr:Array = obj.needItems is String ? decodeJson(obj.needItems) : obj.needItems;
				//if (arr && 2 == arr.length)
				//{
					//conditions.push(new ConditionVo().setData( { type:ConditionType.ITEM, id:arr[0], num:arr[1] } ));
				//}
				
				for (var i:int = arr.length - 1; i >= 0; i--)
				{
					conditions.push(new ConditionVo().setData( { type:ConditionType.ITEM, id:arr[i][0], num:arr[i][1] } ));
				}
			}
			if ("needCoin" in obj && int(obj.needCoin) > 0)
			{
				var num:int = obj.needCoin;
				conditions.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:num} ));
			}
			if ("needLevel" in obj && int(obj.needLevel) > 0)
			{
				num = obj.needLevel;
				conditions.push(new ConditionVo().setData( { type:ConditionType.NONE,
															 id:ConditionId.NeedLevel,
															 num:num,
															 content:LocaleWords.getInstance().getWord("recoverHpMp-needLevel")} ));
			}
			if ("needRoleLevel" in obj && int(obj.needRoleLevel) > 0)
			{
				num = obj.needRoleLevel;
				conditions.push(new ConditionVo().setData( { type:ConditionType.NONE, 
															 id:ConditionId.NeedRoleLevel,
															 num:num, 
															 content:LocaleWords.getInstance().getWord("recoverHpMp-needRoleLevel")} ));
			}
			return super.setData(obj);
		}
		
	}

}