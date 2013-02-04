package  
{
	import flash.events.Event;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author ZC
	 */
	public class TigerMachineReadyCommond extends BaseDataCommand
	{
		
		public function TigerMachineReadyCommond() 
		{
			
		}
		
		public function setData(_fid:int,_buildId:int,_mode:int):void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("TigerMachineReady"),{fid:_fid,buildId:_buildId,mode:_mode});
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			var i:int;
			var temp:Array = new Array();
			
			if (objdata.myRolesId)
			{    
				for (i = 0; i < objdata.myRolesId.length; i++ )
				{
					temp.push(objdata.myRolesId[i]);			
				}
			}	
			
			DataManager.getInstance().setVar("myRolesId", temp);

			temp = new Array();
			
			if (objdata.enemyRolesId)
			{    
				for (i = 0; i < objdata.enemyRolesId.length; i++ )
				{
					temp.push(objdata.enemyRolesId[i]);			
				}
			}	
			
			DataManager.getInstance().setVar("enemyRolesId", temp);					
			
            DataManager.getInstance().setVar("battleInitData", objdata);					
			
			commandComplete();
			
		}		
		
	}

}