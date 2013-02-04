package happymagic.display.view.ui.personMsg 
{
	import happyfish.scene.world.grid.IsoItem;
	
	/**
	 * ...
	 * @author jj
	 */

	public class PersonMsgManager 
	{
		private var msgs:Object = new Object();
		private var storyMsgs:Object = new Object;
		public function PersonMsgManager(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "PersonMsgManager"+"单例" );
			}
		}
		
		public static function getInstance():PersonMsgManager
		{
			if (instance == null)
			{
				instance = new PersonMsgManager( new Private() );
			}
			return instance;
		}
		
		public function addMsg(target:IsoItem,str:String,time:uint=2000,_callback:Function=null):void {
			if (msgs[target.view.name]) 
			{
				(msgs[target.view.name] as PersonMsgView).setData(str,time,_callback);
			}else {
				msgs[target.view.name] = new PersonMsgView(target,str,time,_callback);
			}
		}
		
		/**
		 * 关闭并移除指定对话框
		 * @param	name
		 * @param	hasClose	是否已关闭对话框
		 */
		public function delMsg(name:String,hasClose:Boolean=true):void 
		{
			var msgView:PersonMsgView = msgs[name] as PersonMsgView;
			if (msgView) 
			{
				delete msgs[name];
				if(!hasClose) msgView.closeMe();
			}
		}
		
		public function addStoryMsg(target:IsoItem, npcHead:String, npcName:String, str:String, time:uint = 2000, _callback:Function = null):void
		{
			if (storyMsgs[target.view.name]) 
			{
				(storyMsgs[target.view.name] as StoryPersonMsgView).setData(npcHead, npcName, str, time, _callback);
			}else {
				storyMsgs[target.view.name] = new StoryPersonMsgView(target, npcHead, npcName, str, time, _callback);
			}
		}
		
		public function delStoryMsg(name:String,hasClose:Boolean=true):void 
		{
			var msgView:StoryPersonMsgView = storyMsgs[name] as StoryPersonMsgView;
			if (msgView) 
			{
				delete storyMsgs[name];
				if(!hasClose) msgView.closeMe();
			}
		}
		
		private static var instance:PersonMsgManager;
		
	}
	
}
class Private {}