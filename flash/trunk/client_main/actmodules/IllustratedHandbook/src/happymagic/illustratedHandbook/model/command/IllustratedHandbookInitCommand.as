package happymagic.illustratedHandbook.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.model.DataCommandBase;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.IllustrationsVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class IllustratedHandbookInitCommand extends BaseDataCommand
	{
		
		public function IllustratedHandbookInitCommand() 
		{
			
		}
		
		public function setData():void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("IllustratedHandbookInit"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			var temp:Array = new Array();
			
			if (objdata.illustrations)
			{
				
				for (var i:int = 0; i < objdata.illustrationsVo.length; i++) 
				{
					var vo:IllustrationsVo = new IllustrationsVo();
					vo.setData(objdata.illustrationsVo[i]);
					vo.base = DataManager.getInstance().illustratedData.getIllustrationsClassVo(vo.cid);
					temp.push(vo);
				}						
			}			
			
			DataManager.getInstance().illustratedData.illustratedHandbookInit = temp;
			
			commandComplete();
			
		}
		
	}

}