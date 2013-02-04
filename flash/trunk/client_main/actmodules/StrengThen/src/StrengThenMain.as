package 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import model.command.StrengThenInitCommand;
	import model.view.StrengThenView;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class StrengThenMain extends ActModuleBase 
	{
		
		public function StrengThenMain():void 
		{
           EventManager.getInstance().addEventListener(StrengThenEvent.STRENGTHENCLOSE,closecomplete);
		}
		
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			super.init(actVo, 1);
			
			
			strengThenInitCommandComplete();
		}
		
		private function strengThenInitCommandComplete():void 
		{		
			 if (DataManager.getInstance().getVar("curTrainRole"))
			 {
			 	 var view:StrengThenView = DisplayManager.uiSprite.addModule("StrengThenView", "model.view.StrengThenView", false, AlginType.CENTER, 120, 25,0,0,"fromCenter",0) as StrengThenView;
			 	 view.setData();					 
			 }
			 else
			 {
				 super.close();
			 }
			 
		
		}
		
		private function closecomplete(e:StrengThenEvent):void 
		{
			super.close();
		}		
		
	}
	
}