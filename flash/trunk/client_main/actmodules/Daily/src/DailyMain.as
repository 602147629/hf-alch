package 
{
	import command.InitDailyCommand;
	import flash.events.Event;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happymagic.manager.DisplayManager;
	import view.DailyView;
	/**
	 * ...
	 * @author ZC
	 */
	public class DailyMain extends ActModuleBase 
	{
		
		public function DailyMain():void 
		{
			EventManager.getInstance().addEventListener(DailyEvent.DAILYCLOSE,closecomplete);			
		}

		private function closecomplete(e:DailyEvent):void 
		{
			super.close();
		}		
		
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
           var initcommmand:InitDailyCommand = new InitDailyCommand();
	       initcommmand.setData();
		   initcommmand.addEventListener(Event.COMPLETE, commandcomplete);
		   super.init(actVo, 1);
		   
		   //new test();
		   
			//var dailyView:DailyView = DisplayManager.uiSprite.addModule("DailyView", "view.DailyView", false, AlginType.CENTER, 20, -10) as DailyView;
			//dailyView.setData();
			//DisplayManager.uiSprite.setBg(dailyView);
			
		}
		
		private function commandcomplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, commandcomplete);
			
			var dailyView:DailyView = DisplayManager.uiSprite.addModule("DailyView", "view.DailyView", false, AlginType.CENTER, 20, -10) as DailyView;
			dailyView.setData();
			DisplayManager.uiSprite.setBg(dailyView);
		}
		
	}
	
}