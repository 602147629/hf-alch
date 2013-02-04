package happymagic.illustratedHandbook.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.model.DataCommandBase;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.classVo.IllustrationsClassVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class IllustratedHandbookInitStaticCommand extends BaseDataCommand
	{
		
		public function IllustratedHandbookInitStaticCommand() 
		{
			
		}
		
		public function setData():void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("IllustratedHandbookInitStatic"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);

			var temp:Array = new Array();
			
			if (objdata.illustrationsClass)
			{
				
				for (var i:int = 0; i < objdata.illustrationsClassVo.length; i++) 
				{
					temp.push(new IllustrationsClassVo().setData(objdata.illustrationsClassVo[i]));
				}						
			}			
			
			DataManager.getInstance().illustratedData.illustratedHandbookStatic = temp;
			
			commandComplete();
			
		}
		
	}

}