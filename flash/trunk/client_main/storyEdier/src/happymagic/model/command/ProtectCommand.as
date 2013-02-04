package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.vo.ResultVo;
	/**
	 * ...
	 * @author jj
	 */
	public class ProtectCommand extends BaseDataCommand
	{
		public var safeTime:int;
		
		public function ProtectCommand() 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("openProtect"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			safeTime = objdata["safeTime"];
			
			commandComplete();
		}
	}

}