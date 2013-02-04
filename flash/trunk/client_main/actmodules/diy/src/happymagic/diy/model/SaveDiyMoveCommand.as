package happymagic.diy.model 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.DecorVo;
	
	/**
	 * ...
	 * @author 
	 */
	public class SaveDiyMoveCommand extends BaseDataCommand 
	{
		public var decorVo:DecorVo;
		public function SaveDiyMoveCommand(_callBack:Function=null) 
		{
			super(_callBack);
		}
		
		public function save(id:String,x:int,z:int,mirror:int):void {
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("diyEdit"), { id:id, x:x, z:z, mirror:mirror } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}