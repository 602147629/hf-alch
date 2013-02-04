package  
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.RoleVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class TigerMachineInitCommond extends BaseDataCommand
	{
		
		public function TigerMachineInitCommond() 
		{
			
		}
		
		public function setData(_fid:int,_buildId:int,_mode:int):void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("TigerMachineInit"),{fid:_fid,buildId:_buildId,mode:_mode});
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			var i:int;
			var vo:RoleVo;
			
			var temp:Array = new Array();
			
			if (objdata.myRoles)
			{
				
				for (i = 0; i < objdata.myRoles.length; i++) 
				{
					vo = new RoleVo();
					vo.setData(objdata.myRoles[i]);
					temp.push(vo);
				}						
			}			
			
			DataManager.getInstance().setVar("myRolesArray", temp);
			
			temp = new Array();
			
			if (objdata.enemyRoles)
			{
				
				for (i = 0; i < objdata.enemyRoles.length; i++) 
				{
					vo = new RoleVo();
					vo.setData(objdata.enemyRoles[i]);
					temp.push(vo);
				}						
			}			
			
			DataManager.getInstance().setVar("enemyRolesArray",temp);			
			
			
			commandComplete();
			
		}		
		
	}

}