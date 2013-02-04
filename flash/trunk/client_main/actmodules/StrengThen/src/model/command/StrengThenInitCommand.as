package model.command
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	import manager.StrengThenManager;
	import model.vo.StrengThenVo;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class StrengThenInitCommand extends BaseDataCommand
	{
		
		public function StrengThenInitCommand()
		{
		
		}
		
		public function setData():void
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("strengtheninit"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void
		{
			super.load_complete(e);
			
			StrengThenManager.getInstance().strengThenArr = new Array();
			
			if (objdata.strengthenlist)
			{
			  for (var i:int = 0; i < objdata.strengthenlist.length; i++) 
			  {
				  StrengThenManager.getInstance().strengThenArr.push(new StrengThenVo().setData(objdata.strengthenlist[i]));
			  }
			}
			
			commandComplete();
		}
	}

}