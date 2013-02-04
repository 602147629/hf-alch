package  command
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author ZC
	 */
	public class DeletediaryCommand extends BaseDataCommand
	{
		
		public function DeletediaryCommand() 
		{
			
		}
		
		public function setData(_type:int):void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("deletediary"),{type:_type});
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			var temp:Array = new Array();
			//if (objdata.diaryList)
			//{
				//for (var i:int = 0; i < objdata.diaryList.length; i++) 
				//{
					//temp.push(new DiaryVo().setData(objdata.diaryList[i]);
				//}
			//}
			
			commandComplete();
		}
		
	}

}