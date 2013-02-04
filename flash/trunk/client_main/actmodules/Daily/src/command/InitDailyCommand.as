package command
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.DiaryVo;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class InitDailyCommand extends BaseDataCommand 
	{
		
		public function InitDailyCommand() 
		{
			
		}

		public function setData():void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("initDaily"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			var temp:Array = new Array();
			if (objdata.diaryList)
			{
				for (var i:int = 0; i < objdata.diaryList.length; i++) 
				{
					temp.push(new DiaryVo().setData(objdata.diaryList[i]));
				}
			}
			
		    DataManager.getInstance().diarys = temp;		
			
			commandComplete();
		}
		
	}

}