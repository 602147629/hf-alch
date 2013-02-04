package 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happymagic.event.RoleWorkEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.RoleWorkManager;
	import happymagic.model.command.RoleWorkInitCommand;
	import happymagic.model.command.RoleWorkInitStaticCommand;
	import happymagic.view.RoleWorkView;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkMain extends ActModuleBase 
	{
		private var timer:Timer = new Timer(1000);
		public function RoleWorkMain():void 
		{
           EventManager.getInstance().addEventListener(RoleWorkEvent.ROLEWORKMOUDLECLOSE, closecomplete);
		   timer.addEventListener(TimerEvent.TIMER, timercomplete);
		}
		
		private function timercomplete(e:TimerEvent):void 
		{
			
		}
		
		private function closecomplete(e:RoleWorkEvent):void 
		{
			super.close();
		}
		
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			super.init(actVo, 1);
			
			//new test();
				//
			//showView();
			
			if (!RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic)
			{
				var initstaticcommand:RoleWorkInitStaticCommand = new RoleWorkInitStaticCommand();
				initstaticcommand.setData();
				initstaticcommand.addEventListener(Event.COMPLETE,initstaticCommondComplete);
			}
			else
			{
				var initcommand:RoleWorkInitCommand = new RoleWorkInitCommand();
				initcommand.setData();
				initcommand.addEventListener(Event.COMPLETE,initCommondComplete);
			}
			
		}
		
		private function showView():void 
		{
			 timer.start();
			 var roleworkView:RoleWorkView = DisplayManager.uiSprite.addModule("RoleWorkView", "happymagic.view.RoleWorkView", false, AlginType.CENTER, 20, 0) as RoleWorkView;
			 roleworkView.setData();
			 DisplayManager.uiSprite.setBg(roleworkView);				
		}
		
		private function initCommondComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, initCommondComplete);	
			
            showView();		
		}
		
		private function initstaticCommondComplete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, initstaticCommondComplete);
			var initcommand:RoleWorkInitCommand = new RoleWorkInitCommand();
			initcommand.setData();
			initcommand.addEventListener(Event.COMPLETE,initCommondComplete);			
		}
		
	}
	
}