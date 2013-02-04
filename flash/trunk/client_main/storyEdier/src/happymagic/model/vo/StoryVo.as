package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author slamjj
	 */
	public class StoryVo extends BasicVo 
	{
		public var id:int;
		public var actions:Array;
		
		public var actorList:Array; //演员列表
		
		public var sceneId:int; //剧情所在的场景的ID
		public var endAt:int; //剧情结束时所在的场景
		
		//拿到的奖励
		public var items:Array; // [[cid,num],[]...]
		public var coin:uint;
		public var gem:uint;
		
		public var nextStoryId:int; //下一个剧情的ID 用于连续触发的剧情
		
		override public function setData(obj:Object):BasicVo 
		{
			for (var key:String in obj)
			{
				if (key == "actions")
				{
					actions = new Array();
					for (var i:int = 0; i < obj.actions.length; i++) 
					{
						var item:Object = obj.actions[i];
						actions.push(new StoryActionVo().setData(item));
					}
				}
				else if(hasOwnProperty(key)) this[key] = obj[key];
			}
			return this;
		}
		
	}

}