package 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happymagic.display.view.mainMenu.MainMenuView;
	import happymagic.display.view.RightCenterMenuView;
	import happymagic.display.view.ui.ItemIconView;
	import happymagic.illustratedHandbook.event.IllustratedHandbookEvent;
	import happymagic.illustratedHandbook.model.command.IllustratedHandbookInitCommand;
	import happymagic.illustratedHandbook.model.command.IllustratedHandbookInitStaticCommand;
	import happymagic.illustratedHandbook.model.control.QuickJoinControl;
	import happymagic.illustratedHandbook.model.view.IllustratedHandbookHomepageView;
	import happymagic.illustratedHandbook.model.view.IllustratedHandbookView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.classVo.MixClassVo;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class IllustratedHandbookMain  extends ActModuleBase
	{
		private var vo:ActVo;
		public function IllustratedHandbookMain():void 
		{
           EventManager.getInstance().addEventListener(IllustratedHandbookEvent.ILLUSTRATEMODULECLOSE,closecomplete);			
		}
		
		private function closecomplete(e:IllustratedHandbookEvent):void 
		{
			super.close();
		}
		
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, init);
			
			// entry point
			super.init(actVo, 1);
			vo = actVo;		
			
			if (!DataManager.getInstance().illustratedData.illustratedHandbookInit)
			{
				var illustratedHandbookInitCommand:IllustratedHandbookInitCommand = new IllustratedHandbookInitCommand();
				illustratedHandbookInitCommand.setData();
				illustratedHandbookInitCommand.addEventListener(Event.COMPLETE, illustratedHandbookInitCommandComplete);
			}else
			{
				illustratedHandbookInitCommandComplete();
			}
		}
		
		private function illustratedHandbookInitCommandComplete(e:Event = null ):void 
		{
			if (e)
			{
			   e.target.removeEventListener(Event.COMPLETE, illustratedHandbookInitCommandComplete);				
			}

			
			if (!DataManager.getInstance().illustratedData.illustratedHandbookStatic)
			{
				var illustratedHandbookInitStaticCommand:IllustratedHandbookInitStaticCommand = new IllustratedHandbookInitStaticCommand();
				illustratedHandbookInitStaticCommand.setData();
				illustratedHandbookInitStaticCommand.addEventListener(Event.COMPLETE, illustratedHandbookInitStaticCommandComplete);
			}		
			else
			{
				illustratedHandbookInitStaticCommandComplete();
			}
		}
		
		private function illustratedHandbookInitStaticCommandComplete(e:Event = null):void 
		{
			if (e)
			{
			    e.target.removeEventListener(Event.COMPLETE, illustratedHandbookInitStaticCommandComplete);						
			}
	
			if (vo.moduleData)
			{
				
			    if (vo.moduleData.itemCid)
				{
					var itemvo:BaseItemClassVo = DataManager.getInstance().itemData.getItemClass(vo.moduleData.itemCid);
					//var name:String = DataManager.getInstance().mixData.getMixClass(vo.moduleData.mixCid).name;
				}
				new QuickJoinControl(itemvo.name);	
				vo.moduleData = null;
			}
			else
			{
			   var illustratedHandbookView:IllustratedHandbookView = DisplayManager.uiSprite.addModule(IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKVIEW, IllustratedHandbookDictionary.MODULE_ILLUSTRATEDHANDBOOKVIEW_CLASS, false, AlginType.CENTER, 0, -10) as IllustratedHandbookView;
			   illustratedHandbookView.setData();
			   DisplayManager.uiSprite.setBg(illustratedHandbookView);						
			}
			
	
			
		}
		

		
	}
	
}