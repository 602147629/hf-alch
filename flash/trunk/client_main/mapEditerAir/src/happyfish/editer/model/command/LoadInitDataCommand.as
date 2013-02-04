package happyfish.editer.model.command 
{
	import flash.events.Event;
	import happyfish.editer.model.vo.MapClassVo;
	import happyfish.model.DataCommandBase;
	
	/**
	 * ...
	 * @author ...
	 */
	public class LoadInitDataCommand extends DataCommandBase 
	{
		
		public function LoadInitDataCommand(_callBack:Function=null) 
		{
			super(_callBack);
		}
		
		public function load(url:String):void {
			createLoad();
			createRequest(url);
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			data.bgswfs = objdata.bgswfs;
			
			commandComplete();
		}
		
	}

}