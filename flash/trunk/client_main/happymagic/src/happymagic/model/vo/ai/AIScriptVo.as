package happymagic.model.vo.ai 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class AIScriptVo extends BasicVo
	{
		public var cid:int;
		public var conditions:Vector.<AIConditionVo>; //条件
		public var action:AIActionVo; //动作
		public var target:AITargetVo; //目标
		public var prob:Number = 1; //执行几率
		
		override public function setData(obj:Object):BasicVo 
		{
			for (var key:String in obj)
			{
				if (key == "conditions")
				{
					conditions = new Vector.<AIConditionVo>();
					var arr:Array = obj[key];
					for (var i:int = 0; i < arr.length; i++)
					{
						conditions.push(new AIConditionVo().setData(arr[i]) as AIConditionVo);
					}
				}
				else if (key == "target")
				{
					target = new AITargetVo;
					target.setData(obj[key]);
				}
				else if (key == "action")
				{
					action = new AIActionVo;
					action.setData(obj[key]);
				}
				else if (key == "prob")
				{
					prob = obj[key];
				}
			}
			return this;
		}
	}

}