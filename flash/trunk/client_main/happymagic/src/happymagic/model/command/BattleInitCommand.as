package happymagic.model.command 
{
	import com.adobe.serialization.json.JSON;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	
	/**
	 * 战斗初始化Command
	 * @author XiaJunJie
	 */
	public class BattleInitCommand extends BaseDataCommand
	{
		private const API:String = "BattleInit";
		
		public function BattleInitCommand(monsterId:int) 
		{
			takeResult = false;
			
			var url:String = InterfaceURLManager.getInstance().getUrl(API);
			
			if (url)
			{
				createLoad();
				createRequest(url, { monsterId:monsterId } );
				
				loader.load(request);
			}
			else
			{
				var testLoader:URLLoader = new URLLoader(new URLRequest("data/BattleInit.txt"));
				testLoader.addEventListener(Event.COMPLETE, load_complete);
			}
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}
}