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
	public class SaveDiyAddCommand extends BaseDataCommand 
	{
		public var decor:DecorVo;
		
		public function SaveDiyAddCommand() 
		{
			
		}
		
		/**
		 * 
		 * @param	cid
		 * @param	x
		 * @param	z
		 * @param	mirror
		 * @param	_decor	提交的那个装饰物的数据,请求完成时要设置ID
		 */
		public function add(cid:int, x:int, z:int, mirror:int, _decor:DecorVo):void {
			
			decor=_decor;
			
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("diyAdd"), { cid:cid, x:x, z:z, mirror:mirror } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			if(objdata.id) decor.id = objdata.id;
			
			commandComplete();
		}
		
	}

}