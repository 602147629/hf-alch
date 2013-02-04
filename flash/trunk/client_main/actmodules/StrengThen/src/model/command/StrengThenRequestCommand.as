package model.command 
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
	public class StrengThenRequestCommand extends BaseDataCommand
	{
		public function StrengThenRequestCommand() 
		{
			
		}

		public function setData(_type:int,_roleId:int):void
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("strengthenRequest"),{type:_type,roleId:_roleId});
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void
		{
			super.load_complete(e);
			
			if (objdata.roleVo)
			{
              var rolevo:RoleVo = new RoleVo();
			  rolevo.sMagAtk = objdata.roleVo.sMagAtk;
			  rolevo.sPhyAtk = objdata.roleVo.sPhyAtk;
			  rolevo.sPhyDef = objdata.roleVo.sPhyDef;
			  rolevo.sMagDef = objdata.roleVo.sMagDef;
			  rolevo.sSpeed = objdata.roleVo.sSpeed;
			  
			  DataManager.getInstance().setVar("newstrengthenvo", rolevo);			  
			}
			

			
			commandComplete();
		}		
		
	}

}
