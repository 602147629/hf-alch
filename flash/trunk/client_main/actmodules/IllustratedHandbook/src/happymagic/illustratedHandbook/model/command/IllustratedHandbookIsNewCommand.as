package happymagic.illustratedHandbook.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.model.DataCommandBase;
	import happymagic.display.view.mainMenu.MainMenuView;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author ZC
	 */
	public class IllustratedHandbookIsNewCommand extends BaseDataCommand
	{
		
		public function IllustratedHandbookIsNewCommand() 
		{
			
		}
		
		public function setData(_id:int):void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("readillustration"),{id:_id});
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			


			
			commandComplete();
			
		}
		
	}

}