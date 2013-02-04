package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.manager.SceneTimeManager;
	/**
	 * ...
	 * @author 
	 */
	public class GetTaxesCommand extends BaseDataCommand 
	{
		
		public function GetTaxesCommand() 
		{
			takeResult = false;
		}
		
		public function getTaxes(enemyUid:String):void {
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("collectTax"), { fid:enemyUid } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			DataManager.getInstance().curSceneUser.ownerAwardTime = objdata.nextAwardTime;
			//SceneTimeManager.getInstance().initSceneTime();
			
			
			commandComplete();
		}
		
	}

}