package happyfish.storyEdit.model.vo 
{
	import happyfish.model.vo.BasicVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class EditStoryVo extends BasicVo 
	{
		public var id:int;
		public var name:String="";
		public var actions:Array;
		
		public var actorList:Array; //演员列表
		
		public var sceneId:int; //剧情所在的场景的ID
		public var endAt:int; //剧情结束时所在的场景
		
		//拿到的奖励
		public var items:String=""; // [[cid,num],[]...]
		public var coin:uint;
		public var gem:uint;
		
		public var nextStoryId:int; //下一个剧情的ID 用于连续触发的剧情
		
		public var taskId:int;
		public var unlockSceneId:int;
		public var actionIds:String="";
		public var npcIds:String="";
		
		public function EditStoryVo() 
		{
			
		}
		
		public function outObj():Object {
			var out:Object = new Object();
			var str:String;
			var arr:Array;
			
			arr = new Array();
			for (var i:int = 0; i < actions.length; i++) 
			{
				arr.push(actions[i].id);
			}
			out.actionIds = arr.length > 0 ? arr.join(",") : "";
			
			arr = new Array();
			for (var j:int = 0; j < actorList.length; j++) 
			{
				arr.push(actorList[j].id);
			}
			out.npcIds = arr.length > 0 ? arr.join(",") : "";
			
			out.id = id;
			out.name = name;
			out.sceneId = sceneId;
			out.endAt = endAt;
			out.items = items;
			out.coin = coin;
			out.gem = gem;
			out.nextStoryId = nextStoryId;
			out.taskId = taskId;
			out.unlockSceneId = unlockSceneId;
			out.unlockSceneId = unlockSceneId;
			
			
			return out;
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			for (var key:String in obj)
			{
				if (key == "actions" && obj.actions)
				{
					actions = new Array();
					for (var i:int = 0; i < obj.actions.length; i++) 
					{
						var item:Object = obj.actions[i];
						actions.push(new EditStoryActionVo().setData(item));
					}
				}else if (key == "actorList" && obj.actorList) {
					actorList = new Array();
					for (var j:int = 0; j < obj.actorList.length; j++) 
					{
						actorList.push(new EditStoryActorVo().setData(obj.actorList[j]));
					}
				}else if(hasOwnProperty(key)) this[key] = obj[key];
			}
			return this;
		}
		
	}

}