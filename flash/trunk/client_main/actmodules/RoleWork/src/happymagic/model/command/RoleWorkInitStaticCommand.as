package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.RoleWorkManager;
	import happymagic.model.vo.RoleWorkMapVo;
	import happymagic.model.vo.RoleWorkPointClassVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkInitStaticCommand extends BaseDataCommand
	{
		
		public function RoleWorkInitStaticCommand() 
		{
			
		}
	
		public function setData():void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("loadRoleWorkStatic"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
		
			if (objdata.roleWorkMapVo)
			{
				var vo:RoleWorkMapVo = new RoleWorkMapVo();
				vo.bg = objdata.roleWorkMapVo.bg;
				vo.pointClass = new Array();
				for (var i:int = 0; i < objdata.roleWorkMapVo.pointClass.length; i++) 
				{
					var PointClassVo:RoleWorkPointClassVo = new RoleWorkPointClassVo();
					PointClassVo.setData(objdata.roleWorkMapVo.pointClass[i]);
					vo.pointClass.push(PointClassVo);
				}
				
				RoleWorkManager.getInstance().roleWorkData.roleWorkDataStatic = vo; 
			}
			
			commandComplete();
			
		}
		
	}

}