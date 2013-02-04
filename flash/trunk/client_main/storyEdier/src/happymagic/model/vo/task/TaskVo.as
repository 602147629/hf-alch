package happymagic.model.vo.task 
{
	import com.brokenfunction.json.decodeJson;
	import happyfish.model.vo.BasicVo;
	import happymagic.model.vo.ConditionVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class TaskVo extends BasicVo
	{
		
		public var id:int;
		public var name:String;
		public var index:int;
		public var content:String;
		public var conditions:Array;
		public var awards:Array;
		public var state:int;
		
		
		public var guide:int;
		public var sceneId:int;
		public var nowFinishPrice:int;
		
		public var npcId:int;
		public var npcName:String;
		public var npcFaceClass:String;
		public var npcChatAccept:String;
		public var npcChatComplete:String;
		
		public function get type():int { return TaskType.getType(id); }
		
		public function hasAward():Boolean { return awards && awards.length > 0; }
		
		public function isFinish():Boolean
		{
			var len:int = conditions ? conditions.length : 0;
			for (var i:int = 0; i < len; i++)
			{
				if (!ConditionVo(conditions[i]).isFinish()) return false;
			}
			return true;
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			if ("npcChat" in obj)
			{
				var arr:Array = obj.npcChat.split("|");
				npcChatAccept = arr[0];
				npcChatComplete = arr[1];
			}
			if ("conditions" in obj)
			{
				conditions = (obj.conditions is String) ? decodeJson(obj.conditions) : obj.conditions;
				var len:int = conditions ? conditions.length : 0;
				if (len > 0 && !(conditions[0] is ConditionVo))
				{
					for (var i:int = 0; i < len; i++)
					{
						conditions[i] = new ConditionVo().setData(conditions[i]);
					}
				}
				delete obj.conditions;
			}
			if ("awards" in obj)
			{
				awards = (obj.awards is String) ? decodeJson(obj.awards) : obj.awards;
				len = awards ? awards.length : 0;
				if (len > 0 && !(awards[0] is ConditionVo))
				{
					for (i = 0; i < len; i++)
					{
						awards[i] = new ConditionVo().setData(awards[i]);
					}
				}
				delete obj.awards;
			}
			return super.setData(obj);
		}
		
	}

}