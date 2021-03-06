package happymagic.model.command 
{
	import com.adobe.serialization.json.JSON;
	import flash.events.Event;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.MagicUrlLoader;
	/**
	 * ...
	 * @author jj
	 */
	public class LoadFriendsCommand extends BaseDataCommand
	{
		
		public function LoadFriendsCommand() 
		{
			
		}
		
		public function loadFriend():void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("loadFriend"));
			loader.load(request);
			//dispatchEvent(new Event(Event.COMPLETE));
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}