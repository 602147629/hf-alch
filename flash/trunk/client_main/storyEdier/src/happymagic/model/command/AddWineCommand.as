package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.ResultVo;
	/**
	 * ...
	 * @author jj
	 */
	public class AddWineCommand extends BaseDataCommand
	{
		
		public function AddWineCommand(fid:int) 
		{
			takeResult = false;
			
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("addWine"), { fid:fid } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
	}

}