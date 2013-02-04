package happymagic.battle.model.command 
{
	import com.adobe.serialization.json.JSON;
	import com.brokenfunction.json.encodeJson;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.battle.model.vo.BattleVo;
	import happymagic.model.command.BaseDataCommand;
	
	/**
	 * 战斗初始化Command
	 * @author XiaJunJie
	 */
	public class BattleEndCommand extends BaseDataCommand
	{
		private const API:String = "BattleEnd";
		
		public var confirm:Boolean;
		public var exp:Array;
		
		public var nextScene:int = 0;
		
		public function BattleEndCommand(battleRecord:Object,log:String = "",testUid:int = 0) 
		{
			//takeResult = false;
			
			var url:String;
			
			if (testUid!=0) //测试战斗 使用专门的测试战斗结束接口
			{
				createLoad();
				url = "http://devalchemyrenren.happyfish001.com/zxtest/testendfight";
				createRequest(url, { "rst":encodeJson(battleRecord), "log":log, "uid":testUid } );
				loader.load(request);
				return;
			}
			
			url = InterfaceURLManager.getInstance().getUrl(API);
			if (url)
			{
				createLoad();
				createRequest(url, { "rst":encodeJson(battleRecord), "log":log } );
				
				loader.load(request);
			}
			else
			{
				var testLoader:URLLoader = new URLLoader(new URLRequest("data/BattleEnd.txt"));
				testLoader.addEventListener(Event.COMPLETE, load_complete);
			}
		}
		
		override protected function load_complete(e:Event):void 
		{
			playStory = false;
			
			super.load_complete(e);
			
			confirm = objdata["confirm"] != 0;
			
			if (confirm)
			{
				exp = objdata["exp"];
			}
			
			if (objdata["nextScene"]) nextScene = objdata["nextScene"];
			
			commandComplete();
		}
		
	}
}