package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.RoleWorkManager;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.RoleWorkPointVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkGetAwardCommand extends BaseDataCommand 
	{
		private var id:int;
		public var awards:Array;
		public function RoleWorkGetAwardCommand() 
		{
			takeResult = false;
		}
	
		public function setData(_id:int):void 
		{
			id = _id;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("roleWorkGetAward"),{id:_id});
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			awards = new Array();
			if (objdata.awards)
			{
				for (var i:int = 0; i < objdata.awards.length; i++) 
				{
					var vo:ConditionVo = new ConditionVo();
					vo.id = objdata.awards[i].id;
					vo.type = objdata.awards[i].type;
					vo.num = objdata.awards[i].num;
					awards.push(vo);
				}
			}
			
			var povo:RoleWorkPointVo = RoleWorkManager.getInstance().roleWorkData.getRoleWorkVo(id);
			povo.state = 1;
			
			commandComplete();
			
		}
		
		
		
	}

}