package happymagic.hire.commands 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.hire.data.HireData;
	import happymagic.hire.vo.HireVo;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class CompleteHireCommand extends BaseDataCommand 
	{
		public var npcId:int;
		public var pos:int;
		public var hireVo:HireVo;
		private var callbackFun:Function;
		
		private var isFree:Boolean;
		
		public function completeHire(npcId:int, pos:int, callbackFun:Function):void
		{
			this.npcId = npcId;
			this.pos = pos;
			this.callbackFun = callbackFun;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("completeHire"), { npcId:npcId, pos:pos } );
			loadData();
		}
		
		public function freeCompleteHire(npcId:int, pos:int, callbackFun:Function):void
		{
			isFree = true;
			this.npcId = npcId;
			this.pos = pos;
			this.callbackFun = callbackFun;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("completeHire"), { npcId:npcId, pos:pos, wine:1 } );
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			if (result.isSuccess)
			{
				var vo:HireVo = HireData.instance.getHire(npcId, pos);
				vo.remainingTime = 0;
				if(isFree) DataManager.getInstance().currentUser.hireHelpUsed = 1;
			}
			
			callbackFun(result.isSuccess, npcId, pos, vo);
		}
		
	}

}