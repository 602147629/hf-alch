package model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author ZC
	 */
	public class StrengThenSaveAttributeCommand extends BaseDataCommand
	{
		
		public function StrengThenSaveAttributeCommand() 
		{
			
		}

		public function setData(_roleId:int):void
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("saveAttribute"),{roleId:_roleId});
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void
		{
			super.load_complete(e);
			
			if (objdata.result.status == 1)
			{

			}
			
			commandComplete();
		}	
		
	}

}